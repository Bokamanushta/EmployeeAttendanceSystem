<?php

require_once 'core/config.php';

class DB
{
    // Properties
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $dbname = 'webtech';

    private static $_instance = null;


    // implementing singleton class
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB;
            self::$_instance = self::$_instance->connect();
        }
        return self::$_instance;
    }

    // Connect
    public function connect()
    {
        $mysql_connect_str = "mysql:host=$this->host;dbname=$this->dbname";
        $dbConnection = new PDO($mysql_connect_str, $this->user, $this->password);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    }
}
