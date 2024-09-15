<?php

class Db
{
    private $dsn;
    private $dbuser;
    private $dbpass;

    public $conn;

    public function __construct()
    {
 
        // https://www.phptutorial.net/php-pdo
        $this->dsn = "mysql:host=localhost;dbname=e-ticketing";
        $this->dbuser = "root";
        $this->dbpass = '';
        try {
            $this->conn = new PDO($this->dsn, $this->dbuser, $this->dbpass);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
