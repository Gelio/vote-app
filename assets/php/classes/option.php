<?php


class Option {
    protected $db;
    protected $optionID;
    protected $name;
    protected $pollID;
    protected $amount;

    protected $votes;  // not used right now

    public function __construct(Database $db, $id = null, $pollID = null, $name = "", $amount = 0) {
        $this->db = $db;

        $this->optionID = $id;
        $this->pollID = $pollID;
        $this->name = $name;
        $this->amount = $amount;
    }

    // TODO: fetch function
    public function fetch() {
        if(!$this->optionID)
            throw new Exception("Option ID not set");

        $fetchQuery = $this->db->prepare("SELECT * FROM options WHERE id = :optionID LIMIT 1;");
        $fetchQuery->bindParam(":optionID", $this->optionID, PDO::PARAM_INT);
        $fetchQuery->execute();

        $fetchData = $fetchQuery->fetch(PDO::FETCH_ASSOC);

        if(!$fetchData)
            return false;

        $this->pollID = $fetchData['poll_id'];
        $this->name = $fetchData['name'];
        $this->amount = $fetchData['amount'];
        return true;
    }

    public function addVote($fromUserID) {
        // Update query to update options
        $updateQuery = $this->db->prepare("UPDATE options SET amount = amount+1 WHERE id = :optionID LIMIT 1;");
        $updateQuery->bindParam(":optionID", $this->optionID, PDO::PARAM_INT);
        $updateQuery->execute();

        // Insert query into votes
        $insertQuery = $this->db->prepare("INSERT INTO votes (option_id, user_id, poll_id) VALUES (:optionID, :userID, :pollID);");
        $insertQuery->bindParam(":optionID", $this->optionID, PDO::PARAM_INT);
        $insertQuery->bindParam(":userID", $fromUserID, PDO::PARAM_INT);


        if(!$this->pollID)
            $this->fetch();

        $insertQuery->bindParam(":pollID", $this->pollID, PDO::PARAM_INT);
        $insertQuery->execute();
    }

    public function deleteVote($fromUserID) {
        // Update query to options
        $updateQuery = $this->db->prepare("UPDATE options SET amount = amount-1 WHERE id = :optionID LIMIT 1;");
        $updateQuery->bindParam(":optionID", $this->optionID, PDO::PARAM_INT);
        $updateQuery->execute();

        // Detele query from votes
        $deleteQuery = $this->db->prepare("DELETE FROM votes WHERE poll_id = :pollID AND user_id = :userID LIMIT 1;");
        $deleteQuery->bindParam(":userID", $fromUserID, PDO::PARAM_INT);

        if(!$this->pollID)
            $this->fetch();

        $deleteQuery->bindParam(":pollID", $this->pollID, PDO::PARAM_INT);
        $deleteQuery->execute();
    }

    public function getID() {
        return $this->optionID;
    }

    public function getName() {
        return $this->name;
    }

    public function getAmount() {
        return $this->amount;
    }
}

?>