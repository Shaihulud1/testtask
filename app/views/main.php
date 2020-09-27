<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Соответствие имени</title>
    <link href="/css/normalize.css" type="text/css"  data-template-style="true"  rel="stylesheet" />
    <link href="/css/skeleton.css" type="text/css"  data-template-style="true"  rel="stylesheet" />
    <link href="/css/main.css" type="text/css"  data-template-style="true"  rel="stylesheet" />
</head>
<body>
    <div class="form-box">
        <div class="photo-upload">
            <form action="" id="upload-photo-form" method="post">
                <label for="name">Имя</label>
                <input id="name" type="text" name="name">
                <label for="message">Фото</label>
                <input id="photo" type="file" name="photo">
                <input class="button-primary" type="submit" value="Отправить" />
                <p class="errors"></p>
            </form>        
        </div>
    </div>
    <div class="tasks">
        <?if (!empty($tasks)) {?>
                <?foreach ($tasks as $task) {?>
                    <div class="task-item" data-id="<?=$task['id']?>" data-status="<?=$task['status']?>" data-task="<?=$task['retry_id']?>" data-name="<?=$task['name']?>">
                        <p>
                            <strong>Имя:</strong> <?=$task['name']?> 
                            <strong>Результат:</strong> <?=$task['result']?>
                        </p>
                    </div>
                <?}?>
        <?}?>  
    </div>
    <script src="/js/main.js"></script>
</body>
</html>