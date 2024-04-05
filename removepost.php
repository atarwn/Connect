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

function getEntries($mysqli) {
    $entries = array();

    // SQL to retrieve entries
    $sql = "SELECT * FROM wall ORDER BY created_at DESC";

    // Execute SQL query
    $result = $mysqli->query($sql);

    // Check if entries exist
    if ($result && $result->num_rows > 0) {
        // Fetch each row and add to entries array
        while ($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }
    }

    return $entries;
}

$entries = getEntries($mysqli);

// Check if post ID is provided and user has permission to delete the post
if (isset($_GET['id']) && !empty($entries)) {
    $post_id = $_GET['id'];
    $author = '';

    // Find the entry with the provided post ID
    foreach ($entries as $entry) {
        if ($entry['id'] == $post_id) {
            $author = $entry['author'];
            break;
        }
    }

    // Check if user is the author of the post or a moderator
    if ($_SESSION['username'] == $author || $_SESSION['ismod'] == 1) {
        // Prepare SQL statement to delete post
        $sql = "DELETE FROM wall WHERE id = ? AND author = ?";

        // Check if the statement can be prepared
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param('is', $post_id, $author);

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
    } else {
        // User does not have permission to delete the post
        echo "You do not have permission to delete this post.";
    }
}

// Close connection
$mysqli->close();
?>
