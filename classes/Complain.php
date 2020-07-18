<?php

class Complain{
    private $_id, $_title, $_description, $_status;

    public function __construct($id,$title,$description,$status)
    {
        $this->_id = $id;
        $this->_title = $title;
        $this->_description = $description;
        $this->_status = $status;
    }

    // getter method 
    public function getID() { return $this->_id; }
    public function getTitle() { return $this->_title; }
    public function getDescription() { return $this->_description; }
    public function getStatus() { return $this->_status; }
}