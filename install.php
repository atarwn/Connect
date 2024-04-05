<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection parameters from the form
    $servername = $_POST['servername'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dbname = $_POST['dbname'];

    // Write database connection parameters to db_connect.php
    $db_config_content = "<?php\n";
    $db_config_content .= "date_default_timezone_set('Europe/Moscow');\n";
    $db_config_content .= "// Database credentials\n";
    $db_config_content .= "define('DB_SERVER', '$servername');\n";
    $db_config_content .= "define('DB_USERNAME', '$username');\n";
    $db_config_content .= "define('DB_PASSWORD', '$password');\n";
    $db_config_content .= "define('DB_NAME', '$dbname');\n\n";
    $db_config_content .= "// Attempt to connect to MySQL database\n";
    $db_config_content .= "\$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);\n\n";
    $db_config_content .= "// Check connection\n";
    $db_config_content .= "if (\$mysqli->connect_error) {\n";
    $db_config_content .= "    die('Connection failed: ' . \$mysqli->connect_error);\n";
    $db_config_content .= "}\n";
    $db_config_content .= "?>";

    // Write database connection parameters to db_connect.php
    file_put_contents('db_connect.php', $db_config_content);

    // Attempt to create necessary tables in the database
    $mysqli = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // SQL to create users table
    $sql_users = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            avatar VARCHAR(255),
            personal_info TEXT,
            email TEXT,
            custom_style TEXT,
            isadmin INT,
            ismod INT,
            lastvisittime TIMESTAMP
        )
    ";

    // SQL to create wall table
    $sql_wall = "
        CREATE TABLE IF NOT EXISTS wall (
            id INT AUTO_INCREMENT PRIMARY KEY,
            author VARCHAR(255) NOT NULL,
            author_url VARCHAR(255),
            avatar_url VARCHAR(255),
            post TEXT NOT NULL,
            edited INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";

    $sql_timezone = "SET @@global.time_zone = '+03:00';";

    // Execute SQL queries
    if ($mysqli->query($sql_users) === TRUE && $mysqli->query($sql_wall) === TRUE && $mysqli->query($sql_timezone) === TRUE) {
        echo "Tables created successfully. Please make sure you remove 'install.php' after installation.";
    } else {
        echo "Error creating tables: " . $mysqli->error;
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation</title>
</head>

<body>
    <h2>Database Configuration</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Server Name: <input type="text" name="servername" required><br><br>
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password"><br><br>
        Database Name: <input type="text" name="dbname" required><br><br>
        <input type="submit" value="Install">
    </form>
</body>

</html>
