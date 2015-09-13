<?php
$email = $_POST['email'];
$password = $_POST['password'];

if(!isset($email) || !isset($password) || empty($email) || empty($password))
    die('0');

if($email == "abc@example.com" && $password == "abc")
    die('1');
else
    die('2');
?>