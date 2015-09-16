<?php
// Remove this after moving onto a server
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && (
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'POST' ||
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'DELETE' ||
            $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'PUT' )) {
        header('Access-Control-Allow-Origin: http://localhost, http://localhost:63342');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Authorization');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT'); // http://stackoverflow.com/a/7605119/578667
        header('Access-Control-Max-Age: 86400');
    }
    exit;
}

header('Access-Control-Allow-Origin: *');
?>
