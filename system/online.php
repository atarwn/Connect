<?php
// Include database connection
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check if user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Get user ID from session
    $user_id = $_SESSION['id'];

    // Update last visit time in the database
    $sql = "UPDATE users SET lastvisittime = NOW() WHERE id = ?";
    
    // Prepare the SQL statement
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param('i', $user_id);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Last visit time updated successfully
            // You can handle this case as needed
        } else {
            // Error executing the prepared statement
            echo 'Oops! Something went wrong while updating last visit time.';
        }

        // Close statement
        $stmt->close();
    } else {
        // Error preparing SQL statement
        echo 'Oops! Something went wrong while preparing the SQL statement.';
    }
}
?>
