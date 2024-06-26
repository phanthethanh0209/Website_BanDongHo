<?php
class Database {
    public function getConnect() {
        $host = "localhost";
        $db = "db_elegant_watch";
        $username = "mydb_admin";
        $password = "6gJD1WtuJAxkRlKE";

        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        try{
            $pdo = new PDO($dsn, $username, $password);
            if($pdo){
                return $pdo;
            }
        }catch(PDOException $ex) {
            echo $ex->getMessage();
        }
    }
}
