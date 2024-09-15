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
        $this->dsn = "mysql:host=localhost;dbname=ticketingapp";
        $this->dbuser = "root";
        $this->dbpass = '';
        try {
            $this->conn = new PDO($this->dsn, $this->dbuser, $this->dbpass);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
/* 
<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpassword = "";
$dbname = "ticketingapp";


$dbhost = "localhost";
$dbuser = "root";
$dbpassword = "";
$dbname = "ticketingapp";

try {
    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>


*/