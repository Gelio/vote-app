<?php

use \Firebase\JWT\JWT;

class User
{
    protected $db;
    protected $jwt;
    protected $id;
    protected $email;
    protected $username;
    protected $admin = false;
    protected $auth; // array containing different providers

    protected $authenticated = false;

    // $arg1 - either a JWT (if password not provided) or email if password is provided
    public function __construct(Database $db, $arg1 = "", $password = "") {
        // For new users
        $this->db = $db;

        if(!empty($arg1)) {
            if(!empty($password)) {
                $this->email = $arg1;
                $this->fetchUser($password);
            }
            else {
                global $secretKey;
                // JWT
                $this->jwt = $arg1;

                $decodedJWT = JWT::decode($this->jwt, $secretKey, array('HS256'));

                if($decodedJWT) {
                    $decodedArray = (array) $decodedJWT;
                    $this->id = $decodedArray['id'];
                    $this->fetchUser($this->id);
                }
                else
                    return false;
            }
        }
    }


    public function fetchUser($arg1) {
        if(is_numeric($arg1)) {
            // Via ID
            $id = $arg1;

            $findQuery = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1;");
            $findQuery->bindParam(":id", $id, PDO::PARAM_INT);
            $findQuery->execute();

            $findData = $findQuery->fetch(PDO::FETCH_ASSOC);

            if($findData) {
                $this->authenticated = true;
                $this->id = $findData['id'];
                $this->email = $findData['email'];
                $this->username = $findData['username'];
                $this->admin = $findData['admin'];

                $this->auth = [];

                $this->fetchProviders();
                return true;
            }
            else {
                return false;
            }
        }
        else {
            // Via password
            $password = $arg1;

            $findQuery = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1;");
            $findQuery->bindParam(":email", $this->email, PDO::PARAM_STR);
            // using hashing
            //$findQuery->bindParam(":password", $password, PDO::PARAM_STR);
            $findQuery->execute();

            $findData = $findQuery->fetch(PDO::FETCH_ASSOC);

            if($findData && $this->verifyPassword($password, $findData['password'])) {
                $this->authenticated = true;
                $this->id = $findData['id'];
                $this->username = $findData['username'];
                $this->admin = $findData['admin'];

                $this->auth = [];
                $this->fetchProviders();

                return true;
            }
            else {
                return false;
            }
        }
    }

    public function verifyPassword($password, $hash) {
        if(password_verify($password, $hash)) {
            if(password_needs_rehash($hash, PASSWORD_DEFAULT) && $this->id) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateQuery = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id LIMIT 1;");
                $updateQuery->bindParam(":password", $newHash);
                $updateQuery->bindParam(":id", $this->id, PDO::PARAM_INT);
                $updateQuery->execute();
            }

            return true;
        }
        return false;
    }

    public function fetchProviders() {
        $googleClient = new Google_Client;
        $facebookProvider = new FacebookProvider($this->db, $this->id);
        $facebookProvider->searchByUserID();

        $this->auth = [
            'google' => new GoogleProvider($this->db, $this->id, $googleClient),
            'facebook' => $facebookProvider
        ];

    }

    public function isAuthenticated() {
        return $this->authenticated;
    }

    public function newUser($email, $username, $password) {
        if(empty($email))
            $email = "";

        $this->email = $email;
        $this->username = $username;

        $password = password_hash($password, PASSWORD_DEFAULT);

        $addQuery = $this->db->prepare("INSERT INTO users (email, username, password, admin) VALUES (:email, :username, :password, :admin);");
        $addQuery->bindParam(":email", $this->email, PDO::PARAM_STR);
        $addQuery->bindParam(":username", $this->username);
        $addQuery->bindParam(":password", $password);
        $addQuery->bindParam(":admin", $this->admin, PDO::PARAM_BOOL);
        $addQuery->execute();

        if($addQuery->errorCode() === '00000') {
            $this->fetchUser($this->db->lastInsertId());
            return true;
        }
        else
            return $addQuery->errorCode();
    }

    public function getJWT($encoded = true) {
        global $expireTime, $secretKey;
        $data = [
            'iss' => 'localhost',
            'exp' => time()+$expireTime,
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'admin' => $this->admin
        ];
        // TODO: when adding more providers include them in payload, for instance: authGoogle => false
        $data['authGoogle'] = $this->auth['google']->isAuthenticated();
        $data['authFacebook'] = $this->auth['facebook']->isAuthenticated();

        $jwt = JWT::encode($data, $secretKey, 'HS256');
        $this->jwt = $jwt;

        if($encoded)
            return $this->jwt;
        else
            return $data;
    }

    public function getID() {
        return $this->id;
    }
}

?>
