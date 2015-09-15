<?php
require_once("../../vendor/autoload.php");
require_once("config.inc.php");
use \Firebase\JWT\JWT;


// Remove this after moving onto a server
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && (
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'POST' ||
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'DELETE' ||
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'PUT' )) {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Headers: X-Requested-With');
        header('Access-Control-Allow-Headers: Content-Type');
		header('Access-Control-Allow-Headers: Authorization');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT'); // http://stackoverflow.com/a/7605119/578667
        header('Access-Control-Max-Age: 86400');
    }
    exit;
}

header('Access-Control-Allow-Origin: *');
// header("Access-Control-Allow-Credentials: true");
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
// header('Access-Control-Allow-Headers: X-Requested-With');
// header('Access-Control-Allow-Headers: Content-Type');
// header('Access-Control-Max-Age: 86400');

$headerBody = file_get_contents("php://input");
if(is_string($headerBody))
{
	$parsedBody = json_decode($headerBody, true);


	if(isset($parsedBody['email']) && isset($parsedBody['password'])) {
		$email = $parsedBody['email'];
		$password = $parsedBody['password'];

		if(!empty($email) && !empty($password)) {
			if($email == "abc@example.com" && $password == "abc") {
				$data = [
					'iss' => 'localhost',
					'exp' => time()+$expireTime,
					'id' => 1,
					'email' => $email,
					'admin' => false
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


	// $file = fopen("post.txt", "w");
	// fwrite($file, print_r($parsedBody, true));
	// fclose($file);

}
// $file = fopen("headers.txt", "w");
// fwrite($file, print_r(getallheaders(), true));
// fclose($file);
?>
