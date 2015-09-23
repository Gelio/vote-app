<?php

namespace Classes\Polls;

class Poll {
    protected $db;
    protected $pollID;

    protected $userID;
    protected $question;

    protected $options;


    public function __construct(Database $db, $id = null) {
        $this->db = $db;
        $this->pollID = $id;

        if($this->pollID)
            $this->fetchPoll();

        $this->options = array();
    }

    public function setPollID($id) {
        $this->pollID = $id;
    }

    public function fetchPoll() {
        if(!$this->pollID)
            throw new Exception("Poll ID not set");

        $fetchQuery = $this->db->prepare("SELECT * FROM polls WHERE id = :pollID LIMIT 1;");
        $fetchQuery->bindParam(":pollID", $this->pollID, PDO::PARAM_INT);
        $fetchQuery->execute();

        $fetchData = $fetchQuery->fetch(PDO::FETCH_ASSOC);

        if(!$fetchData)
            throw new Exception("Poll with id {$this->pollID} does not exist");

        $this->userID = $fetchData['user_id'];
        $this->question = $fetchData['question'];

        return true;
    }

    public function fetchOptions() {
        if(!$this->pollID)
            throw new Exception("Poll ID not set");

        $fetchQuery = $this->db->prepare("SELECT * FROM options WHERE poll_id = :pollID;");
        $fetchQuery->bindParam(":pollID", $this->pollID, PDO::PARAM_INT);
        $fetchQuery->execute();

        while(($fetchData = $fetchQuery->fetch(PDO::FETCH_ACCOC))) {
            array_push($this->options,
                new Option($this->db, $fetchData['id'], $fetchData['poll_id'], $fetchData['name'], $fetchData['amount'])
            );
        }
    }

    public function getQuestion() {
        return $this->question;
    }

    public function getUser() {
        $user = new User($this->db);
        $user->fetchUser($this->userID);
        return $user;
    }

    public function getOptions() {
        return $this->options;
    }

    public function hasUserVoted($userID) {
        $checkQuery = $this->db->prepare("SELECT option_id FROM votes WHERE user_id = :userID AND poll_id = :pollID LIMTI 1;");
        $checkQuery->bindParam(":userID", $userID, PDO::PARAM_INT);
        $checkQuery->bindParam(":pollID", $this->pollID, PDO::PARAM_INT);
        $checkQuery->execute();

        $checkData = $checkQuery->fetch(PDO::FETCH_ASSOC);
        if($checkData)
            return new Option($this->db, $checkData['id']);

        return false;
    }


}


?>