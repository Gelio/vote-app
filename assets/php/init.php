<?php

require_once("../../vendor/autoload.php");
use \Firebase\JWT\JWT;

require_once("config.inc.php");

require_once("classes/database.php");
require_once("classes/headers-handler.php");
require_once("classes/output-handler.php");

require_once("classes/GoogleProvider.php");

require_once("classes/user.php");


require_once('cors-headers.inc.php');

$headersHandler = new HeadersHandler;

$dbh = new PDO($dbn, $database['user'], $database['password']);
$database = new Database($dbh);

?>
