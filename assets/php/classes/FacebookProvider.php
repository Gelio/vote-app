<?php
/*
    -- 1. Create this class
    -- 2. Modify external-auth2.php and add Facebook as a provider
    -- 3. Modify User::fetchProviders to include Facebook as a provider
    -- 4. Modify User::getJWT to return authFacebook as a provider (check if the user is authenticated)
    -- 5. Add authFacebook to front end and make the button disabled if authFacebook == true
*/
//session_start();

class FacebookProvider {
    protected $db;
    protected $dbID = null;
    protected $userID = 0;
    protected $facebookID = null;
    protected $accessToken = null;
    protected $facebookClient;

    public function __construct(Database $database, $userID = 0) {
        $this->db = $database;
        $this->facebookClient = new Facebook\Facebook([
            'app_id' => '1636616339940292',
            'app_secret' => '807083068e41e89225561f9e89411190',
            'default_graph_version' => 'v2.2'
        ]);
        $this->userID = $userID;
    }

    public function retrieveAccessTokenFromRedirect() {
        // not getRedirectLoginHelper
        $helper = $this->facebookClient->getJavaScriptHelper();

        try {
            $this->accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            return false;;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            return false;
        }

        if($this->accessToken) {
            $this->accessToken = (string) $this->accessToken;
            $this->extendAccessToken();
            return true;
        }
        else
            return false;
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }

    public function extendAccessToken() {
        if($this->accessToken) {
            $oAuth2Client = $this->facebookClient->getOAuth2Client();

            $longToken = $oAuth2Client->getLongLivedAccessToken($this->accessToken);
            if($longToken) {
                $this->accessToken = (string) $longToken;
                return true;
            }
        }

        return false;
    }

    public function getUserData() {
        if($this->accessToken) {
            $this->facebookClient->setDefaultAccessToken($this->accessToken);

            try {
                $response = $this->facebookClient->get('/me');
                $userNode = $response->getGraphUser();
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                return false;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                return false;
            }

            if(isset($userNode)) {
                $userData = [
                    'facebookID' => $userNode->getId(),
                    'name' => $userNode->getName(),
                    'email' => $userNode->getField('email')
                ];
                $this->facebookID = $userNode->getId();

                return $userData;
            }
            else
                return false;
        }
    }

    public function exchangeCodeForAccessToken($clientID, $redirectURI, $code) {
        $exchangeURL = "https://graph.facebook.com/oauth/access_token?client_id=$clientID&redirect_uri=$redirectURI&client_secret=807083068e41e89225561f9e89411190&code=$code";
        $exchangeData = file_get_contents($exchangeURL);

        $divideParameters = explode('&', $exchangeData);
        $divideValues = explode('=', $divideParameters[0]);
        return $divideValues[1];

    }

    public function getHelper() {
        return $this->facebookClient->getJavaScriptHelper();
    }

    public function save() {
        if($this->isAuthenticated()) {
            // Update
            $updateQuery = $this->db->prepare("UPDATE facebook_users SET user_id = :userID, facebookID = :facebookID, access_token = :accessToken WHERE id = :dbID LIMIT 1;");
            $updateQuery->bindParam(":userID", $this->userID, PDO::PARAM_INT);
            $updateQuery->bindParam(":facebookID", $this->facebookID);
            $updateQuery->bindParam(":accessToken", $this->accessToken);
            $updateQuery->bindParam(":dbID", $this->dbID, PDO::PARAM_INT);
            $updateQuery->execute();

            return true;
        }
        else {
            // Insert
            $insertQuery = $this->db->prepare("INSERT INTO facebook_users (user_id, facebook_id, access_token) VALUES (:userID, :facebookID, :accessToken);");
            $insertQuery->bindParam(":userID", $this->userID, PDO::PARAM_INT);
            $insertQuery->bindParam(":facebookID", $this->facebookID);
            $insertQuery->bindParam(":accessToken", $this->accessToken);
            $insertQuery->execute();

            $this->dbID = $this->db->lastInsertId();
            return true;
        }
    }

    public function isAuthenticated() {
        return ($this->dbID !== null ? 1 : 0);
    }

    public function getUserID() {
        return $this->userID;
    }

    public function setUserID($userID) {
        $this->userID = $userID;
    }

    public function searchByUserID($userID = null) {
        if(!$userID)
            $userID = $this->userID;

        if(!$userID)
            return false;

        $checkQuery = $this->db->prepare("SELECT * FROM facebook_users WHERE user_id = :userID LIMIT 1;");
        $checkQuery->bindParam(":userID", $this->userID, PDO::PARAM_INT);
        $checkQuery->execute();

        $checkQueryData = $checkQuery->fetch(PDO::FETCH_ASSOC);
        if($checkQueryData) {
            $this->dbID = $checkQueryData['id'];
            $this->userID = $checkQueryData['user_id'];
            $this->facebookID = $checkQueryData['facebook_id'];
            $this->accessToken = $checkQueryData['access_token'];
            return true;
        }
        else {
            // Not found
            return false;
        }
    }

    public function searchByFacebookID($facebookID = null) {
        if(!$facebookID)
            $facebookID = $this->facebookID;

        if(!$facebookID)
            return false;

        $checkQuery = $this->db->prepare("SELECT * FROM facebook_users WHERE facebook_id = :facebookID LIMIT 1;");
        $checkQuery->bindParam(":facebookID", $this->facebookID, PDO::PARAM_INT);
        $checkQuery->execute();

        $checkQueryData = $checkQuery->fetch(PDO::FETCH_ASSOC);
        if($checkQueryData) {
            $this->dbID = $checkQueryData['id'];
            $this->userID = $checkQueryData['user_id'];
            $this->facebookID = $checkQueryData['facebook_id'];
            $this->accessToken = $checkQueryData['access_token'];
            return true;
        }
        else {
            // Not found
            return false;
        }
    }

    public function facebookIDRepeatsAmount($facebookID = null) {
        if(!$facebookID)
            $facebookID = $this->facebookID;

        // if none of facebookID's are given return false
        if(!$facebookID)
            return false;

        $checkQuery = $this->db->prepare("SELECT id FROM facebook_users WHERE facebook_id = :facebookID;");
        $checkQuery->bindParam(":facebookID", $facebookID, PDO::PARAM_INT);
        $checkQuery->execute();

        return $checkQuery->rowCount();
    }
}

?>
