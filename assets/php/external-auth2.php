<?php
/*
New version - using classes
    1. Check if the user is already authenticated
        a) if yes - linking a provider to user's current account
        b) otherwise - check if user already has authenticated with google before
            i. if yes - log him in again
            ii. otherwise - create a new account and log in
*/

require_once("init.php");

$outputHandler = new OutputHandler("output-external-auth.txt");

if(isset($_GET['provider'])) {
    $provider = $_GET['provider'];


    switch($provider) {
        case "google":
            $code = $headersHandler->getHeader('code');

            $google_client = new Google_Client;

            if($headersHandler->isAuthenticated()) {
                // Link accounts
                $user = new User($database, $headersHandler->getBearer());
                //$outputHandler->write($user->getJWT(false));
                //$headersHandler->sendHeaderCode(400);
                //$headersHandler->sendJSONData(['token' => $user->getJWT()]);

                $googleProvider = new GoogleProvider($database, $user->getID(), $google_client);
                $accessToken = $googleProvider->getAccessToken($code);

                $googleProvider->retrieveData();
                $googleProvider->save();

                $user->fetchProviders();

                $jwt = $user->getJWT();
                $headersHandler->sendJSONData(['token' => $jwt]);
                $outputHandler->write($jwt);
            }
            else {
                // Create a new account and log in
                // TODO
                $outputHandler->write("create a new account - TODO");
            }
            break;





        default:
            $headersHandler->sendHeaderCode(401);
            $headersHandler->sendJSONData(['error' => 'unknown provider']);
            $outputHandler->write('unknown provider');
            break;
    }
}
else {
    $headersHandler->sendHeaderCode(401);
    $headersHandler->sendJSONData(['error' => 'provider not specified']);
    $outputHandler->write('provider not specified');
}

?>
