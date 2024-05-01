<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="form.css">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="cookiesbanner.css">
    <title>Простой файлообменник</title>
    <meta name="description" content="Просто примитивный Web 3.0 файлообменник">
    <style>
        .success {
            background-color: limegreen;
            color: white;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div id="cookie-banner">
        <p>
        Мы используем куки, только чтобы сохранить текущую тему.<br>Поверьте мне, это удобно <3
        </p>
        <button id="accept-cookie">Понятно</button>
    </div>
    <div class="header">
        <h1><a href="index.php" class="header">Простой файлообменник</a></h1>
    </div>
    <div class="main">
        <?php
        if($_GET['upload'] == 'success') {
            echo '<p class="success">Файл успешно загружен!</p>';
        }
        ?>

        <?php
        // Отображаем список загруженных файлов
        //echo '<p>'.$_POST['MAX_FILE_SIZE'].'</p>';
        echo '<h2>Загруженные файлы</h2>';
        echo '<table>';
        echo '<tr><th>Имя файла</th><th>Описание</th><th>Ссылка на скачивание</th></tr>';

        $fileList = file("filelist.txt", FILE_IGNORE_NEW_LINES);
        foreach ($fileList as $fileInfo) {
            list($fileName, $fileDescription, $fileUrl) = explode('|', $fileInfo);
            echo "<tr><td>$fileName</td><td>$fileDescription</td><td><a href='$fileUrl' download>Скачать</a></td></tr>";
        }
        echo '</table>';
        ?>

        <h2>Загрузить файл</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label for="file">Выберите файл:</label>
            <input type="file" name="file" id="file" required><br>
            <label for="description">Описание:</label>
            <input type="text" maxlength="150" name="description" id="description"><br>
            <input type="submit" value="Загрузить файл">
        </form>

        <button class='btn'><img class="moon" src="moon-svgrepo-com.svg"></button>
    </div>
    <div class="footer">
        <p class="footer"><a href="cookie.html">Политика конфиденциальности</a></p>
        <p class="footer"><a href="https://qwa.su">&copy; qwa.su 2024</a></p>
    </div>
</body>
    <script>
        let button = document.querySelector('.btn');

        let darkModeEnabled = document.cookie.includes('darkModeEnabled=true');

        if (darkModeEnabled) {
            document.documentElement.classList.add('dark-mode');
        } else {
            document.documentElement.classList.remove('dark-mode');
        }

        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark-mode');

            if (document.documentElement.classList.contains('dark-mode')) {
                document.cookie = 'darkModeEnabled=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
            } else {
                document.cookie = 'darkModeEnabled=false; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
            }
        }

        button.addEventListener('click', toggleDarkMode);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var cookiesAccepted = document.cookie.includes('cookiesAccepted=true'); // Проверяем, принял ли пользователь куки
            var cookieBanner = document.getElementById('cookie-banner');
            var acceptCookieButton = document.getElementById('accept-cookie');

            if (cookiesAccepted) {
                cookieBanner.style.display = 'none'; // Скрываем плашку, если пользователь уже принял куки
            } else {
                cookieBanner.style.display = 'block'; // Показываем плашку, если пользователь еще не принял куки
            }

            // Обработчик события для кнопки принятия куки
            acceptCookieButton.addEventListener('click', acceptCookie);
        });

        // Функция для принятия куки
        function acceptCookie() {
            var cookieBanner = document.getElementById('cookie-banner');
            cookieBanner.style.display = 'none'; // Скрываем плашку
            // Устанавливаем куки
            document.cookie = 'cookiesAccepted=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
        }
    </script>
</html>
