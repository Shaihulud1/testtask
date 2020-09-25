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
    <script src="/js/main.js"></script>
</body>
</html>