<?php
class GoogleAuth
{
    protected $client;

    public function __construct(Google_Client $googleClient = null) {
        $this->client = $googleClient;

        if($this->client) {
            $this->client->setClientId('971255903327-u70mlh2duncr4sent7hc8f0j9s8lebld.apps.googleusercontent.com');
            $this->client->setClientSecret('q3rjhm8CSRPvExX46KcPZu3L');
            $this->client->setRedirectUri('http://localhost');
            //$this->client->setScopes(array('email', 'profile'));
        }
    }

    public function getAccessToken($code) {
        $this->client->authenticate($code);

        return $this->client->getAccessToken();
    }

    public function getPayload() {
        return $this->client->verifyIdToken()->getAttributes()['payload'];
    }
}
?>
