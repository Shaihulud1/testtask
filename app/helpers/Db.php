<?php
namespace app\helpers;

class Db 
{
    private static $conn; 
    private static $dbObj = null;

    private function __construct()
    {
        try {
            $this->conn = new \PDO('mysql:host=localhost;dbname=tasks', 'root', 'root');
        } catch (\Throwable $th) {
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
            print_R($th);
            die;
            return null;
        }

    }
}