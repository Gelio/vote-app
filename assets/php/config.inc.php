<?php
$secretKey = "abc";
$expireTime = 3600;

$database = [
    'ip' => 'localhost',
    'user' => 'root',
    'password' => 'abc',
    'port' => 3306,
    'database' => 'vote-app'
];

$dbn = 'mysql:dbname='.$database['database'].';host='.$database['ip'].':'.$database['port'].";charset=UTF8";
?>
