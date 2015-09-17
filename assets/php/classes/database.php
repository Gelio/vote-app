<?php

class Database
{
    protected $dbh;

    public function __construct(PDO $dbh) {
        if($dbh->errorCode() !== NULL) {
            echo "database handle invalid, error code ".$dbh->errorCode();
            return;
        }

        $this->dbh = $dbh;
    }

    public function prepare($query) {
        return $this->dbh->prepare($query);
    }

    public function userNameExists($name) {
        $checkQuery = $this->prepare("SELECT id FROM users WHERE username = :username LIMIT 1;");
        $checkQuery->bindParam(":username", $name);
        $checkQuery->execute();

        $checkData = $checkQuery->fetch(PDO::FETCH_ASSOC);
        if($checkData)
            return $checkData['id'];
        else
            return false;
    }

    public function userEmailExists($email) {
        $checkQuery = $this->prepare("SELECT id FROM users WHERE email = :email LIMIT 1;");
        $checkQuery->bindParam(":email", $email);
        $checkQuery->execute();

        $checkData = $checkQuery->fetch(PDO::FETCH_ASSOC);
        if($checkData)
            return $checkData['id'];
        else
            return false;
    }

    public function userIDExists($id) {
        $checkQuery = $this->prepare("SELECT id FROM users WHERE id = :id LIMIT 1;");
        $checkQuery->bindParam(":id", $id ,PDO::PARAM_INT);
        $checkQuery->execute();

        $checkData = $checkQuery->fetch(PDO::FETCH_ASSOC);
        if($checkData)
            return $checkData['id'];
        else
            return false;
    }

    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
}

?>
