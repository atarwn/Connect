<?php
$uploadDirectory = 'uploaded-files/';
$fileListFile = "filelist.txt";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fileNameToDelete = $_POST['filename'];
    $adminPassword = $_POST['password'];

    // Проверяем правильность пароля администратора
    $correctAdminPassword = "your_admin_password"; // Замените на ваш пароль

    if ($adminPassword !== $correctAdminPassword) {
        echo '<p>Неправильный пароль администратора!</p>';
    } else {
        // Удаляем файл с сервера
        $filePath = $uploadDirectory . $fileNameToDelete;
        if (file_exists($filePath)) {
            unlink($filePath);
            echo "<p>Файл '$fileNameToDelete' успешно удален с сервера.</p>";
        } else {
            echo "<p>Файл '$fileNameToDelete' не существует на сервере.</p>";
        }

        // Чтение содержимого файла filelist.txt
        $fileData = file($fileListFile);

        // Удаление строки с информацией о файле $fileNameToDelete
        $newFileData = [];
        foreach ($fileData as $line) {
            $lineData = explode('|', $line);
            if (trim($lineData[2]) !== $filePath) {
                $newFileData[] = $line;
            }
        }

        // Запись обновленного содержимого обратно в файл filelist.txt
        file_put_contents($fileListFile, implode("", $newFileData));

        echo "<p>Информация о файле '$fileNameToDelete' успешно удалена из списка файлов.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление файла</title>
</head>
<body>
    <h2>Удаление файла</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="filename">Имя файла:</label>
        <input type="text" name="filename" id="filename" required><br>
        <label for="password">Пароль администратора:</label>
        <input type="password" name="password" id="password" required><br>
        <input type="submit" value="Удалить файл">
    </form>
</body>
</html>