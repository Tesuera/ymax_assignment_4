<?php

namespace Lib\Database;

use PDO;
use PDOException;

class MySQL {
    private $db;
    private $host;
    private $dbname;
    private $user;
    private $psw;


    public function __construct(
        $host = "localhost",
        $dbname = "ymax_assignment",
        $user = "root",
        $psw = "",
    ) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->psw = $psw;
    }

    public function connection () {
        try {
            $this->db = new PDO("mysql:dbhost=$this->host;dbname=$this->dbname",
            $this->user,
            $this->psw,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);

            return $this->db;
        }catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }
}
