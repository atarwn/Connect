<?php
// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'a0914225_cc');
define('DB_PASSWORD', 'LHzGMuLP');
define('DB_NAME', 'a0914225_cc');

// Attempt to connect to MySQL database
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}
?>