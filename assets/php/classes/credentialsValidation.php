<?php
namespace Classes;

class Validation
{
    static function checkUsername($username) {
        if (!$username || !is_string($username))
            return false;

        $length = strlen($username);

        if ($length >= 3 && $length <= 50)
            return true;
        return false;
    }

    static function checkPassword($password) {
        if (!$password || !is_string($password))
            return false;

        $length = strlen($password);
        if ($length >= 5 && $length <= 50)
            return true;
        return false;
    }

    static function checkEmail($email) {
        if(!$email || !is_string($email))
            return false;

        $length = strlen($email);
        if($length <= 255 && filter_var($email, FILTER_VALIDATE_EMAIL))
            return true;
        return false;
    }
}
?>