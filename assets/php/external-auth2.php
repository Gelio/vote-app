<?php
/*
New version - using classes
*/

require_once("init.php");

$outputHandler = new OutputHandler("output-external-auth.txt");

if(isset($_GET['provider'])) {
    $provider = $_GET['provider'];
    $outputHandler->write($provider);


    switch($provider) {
        case "google":
            $code = $headersHandler->getHeader('code');

            $google_client = new Google_Client;

            if($headersHandler->isAuthenticated()) {
                // Link accounts
                $user = new User($database, $headersHandler->getBearer());

                $googleProvider = new GoogleProvider($database, $user->getID(), $google_client);
                $accessToken = $googleProvider->getAccessToken($code);

                //$googleProvider->getDataFromDB();
                $retrievedData = $googleProvider->retrieveData();

                // check if anyone has the same GoogleID and different user_id
                $checkQuery = $database->prepare("SELECT id FROM google_users WHERE google_id = :googleID AND user_id != :userID LIMIT 1;");
                $checkQuery->bindParam(":googleID", $retrievedData['id'], PDO::PARAM_INT);
                $checkQuery->bindParam(":userID", $user->getID(), PDO::PARAM_INT);
                $checkQuery->execute();

                if($checkQuery->rowCount() == 0) {
                    // First time this google ID appeared in the DB
                    $googleProvider->save();

                    $user->fetchProviders();

                    $jwt = $user->getJWT();
                    $headersHandler->sendJSONData(['token' => $jwt]);
                    $outputHandler->write($jwt);
                }
                else {
                    // Someone is already using that account with other Vote App account
                    $headersHandler->sendHeaderCode(401);
                    $headersHandler->sendJSONData(['error' => "google account already in use"]);
                    $outputHandler->write("google account ".$retrievedData['email']." already in use");
                }
            }
            else {
                // b from the list

                $googleProvider = new GoogleProvider($database, 0, $google_client);
                $accessToken = $googleProvider->getAccessToken($code);
                $userGoogleData = $googleProvider->retrieveData();

                // Check if id from userGoogleData exists as google_id in google_users
                $checkIDQuery = $database->prepare("SELECT id, user_id FROM google_users WHERE google_id = :googleID LIMIT 1;");
                $checkIDQuery->bindParam(":googleID", $userGoogleData['id'], PDO::PARAM_INT);
                $checkIDQuery->execute();

                $checkIDData = $checkIDQuery->fetch(PDO::FETCH_ASSOC);

                if($checkIDData) {
                    // Already exists, log in existing user
                    $user = new User($database);
                    $user->fetchUser($checkIDData['user_id']);

                    $jwt = $user->getJWT();
                    $headersHandler->sendJSONData(['token' => $jwt]);
                    $outputHandler->write($jwt);
                }
                else {
                    // Does not exist, register user
                    $user = new User($database);
                    // putting in a placeholder password, BAD PRACTICE
                    $user->newUser($userGoogleData['email'], $userGoogleData['name'], md5("a"));

                    /// add Google as a provider
                    $googleProvider->setUserID($user->getID());
                    $googleProvider->setGoogleID($userGoogleData['id']);
                    $googleProvider->getDataFromDB();
                    $googleProvider->save();

                    // refresh providers
                    $user->fetchProviders();

                    $jwt = $user->getJWT();
                    $headersHandler->sendJSONData(['token' => $jwt]);
                    $outputHandler->write($jwt);
                }
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
