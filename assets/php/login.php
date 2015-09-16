<?php
require_once("../../vendor/autoload.php");
require_once("config.inc.php");
use \Firebase\JWT\JWT;


require_once('cors-headers.inc.php');

$headerBody = file_get_contents("php://input");
if(is_string($headerBody))
{
	$parsedBody = json_decode($headerBody, true);


	if(isset($parsedBody['email']) && isset($parsedBody['password'])) {
		$email = $parsedBody['email'];
		$password = $parsedBody['password'];

		if(!empty($email) && !empty($password)) {
            try {
                $dbh = new PDO($dbn, $database['user'], $database['password']);
            }
            catch (PDOException $e) {
                fwrite($output, "Connection failed: ".$e->getMessage());
                header("HTTP/1.1 500 Internal Server Error");
            }

            // Query the database for users
            $userQuery = $dbh->prepare("SELECT * FROM users WHERE email = :email AND password = :password LIMIT 1;");
            $userQuery->bindParam(":email", $email, PDO::PARAM_STR);
            $userQuery->bindParam(":password", $password, PDO::PARAM_INT);

            $userQuery->execute();

            $userRow = $userQuery->fetch(PDO::FETCH_ASSOC);
            if($userRow) {
                // Authenticated properly
                $data = [
                    'iss' => 'localhost',
                    'exp' => time()+$expireTime,
                    'id' => $userRow['id'],
                    'email' => $email,
                    'username' => $userRow['username'],
                    'admin' => $userRow['admin']
                ];

                $jwt = JWT::encode($data, $secretKey, 'HS256');

				header("Content-type: application/json");
				echo json_encode(['token' => $jwt]);
            }
			else
				header('HTTP/1.1 401 Unauthorised');
		}
		else
			header("HTTP/1.1 401 Unauthorised");
	}
	else
		header("HTTP/1.1 401 Unauthorised");
}
?>
