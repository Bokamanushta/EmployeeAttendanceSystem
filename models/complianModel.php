<?php

require_once 'api/db.php';
require_once 'classes/Complain.php';

class ComplainModel{
    private static $_instance = null;
    private $_db;

    public function __construct() { $this->_db = DB::getInstance(); }

    // implementing singleton class
    public static function getInstance(){
        if (!isset(self::$_instance)) self::$_instance = new ComplainModel;
        return self::$_instance;
    }

    // getting all the complains
    public function getAllComplains(){
        $sql = "SELECT * FROM complain";
        try {
            $stmt = $this->_db->query($sql);
            $complains = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($complains);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // getting a single complina
    public function getSingleComplain($id){
        try {
            $sql = "SELECT * FROM complain WHERE complainID = '$id'";
            $stmt = $this->_db->query($sql);
            $complain = $stmt->fetch(PDO::FETCH_OBJ);
            echo json_encode($complain);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // adding neew complains 
    public function addNewComplain(Complain $complain){
        try {
            $sql = "INSERT INTO complain (complainID,title,description,status) VALUES (:id,:title,:desc,:status)";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindValue(':id', $complain->getID());
            $stmt->bindValue(':title', $complain->getTitle());
            $stmt->bindValue(':desc', $complain->getDescription());
            $stmt->bindValue(':status', $complain->getStatus());

            $stmt->execute();
            $count = $stmt->rowCount();

            $data = array(
                "status" => "success",
                "rowcount" => $count,
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // editing a complain 
    public function updateComplain(Complain $complain)
    {
        $sql = "UPDATE complain SET
                title	= :title,
                description 	= :desc,
                status 	= :status
			    WHERE complainID = :id";

        try {
            $title = $complain->getTitle();
            $description = $complain->getDescription();
            $status = $complain->getStatus();
            $id = $complain->getID();

            $stmt = $this->_db->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':desc', $description);
            $stmt->bindParam(':status', $status);

            $stmt->execute();
            $count = $stmt->rowCount();

            $data = array(
                "rowAffected" => $count,
                "status" => "success"
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    // Deleting a complain  
    public function deleteComplain($id)
    {
        $sql = "DELETE FROM complain WHERE complainID = :id";

        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $count = $stmt->rowCount();

            $data = array(
                "rowAffected" => $count,
                "status" => "success"
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            $data = array( "status" => "fail" );
            echo json_encode($data);
        }
    }

}