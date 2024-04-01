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

// Check if post ID is provided
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Prepare SQL statement to delete post
    $sql = "DELETE FROM wall WHERE id = ? AND author = ?";

    // Check if the statement can be prepared
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param('is', $post_id, $_SESSION['username']);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Post deleted successfully
            header('location: index.php');
            exit;
        } else {
            // Error deleting post
            echo "Error deleting post.";
        }
        // Close statement
        $stmt->close();
    }
}

// Close connection
$mysqli->close();
?>
