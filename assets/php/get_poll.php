<?php
use Classes\Polls;
use \Firebase\JWT\JWT;

$pollID = 0;

$outputMessage = [
    'error' => false,
    'message' => ""
];

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $pollID = $_GET['id'];

    $poll = new Poll($database, $pollID);
    $poll->fetchOptions();
    $options = $poll->getOptions();

    $data = [
        'question' => $poll->getQuestion(),
        'options' => array(),
        'hasVoted' => false
    ];

    foreach($option as $options) {
        array_push($data['options'], [
            'name' => $options->getName(),
            'amount' => $options->getAmount()
        ]);
    }

    if($headersHandler->isAuthenticated()) {
        // if authenticated - send info if user has already voted ($poll->hasUserVoted)
        $jwt = $headersHandler->getBearer();
        $decodedJWT = JWT::decode($jwt, $secretKey, array('HS256'));

        if($decodedJWT) {
            $hasVoted = $poll->hasUserVoted($decodedJWT['id']);

            if($hasVoted)
                $data['hasVoted'] = $hasVoted->getID();
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