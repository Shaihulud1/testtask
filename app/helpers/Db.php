<?php
namespace app\helpers;

class Db 
{
    private static $conn; 
    private static $dbObj = null;

    private function __construct()
    {
        try {
            //$this->conn = new \PDO('mysql:host=127.0.0.1:3306;dbname=tasks', 'root', 'root');
            $this->conn = new \PDO('mysql:dbname=tasks;host=mysql;port=3306;charset=utf8', 'root', 'root');
        } catch (\Throwable $th) {
            echo "<pre>";print_R($th);echo "</pre>";
            echo 'Ошибка в БД';
            die;
        }
    }

    static function init(): object
    {
        if (self::$dbObj === null) {
            self::$dbObj = new self;
        }
        return self::$dbObj;
    } 

    public function query($sql, $params, $insert=false): ?array
    {
        try {
            $q = $this->conn->prepare($sql);
            $result = $q->execute($params);
            $result = $insert ? ['id' => $this->conn->lastInsertId()] : $q->fetchAll();
            return is_array($result) ? $result : null;
        } catch (\Throwable $th) {
            return null;
        }

    }
}