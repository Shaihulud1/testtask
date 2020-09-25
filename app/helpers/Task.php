<?php
namespace app\helpers;
use app\helpers\Db;

class Task
{
    private $db; 
    private $fileUpload;
    private $user;
    private $errors = [];

    public function __construct()
    {
        $this->db = Db::init();    
    }

    public function setFile(FileUpload $fileUpload): void
    {
        $this->fileUpload = $fileUpload;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function save(): array
    {
        if (!$this->fileUpload || !$this->user) {
            return ['status' => "failed", "result" => ['Пустой файл или пользователь']];
        }
        $existTasks = $this->getTasks();
        if (!empty($existTasks)) {
            return ['status' => $existTasks[0]['status'], "result" => $existTasks[0]['result'], "task" => $existTasks[0]['id_task']];
        }
        move_uploaded_file($_FILES["photo"]["tmp_name"], "upload/" . $filename);
        return [];
    }

    public function getTasks(): array
    {
        if (!$this->user) {
            return [];
        }
        $result = [];
        $sql = "SELECT * FROM tasks WHERE user = ?";
        $params = [$this->user];
        if ($this->fileUpload) {
            $photo = $this->fileUpload->getFileHash();
            $sql .= " AND photo = ? ";
            $params[] = $photo;
        }
        $result = $this->db->query($sql, $params);
        return is_array($result) ? $result : [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}