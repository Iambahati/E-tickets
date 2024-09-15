<?php

namespace app;

use PDO;

class Database
{

  static ?PDO $conn = null;

  private $host = 'localhost';
  private $user = 'root';
  private $password = '`eticketing`';
  private $dbName = 'eticketing_db';


  protected function connectToDb()
  {

    // to ensure that the connection is only established once
    if (static::$conn === null) {

      // https://www.php.net/manual/en/intro.pdo.php
      // https://www.phptutorial.net/php-pdo
      $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
      $pdo = new PDO($dsn, $this->user, $this->password);

      // https://www.php.net/manual/en/pdo.setattribute.php
      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC, PDO::FETCH_CLASS
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // PDO::ERRMODE_SILENT, PDO::ERRMODE_WARNING, PDO::ERRMODE_EXCEPTION

      // set timezone
      $pdo->exec("SET SESSION time_zone = '+3:00';");

      static::$conn = $pdo;
    }

    return static::$conn;
  }

  public function getConnection()
  {
    return $this->connectToDb();
  }
}