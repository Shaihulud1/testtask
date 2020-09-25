<?php
namespace app;

use app\helpers\FileUpload;
use app\helpers\Task;

class CoreController extends AbstractController
{
    public function index()
    {
        return $this->renderView('main');
    }

    public function uploadPhoto()
    {
        $response = [];
        $errors = [];
       # {"status": "received", "task": НОМЕР_ЗАДАЧИ, "result":"null или Результат_(float)"}
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

        $task = new Task;
        $task->setFile($fileUpload);
        $task->setUser($name);
        if ($task->save()) {
            echo 'done';
        } else {
            echo 'error';
        }

        
        // $posibleExtensions = ['jpg', 'png', 'jpeg'];
        // if (!$fileUpload->isCorrectExtension($posibleExtensions)) 
        // {
        //     $errors[] = 'Неправильное расширение файла, должно быть одно из следующих расширений: '.implode(', ', $posibleExtensions);
        //     $response = ['status' => "failed", "result" => $errors];
        //     $this->renderJson($response);  
        //     return;           
        // }

        
        
        // print_R($fileUpload->getFileHash());
        // die;
        

        // print_R($name);
        // print_R($userId);
        // die;
        // print_R($_POST);
        // print_r($_FILES);
        // die;
        // $uploaded = ['response' => 'photo'];
        // return $this->renderJson($uploaded); 
    }
    
    public function getTaskStatus()
    {
        $uploaded = ['response' => 'tasks'];
        return $this->renderJson($uploaded); 
    }
}