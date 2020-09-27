<?php
namespace app\helpers;
use app\helpers\Db;

class Task
{
    private $db; 
    private $fileUpload;
    private $name;
    private $userId;

    public function __construct()
    {
        $this->db = Db::init();    
    }

    public function setFile(FileUpload $fileUpload): void
    {
        $this->fileUpload = $fileUpload;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function save(): array
    {
        if (!$this->fileUpload || !$this->name) {
            return ['status' => "failed", "result" => ['Пустой файл или пользователь']];
        }
        $existTasks = $this->getTasks(['name' => $this->name, 'photo_hash' => $this->fileUpload->getFileHash()] );
        if (!empty($existTasks)) {
            return ['id' => $existTasks[0]['id'], 'status' => $existTasks[0]['status'], "result" => $existTasks[0]['result'], "retry_id" => $existTasks[0]['retry_id']];
        }
        if (!$this->fileUpload->saveFile()) {
            return ['status' => "failed", "result" => ['Не удается загрузить фото']];
        }
        
        $cFile = curl_file_create($this->fileUpload->getSavedFile(), $this->fileUpload->getMime(), $this->fileUpload->getName());
        $post = ['name' => $this->name, 'photo' => $cFile];
        $apiResult = $this->getNamePhotoConformity($post);
        if (!$apiResult) {
            return ['status' => "failed", "result" => ['Ошибка проверки фото']];
        }
        $insertId = $this->insertNewTask(
            (string) $apiResult['retry_id'], 
            (string) $this->userId, 
            (string) $this->fileUpload->getFileHash(),
            (string) $apiResult['status'],
            (string) $apiResult['result'],
            (string) $this->name
        );
        $this->fileUpload->deleteSavedFile();
        return ['id' => $insertId, 'status' => $apiResult['status'], "result" => $apiResult['result'], "retry_id" => $apiResult['retry_id']];
    }

    public function insertNewTask(string $retryId, string $idUser, string $photoHash, string $status, string $result, string $name): ?int
    {
        $sql = "INSERT INTO tasks 
                    (retry_id, id_user, photo_hash, status, result, name) 
                    VALUES 
                    (?, ?, ?, ?, ?, ?)";
        $result = $this->db->query($sql, [$retryId, $idUser, $photoHash, $status, $result, $name], true);
        return $result['id'] ? $result['id'] : null;
    }
    
    public function updateTask(int $id, string $status, string $result)
    {
        $sql = "UPDATE tasks SET status = ? , result = ? WHERE id = ?";
        $this->db->query($sql, [$status, $result, $id]);
    }

    public function getNamePhotoConformity($post=[]): ?array
    {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, 'http://merlinface.com:12345/api/');
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        $headers = ['Content-type: multipart/form-data'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        if (preg_match("/(\{.+\})/", $result, $matches)) {    
            /* Почему-то иногда в тело запроса возвращается такого вида ответ: 
                HTTP/1.1 202 Accepted Server: nginx Content-Type: application/json Transfer-Encoding: chunked Connection: 
                keep-alive X-Powered-By: PHP/7.4.10 Cache-Control: no-cache, private Date: Sun, 27 Sep 2020 09:38:50 GMT 
                {"status":"wait","result":null,"retry_id":"3f8c863e-00a5-11eb-9b94-0242ac12003a","errors":[]} 
                Поэтому запилил такой костыль       
            */ 
            $result = $matches[1];
        }
        if (curl_errno($ch)) {
            return null;
        } 
        curl_close($ch); 
        $result = json_decode($result, true);        
        return is_array($result) ? $result : null;
    }

    public function getTasks($whereParams=[]): array
    {
        $result = [];
        $sql = "SELECT * FROM tasks";
        $params = [];
        if (!empty($whereParams)) {
            $isFirst = true;
            foreach ($whereParams as $nameParam => $valueParam) {
                if ($isFirst) {
                    $sql .= " WHERE ";
                    $isFirst = false;
                } else {
                    $sql .= " AND ";
                }
                $sql .= " $nameParam = ? ";
                $params[] = $valueParam;
            }

        }
        $sql .= " ORDER BY id DESC";
        $result = $this->db->query($sql, $params);
        return is_array($result) ? $result : [];
    }

}


// Написать сервис, который будет сообщать насколько имя подходит человеку по его фотографии.
// Сервис должен быть обернут в Docker контейнер. Для контроля зависимостей важно использовать composer.
// Нельзя использовать какой либо фреймворк целиком, но можно спокойно использовать его части, либо любые другие библиотеки любых других разработчиков.

// Сервис должен иметь одну точку входа (localhost:8000), которая может принимать два типа запроса:
// 1) POST запрос, с содержимым типа multipart/form-data, с полями name и photo.
// 2) GET запрос, с обычным query параметром task_id

// Результатом запроса должны быть:
// 1) Загружена фотография во временную папку
// 2) Проверено, не отправлял ли этот пользователь уже эту фотографию, в этом случае вернуть результат и status=ready.
// 2) Создана задача и записана в БД/Хранилище
// 3) Отдан ответ json (с соответствующим http кодом ответа):
// {"status": "received", "task": НОМЕР_ЗАДАЧИ, "result":"null или Результат_(float)"}

// Сервис должен сразу вернуть ответ и в фоне запустить дальнейшую работу:
// 1) Отправить POST запрос, с содержимым типа multipart/form-data на http://merlinface.com:12345/api/ с телом, содержащим:
// [
//   'name' => "Имя",
//   'photo' => файл фотографии, созданный посредством curl_file_create(),
// ]
// 2) Получить и обработать два варианта ответа
//   а)  Результат готов сразу:
//     {"status": "ready", "result": "Результат_(float)"}
//   б)  Требуется время, запросите результат через пару секунд
//     {"status": "wait", "result": null, "retry_id": id_задачи_для_повторного_запроса}
    
// В случае варианта "а", нужно:
// - отметить что задача с номером НОМЕР_ЗАДАЧИ была выполнена
// - записать результат

// В случае варианта "б", нужно:
// - повторять запрос через пару секунд, но уже вместо фотографии и имени отправить поле retry_id со значением retry_id, полученного в ответе варианта "б". 
//   До тех пор, пока не будет получен ответ:
//   {"status": "ready", "result": "Результат_(float)"}
// - отметить что задача с номером НОМЕР_ЗАДАЧИ была выполнена
// - записать результат

// При обращении к конечной точке сервиса(для получения результата) посредством GET запроса /?task_id=НОМЕР_ЗАДАЧИ, должен быть отдан один из трех вариантов ответов:
// 1)  Результат еще не готов  (с соответствующим http кодом ответа)
//   {"status": "wait", "result": null}
// 2)  Результат готов (с соответствующим http кодом ответа)
//   {"status": "ready", "result": "Результат_(float)"}
// 3)  Задача с таким номером не найдена (с соответствующим http кодом ответа)
//   {"status": "not_found", "result": null}