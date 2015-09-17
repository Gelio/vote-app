<?php
/*
New version - with classes
*/

require_once("init.php");

$email = $headersHandler->getHeader('email');
$password = $headersHandler->getHeader('password');

$outputHandler = new OutputHandler("output-login2.txt");
$outputHandler->write($headersHandler->getHeaders(true));

if($headersHandler->isAuthenticated()) {
    // if the user is already authenticated - do not login
    $headersHandler(400);
    $headersHandler->sendJSONData(['error' => "User already authenticated"]);
    $outputHandler->write("user already authenticated");
    die();
}


if($email && $password) {
    $user = new User($database, $email, $password);

    if($user->isAuthenticated()) {
        $jwt = $user->getJWT();
        $preparedData = [
            'token' => $jwt
        ];

        $headersHandler->sendJSONData($preparedData);
        $outputHandler->write("successful login");
        $outputHandler->write($preparedData);
        $outputHandler->write($jwt);
    }
    else {
        $outputHandler->write("email and password do not match");
        $headersHandler->sendHeaderCode(401);
        $headersHandler->sendJSONData(['error' => "Email and password do not match"]);
    }
}
else {
    $outputHandler->write("email and password not set");
    $headersHandler->sendHeaderCode(401);
    $headersHandle->sendJSONData(['error' => "Email or password was not sent correctly"]);
}
?>
