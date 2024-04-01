<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

// Include database connection
include_once 'db_connect.php';

// Function to post a new entry
function postEntry($mysqli, $content) {
    // Prepare SQL statement to insert entry
    $sql = "INSERT INTO wall (author, author_url, avatar_url, post, created_at) VALUES (?, ?, ?, ?, NOW())";

    // Check if the statement can be prepared
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters
        $author = $_SESSION['username'];
        $author_url = 'user.php?id='.$_SESSION['id']; 
        $avatar_url = ''; // Assuming there is no avatar URL stored for now
        $stmt->bind_param('ssss', $author, $author_url, $avatar_url, $content);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Entry posted successfully
            header('Location: index.php');
            exit;
        } else {
            // Error posting entry
            echo "Error posting entry.";
        }
        // Close statement
        $stmt->close();
    }
}

// Check if form is submitted to post a new entry
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST["content"];

    // Validate content
    if (!empty($content)) {
        // Post entry
        postEntry($mysqli, $content);
    } else {
        echo "Content cannot be empty.";
    }
}
?>
