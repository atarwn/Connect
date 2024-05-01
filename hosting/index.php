<?php
session_start();
// Include database connection
include_once '../system/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Function to generate a random password
function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// Function to create folder and FTP account
function createHosting($mysqli, $username) {
    // Check if hosting already exists for the user
    $sql_check = "SELECT * FROM hosting WHERE username = ?";
    $stmt_check = $mysqli->prepare($sql_check);
    $stmt_check->bind_param('s', $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        // Hosting already exists, retrieve and display FTP account details
        $row = $result_check->fetch_assoc();
        echo "Hosting already exists for $username:";
        echo "<p>IP: {$row['server_ip']}</p>";
        echo "<p>FTP Username: {$row['ftp_username']}</p>";
        echo "<p>FTP Password: {$row['ftp_password']}</p>";
        return false; // Hosting creation failed
    }

    // Generate folder name
    $folderName = strtolower(str_replace(' ', '_', $username));

    // Check if folder already exists
    if (!file_exists("../../my/$folderName")) {
        // Create the folder
        mkdir("../../my/$folderName");

        // Generate FTP username (you can customize this logic as needed)
        $ftpUsername = "myconnect_" . $username;

        // Generate FTP password
        $ftpPassword = generateRandomPassword(); // Generate random password

        // Insert into database
        $sql = "INSERT INTO hosting (username, folder_name, ftp_username, ftp_password, server_ip) VALUES (?, ?, ?, ?, '141.8.192.163')";
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param('ssss', $username, $folderName, $ftpUsername, $ftpPassword);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                echo "<h1>Hosting created successfully!</h1>";
                echo "<p>IP: 141.8.192.163</p>";
                echo "<p>FTP Username: $ftpUsername</p>";
                echo "<p>FTP Password: $ftpPassword</p>";
                return true; // Hosting created successfully
            } else {
                echo "Failed to insert into database.\n";
                return false; // Error inserting into database
            }

            // Close statement
            $stmt->close();
        } else {
            echo "Error preparing SQL statement.\n";
            return false; // Error preparing SQL statement
        }
    } else {
        echo "Folder already exists.\n";
        return false; // Folder already exists
    }
}

// Example usage:
$username = $_SESSION['username']; // Replace with the actual username
if (createHosting($mysqli, $username)) {
    // Hosting created successfully
} else {
    // Failed to create hosting
}

// Close connection
$mysqli->close();
?>
