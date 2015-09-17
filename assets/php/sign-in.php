<?php
/*
========================================================================================
					Broken version, not using classes, use sign-up.php
========================================================================================
*/

require_once("init.php");

$output = fopen("sign-in-output.txt", "w");
$outputData = [
    'error' => false,
    'message' => 'User has been successfully signed in.'
];

$parsedBody = json_decode(file_get_contents("php://input"), true);

if(isset($parsedBody['username']) && isset($parsedBody['email']) && isset($parsedBody['password'])) {
    try {
        $dbh = new PDO($dbn, $database['user'], $database['password']);
    } catch(PDOException $e) {
        fwrite($output, "Connection failed: ".$e->getMessage());
        header("HTTP/1.1 500 Internal Server Error");
        $outputData['error'] = true;
        $outputData['message'] = "Database connection cannot be established.";
    }

    // Check for username
    $checkQuery = $dbh->prepare("SELECT id FROM users WHERE username LIKE :username LIMIT 1;");
    $checkQuery->bindParam(':username', $parsedBody['username'], PDO::PARAM_STR);
    $checkQuery->execute();

    if($checkQuery->rowCount() == 0) {
        $checkQuery = $dbh->prepare("SELECT id FROM users WHERE email LIKE :email LIMIT 1;");
        $checkQuery->bindParam(":email", $parsedBody['email'], PDO::PARAM_STR);
        $checkQuery->execute();

        if($checkQuery->rowCount() == 0) {
            // User does not exist, register
            $registerQuery = $dbh->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $registerQuery->bindParam(":username", $parsedBody['username']);
            $registerQuery->bindParam(":email", $parsedBody['email']);
            $registerQuery->bindParam(":password", $parsedBody['password']);
            $registerQuery->execute();
        }
        else {
            $outputData['error'] = true;
            $outputData['message'] = "E-mail already exists.";
            header("HTTP/1.1 401 Unauthorised");
        }
    }
    else {
        // username already exists
        $outputData['error'] = true;
        $outputData['message'] = "Username already exists.";
        header("HTTP/1.1 401 Unauthorised");
    }
}
else {
    $outputData['error'] = true;
    $outputData['message'] = "User data not sent.";
    header("HTTP/1.1 401 Unauthorised");
}

fwrite($output, "\n\n".print_r($outputData, true));
fclose($output);
echo json_encode($outputData);

?>
