<?php

class GoogleProvider {
    protected $db;
    protected $client;
    protected $userID = 0;
    protected $dbID = null;
    protected $googleID = null;
    protected $code = null;

    public function __construct(Database $db, $userID, Google_Client $googleClient = null) {
        $this->db = $db;
        $this->userID = $userID;
        $this->client = $googleClient;

        if($this->client) {
            $this->client->setClientId('971255903327-u70mlh2duncr4sent7hc8f0j9s8lebld.apps.googleusercontent.com');
            $this->client->setClientSecret('q3rjhm8CSRPvExX46KcPZu3L');
            $this->client->setRedirectUri('http://localhost');
            //$this->client->setScopes(array('email', 'profile'));
        }
        else {
            echo "client not given";
        }

        $this->getDataFromDB();
    }

    public function setUserID($id) {
        $this->userID = $id;
    }

    public function setGoogleID($googleID) {
        $this->googleID = $googleID;
    }

    public function getDataFromDB() {
        // Checkes if user already authenticated with google
        if($this->userID == 0)
            return false;

        $checkQuery = $this->db->prepare("SELECT * FROM google_users WHERE user_id = :userID LIMIT 1;");
        $checkQuery->bindParam(":userID", $this->userID);
        $checkQuery->execute();

        $checkData = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if($checkData) {
            $this->dbID = $checkData['id'];
            $this->googleID = $checkData['google_id'];
            $this->code = $checkData['code'];
            return true;
        }
        else
            return false;
    }

    public function getAccessToken($code = null) {
        if($code) {
            $this->code = $code;

            $this->client->authenticate($this->code);
        }

        return json_decode($this->client->getAccessToken(), true)['access_token'];
    }

    public function getPayload() {
        return $this->client->verifyIdToken()->getAttributes()['payload'];
    }

    public function isAuthenticated() {
        return ($this->dbID !== null ? 1 : 0);
    }

    public function retrieveData() {
        $userData = json_decode(
            file_get_contents(
                "https://www.googleapis.com/userinfo/v2/me?access_token=".$this->getAccessToken()
                ), true
        );
        $this->googleID = $userData['id'];
        return $userData;
    }

    public function save() {
        if($this->isAuthenticated()) {
            // Update a row
            $updateQuery = $this->db->prepare("UPDATE google_users SET google_id = :googleid, code = :accessToken WHERE id = :id LIMIT 1;");
            $updateQuery->bindParam(":googleid", $this->googleID, PDO::PARAM_INT);
            $updateQuery->bindParam(":accessToken", $this->getAccessToken());
            $updateQuery->bindParam(":id", $this->dbID, PDO::PARAM_INT);
            $updateQuery->execute();
        }
        else {
            // Create a new row
            $insertQuery = $this->db->prepare("INSERT INTO google_users (user_id, google_id, code) VALUES (:userid, :googleid, :accessToken);");
            $insertQuery->bindParam(":userid", $this->userID, PDO::PARAM_INT);
            $insertQuery->bindParam(":googleid", $this->googleID, PDO::PARAM_INT);
            $insertQuery->bindParam(":accessToken", $this->getAccessToken());
            $insertQuery->execute();
            $this->dbID = $this->db->lastInsertId();
        }
    }
}

?>
