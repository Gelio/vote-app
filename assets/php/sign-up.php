<?php
/*
Working version
If successful - returns 'message' in a JSON object
Otherwise - returns 'error' in a JSON object with a 401 Unauthorised code

// TODO: check username's, email's and password's vailidity
    currently it's only checked if they exist

*/
require_once("init.php");

$outputHandler = new OutputHandler("output-sign-up.txt");
$outputHandler->write($headersHandler->getHeaders(true));

$username = $headersHandler->getHeader('username');
$email = $headersHandler->getHeader('email');
$password = $headersHandler->getHeader('password');

if($username && $email && $password) {
    if(!$database->userNameExists($username)) {
        if(!$database->userEmailExists($email)) {
            $user = new User($database);
            $user->newUser($email, $username, $password);

            $outputHandler->write("user registered successfully");
            $headersHandler->sendJSONData(['message' => "registered successfully"]);
        }
        else {
            // Email already exists
            $headersHandler->sendHeaderCode(401);
            $headersHandler->sendJSONData(['error' => "E-mail already registered"]);
            $outputHandler->write("e-mail $email already exists");
        }
    }
    else {
        // Username already exists
        $headersHandler->sendHeaderCode(401);
        $headersHandler->sendJSONData(['error' => "Username already taken"]);
        $outputHandler->write("username $username already taken");
    }
}
else {
    // Credentials not given
    $headersHandler->sendHeaderCode(401);
    $headersHandler->sendJSONData(['error' => "Credentials not given"]);
    $outputHandler->write("credentials not given");
}

?>
