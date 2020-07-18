<?php
require_once 'core/config.php';
if($_POST){
    $data = array();
    array_push($data,$_POST["name"],$_POST["age"],$_POST["logo"],$_POST["file"]["name"]);
    echo json_encode($data);
}