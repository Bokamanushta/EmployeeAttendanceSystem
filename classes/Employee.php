<?php
//This class will help to register new Employees for the system
class Employee{
    private $_id, $_name, $_email, $_rfid, $_dob, $_phone, $_ic, $_department, $_position;

    public function __construct($id,$name,$email,$rfid,$dob,$phone,$ic,$department,$position)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_email = $email;
        $this->_rfid = $rfid;
        $this->_dob = $dob;
        $this->_phone = $phone;
        $this->_ic = $ic;
        $this->_department = $department;
        $this->_position = $position;
        // $this->_picture = $picture;
    }

    // getter method 
    public function getID() { return $this->_id; }
    public function getName() { return $this->_name; }
    public function getEmail() { return $this->_email; }
    public function getRFID() { return $this->_rfid; }
    public function getDOB() { return $this->_dob; }
    public function getPhone() { return $this->_phone; }
    public function getIC() { return $this->_ic; }
    public function getDepartment() { return $this->_department; }
    public function getPosition() { return $this->_position; }
    // public function getPicture() { return $this->_picture; }
}