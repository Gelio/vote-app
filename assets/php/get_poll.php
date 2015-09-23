<?php
require_once("init.php");

use \Firebase\JWT\JWT;

$pollID = 0;

$outputMessage = [
    'error' => false,
    'message' => ""
];

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $pollID = $_GET['id'];

    $poll = new Poll($database, $pollID);

    if(!$poll->valid()) {
        $outputMessage['error'] = true;
        $outputMessage['message'] = "Poll does not exist";
        $headersHandler->sendHeaderCode(404);
        $headersHandler->sendJSONData($outputMessage);
        die();
    }

    $poll->fetchOptions();
    $options = $poll->getOptions();

    $data = [
        'question' => $poll->getQuestion(),
        'options' => array(),
        'hasVoted' => false
    ];

    foreach($options as $option) {
        array_push($data['options'], [
            'name' => $option->getName(),
            'amount' => $option->getAmount(),
            'id' => $option->getID()
        ]);
    }

    if($headersHandler->isAuthenticated()) {
        // if authenticated - send info if user has already voted ($poll->hasUserVoted)
        $jwt = $headersHandler->getBearer();
        $decodedJWT = JWT::decode($jwt, $secretKey, array('HS256'));

        if($decodedJWT) {
            $decodedArray = (array) $decodedJWT;
            $hasVoted = $poll->hasUserVoted($decodedArray['id']);

            if($hasVoted) {
                $data['hasVoted'] = $hasVoted->getID();
            }
        }
    }

    $headersHandler->sendJSONData($data);
}
else {
    $outputMessage['error'] = true;
    $outputMessage['message'] = "Poll ID is invalid";
    $headersHandler->sendHeaderCode(404);
    $headersHandler->sendJSONData($outputMessage);
}

?>