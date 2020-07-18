<?php

require_once 'api/db.php';
require_once 'classes/Admin.php';

session_start();

class AdminModel
{
    private static $_instance = null;
    private $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    // implementing singleton class
    public static function getInstance()
    {
        if (!isset(self::$_instance)) self::$_instance = new AdminModel;
        return self::$_instance;
    }

    // getting a single admin onfo for setting page
    public function getAdminInfo($id)
    {
        try {
            $sql = "SELECT * FROM admin WHERE adminID = '$id'";
            $stmt = $this->_db->query($sql);
            $admin = $stmt->fetch(PDO::FETCH_OBJ);
            echo json_encode($admin);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    // adding neew Admins 
    public function addNewAdmin(Admin $admin)
    {
        try {
            $sql = "INSERT INTO admin (adminID,name,username,password) VALUES (:id,:name,:username,:password)";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindValue(':id', $admin->getID());
            $stmt->bindValue(':name', $admin->getName());
            $stmt->bindValue(':username', $admin->getUsername());
            $stmt->bindValue(':password', $admin->getPassword());
            // $stmt->bindValue(':picture', $admin->getPicture());

            $stmt->execute();
            $count = $stmt->rowCount();

            $data = array(
                "status" => "success",
                "rowcount" => $count,
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    // editing Admin's info 
    public function updateAdminInfo(Admin $admin)
    {
        $sql = "UPDATE admin SET
                name	 = :name,
                username = :username,
                password = :password,
                picture  = :picture
			    WHERE adminID = :id";

        try {
            $id = $admin->getID();
            $name = $admin->getName();
            $username = $admin->getUsername();
            $password = $admin->getPassword();
            // $picture = $admin->getPicture();

            $stmt = $this->_db->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            // $stmt->bindParam(':picture', $picture);

            $stmt->execute();
            $count = $stmt->rowCount();

            $data = array(
                "rowAffected" => $count,
                "status" => "success"
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    // login to the system 
    public function login($username, $password)
    {
        try {
            $sql = "SELECT * FROM admin WHERE username = :username AND password = :password";

            $stmt = $this->_db->prepare($sql);

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_OBJ);

            if ($stmt->rowCount()) {
                $data = array("status" => "success");
                // setting some admin info
                $_SESSION["name"] = $admin->name;
                $_SESSION["id"] = $admin->adminID;
            } else $data = array("status" => "failed");
            echo json_encode($data);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        $data = array("status" => "success");

        echo json_encode($data);
    }
}
