<?php
namespace app;

use app\helpers\FileUpload;
use app\helpers\Task;

class CoreController extends AbstractController
{
    public function index()
    {
        $userId = $_COOKIE['PHPSESSID'];
        $task = new Task;
        $tasks = $task->getTasks(['id_user' => $userId]);
        return $this->renderView('main', ['tasks' => $tasks]);
    }

    public function uploadPhoto()
    {
        $response = [];
        $errors = [];
        if (!isset($_FILES['photo'])) {
            $errors[] = 'Пустое фото';
        }
        if (!isset($_POST['name'])) {
            $errors[] = 'Пустое имя';
        }
        if (!empty($errors)) {
            $response = ['status' => "failed", "result" => $errors];
            $this->renderJson($response);  
            return;
        }
        $name = trim(strip_tags($_POST['name']));
        $fileUpload = new FileUpload($_FILES['photo']);
        $userId = $_COOKIE['PHPSESSID']; 
        $task = new Task;
        $task->setFile($fileUpload);
        $task->setName($name);
        $task->setUserId($userId);
        $result = $task->save();
        $this->renderJson($result); 
    }
    
    public function getTaskStatus()
    {
        $taskId = trim(strip_tags($_GET['task_id']));
        $task = new Task;
        $waitingTask = $task->getTasks(['retry_id' => $taskId, 'status' => 'wait']);
        $taskStatus = $task->getNamePhotoConformity(['retry_id' => $taskId]);
        if ($taskStatus['status'] == 'ready' || $taskStatus['status'] == 'success') {
            $task->updateTask((int)$waitingTask[0]['id'], (string)$taskStatus['status'], (string)$taskStatus['result']);
        }
        return $this->renderJson($taskStatus);
    }
}