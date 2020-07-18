<?php
//This class will help to register new admins for the system
class Admin{
    private $_id, $_name, $_username, $_password;

    public function __construct($id,$name,$username,$password)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_username = $username;
        $this->_password = $password;
        // $this->_picture = $picture;
    }

    // getter method 
    public function getID() { return $this->_id; }
    public function getName() { return $this->_name; }
    public function getUsername() { return $this->_username; }
    public function getPassword() { return $this->_password; }
    // public function getPicture() { return $this->_picture; }
}