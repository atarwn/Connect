<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="form.css">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="cookiesbanner.css">
    <title>Простой файлообменник</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        th {
            word-break: break-all;
            padding: 5px;
            width: 33.3%
        }
        table {
            width: 100%;
            border-spacing: 0px;
            border-collapse: collapse;
        }
        table th, td {
            word-break: break-all;
            border: 2px solid #000000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><a href="index.php" class="header">Простой файлообменник</a></h1>
    </div>
    <div class="main">
        <?php
        $uploadDirectory = 'uploaded-files/';
        $maxFileSize = 21000000;


        
        // Обработка загрузки файла
        if (isset($_FILES['file']) && !empty($_FILES['file']['name']) && pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)  !== "php") {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_OK:
                    if ($_FILES['file']['size'] > $maxFileSize) {
                        echo '<p>Ошибка: Размер файла превышает допустимый (' . $maxFileSize/1000000 . ' мегабайт).</p>';
                        break;
                    } else {
                        $fileName = $_FILES['file']['name'];
                        $fileDescription = $_POST['description'];
                        $uniqueFileName = $uniqueFileName = uniqid() . '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDirectory . $uniqueFileName);
                        file_put_contents("filelist.txt", "$fileName|$fileDescription|$uploadDirectory$uniqueFileName\n", FILE_APPEND);
                        header("Location: index.php?upload=success");
                        break;
                    }
                case UPLOAD_ERR_PARTIAL:
                    echo '<p>Ошибка: Файл был загружен частично.</p>';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo '<p>Ошибка: Файл не был загружен.</p>';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo '<p>Ошибка: Отсутствует временная папка для хранения файлов.</p>';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo '<p>Ошибка: Невозможно записать файл на диск.</p>';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo '<p>Ошибка: Загрузка файла запрещена из-за расширения.</p>';
                    break;
                default:
                    echo '<p>Ошибка: Неизвестная ошибка загрузки файла.</p>';
                }
        } else {
            echo '<p>Файл не был загружен.</p>';
        } 
        echo '<p><a href="index.php">Вернутся назад</a></p>'
        ?>
    </div>
</body>
</html>