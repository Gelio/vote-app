<?php
/*
========================================================================================
					Broken version, not using classes, use external-auth2.php
========================================================================================
*/

require_once("../../vendor/autoload.php");
require_once("config.inc.php");
require_once('classes/GoogleAuth.php');
use \Firebase\JWT\JWT;


require_once('cors-headers.inc.php');

$output = fopen("output.txt", "w");
if(isset($_GET['provider']) && !empty($_GET['provider'])) {
    $provider = $_GET['provider'];
    fwrite($output, $provider);

    $headerBody = file_get_contents("php://input");

    if(is_string($headerBody)) {
        //fwrite($output, "\n\n".$headerBody);

        $parsedBody = json_decode($headerBody, true);

        if(isset($parsedBody['code']) && !empty($parsedBody['code'])) {
            /* Query the DB, check if user's created in provider's specific table
                If yes: authenticate with that info
                If no: check if user is currently authenticated (Bearer)
                    If yes: add info to that specific provider's table
                    If no: create a user with info from the API and add it to provider's table
            */
            $googleClient = new Google_Client;
            $googleAuth = new GoogleAuth($googleClient);
            $accessToken = json_decode($googleAuth->getAccessToken($parsedBody['code']), true);

            $userData = json_decode(file_get_contents("https://www.googleapis.com/userinfo/v2/me?access_token=".$accessToken['access_token']), true);
            //fwrite($output, "\nsuccess".print_r($userData, true));

            try {
                $dbh = new PDO($dbn, $database['user'], $database['password']);
            }
            catch (PDOException $e) {
                fwrite($output, "Connection failed: ".$e->getMessage());
                header("HTTP/1.1 500 Internal Server Error");
            }

            $checkQuery = $dbh->prepare("SELECT users.* FROM google_users, users WHERE google_users.google_id = :googleid AND users.id = google_users.user_id LIMIT 1;");
            $checkQuery->bindParam(":googleid", $userData['id']);
            $checkQuery->execute();

            $checkQueryResults = $checkQuery->fetch(PDO::FETCH_ASSOC);

            fwrite($output, "\n\n".print_r($checkQueryResults, true)."\n".$userData['id']);

            if($checkQueryResults) {
                // User already exists
                $data = [
                    'iss' => 'localhost',
                    'exp' => time()+$expireTime,
                    'id' => $checkQueryResults['id'],
                    'email' => $checkQueryResults['email'],
                    'username' => $checkQueryResults['username'],
                    'admin' => $checkQueryResults['admin'],
                    'authGoogle' => true
                ];

                $jwt = JWT::encode($data, $secretKey, 'HS256');

				header("Content-type: application/json");
				echo json_encode(['token' => $jwt]);
            }
            else {
                // Register user
                $userID = 0;
                $httpHeaders = getallheaders();
                if(isset($httpHeaders['Authorization'])) {
                    // Already authenticated, add google info
                    $jwt = explode(" ", $httpHeaders['Authorization'])[1];

                    $decoded = JWT::decode($jwt, $secretKey, array('HS256'));
                    //fwrite($output, "\n\n".print_r($decoded, true));

                    $userID = $decoded->id;
                }
                else {
                    // Not authenticated, create an account from scratch
                    $newUserQuery = $dbh->prepare("INSERT INTO users (username, email) VALUES (:username, :email);");
                    $newUserQuery->bindParam(":username", $userData['name'], PDO::PARAM_STR);
                    $newUserQuery->bindParam(":email", $userData['email'], PDO::PARAM_STR);

                    $newUserQuery->execute();
                    $userID = $dbh->lastInsertId();
                }

                // Link google account
                $newGoogleQuery = $dbh->prepare("INSERT INTO google_users (user_id, google_id) VALUES (:user_id, :google_id);");
                $newGoogleQuery->bindParam(":user_id", $userID, PDO::PARAM_INT);
                $newGoogleQuery->bindParam(":google_id", $userData['id'], PDO::PARAM_INT);
                $newGoogleQuery->execute();

                $data = [
                    'iss' => 'localhost',
                    'exp' => time()+$expireTime,
                    'id' => $userID,
                    'email' => $userData['email'],
                    'username' => $userData['name'],
                    'admin' => false
                ];

                $jwt = JWT::encode($data, $secretKey, 'HS256');

				header("Content-type: application/json");
				echo json_encode(['token' => $jwt]);
            }
        }
        else {
            header("HTTP/1.1 401 Unauthorised");
            fwrite($output, "header's body doesn't have the code attribute");
        }
    }
    else {
        header("HTTP/1.1 401 Unauthorised");
        fwrite($output, "header not set");
    }
}
else {
    header("HTTP/1.1 401 Unauthorised");
    fwrite($output, "provider not set");
}


fwrite($output, print_r(getallheaders(), true));
fclose($output);

?>
