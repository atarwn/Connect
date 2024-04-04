<?php
// Initialize session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Include database connection
include_once 'db_connect.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $new_personal_info = htmlspecialchars($_POST['personal_info']);
    $new_avatar = trim($_POST['avatar']);
    $new_custom_style = trim($_POST['custom_style']);
    $new_email = trim($_POST['email']);

    // Prepare update statement
    $sql = "UPDATE users SET personal_info = ?, avatar = ?, custom_style = ?, email = ? WHERE id = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("ssssi", $param_personal_info, $param_avatar, $param_custom_style, $param_id);

        // Set parameters
        $param_personal_info = $new_personal_info;
        $param_avatar = $new_avatar;
        $param_custom_style = $new_custom_style;
        $param_email = $new_email;
        $param_id = $_SESSION['id'];

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to user page with success message
            $_SESSION['success_message'] = "Personal information updated successfully";
            header("Location: user_page.php");
            exit;
        } else {
            // Redirect to user page with error message
            $_SESSION['error_message'] = "Oops! Something went wrong. Please try again later.";
            header("Location: user_page.php");
            exit;
        }

        // Close statement
        $stmt->close();
    } else {
        // Redirect to user page with error message if prepared statement fails
        $_SESSION['error_message'] = "Oops! Something went wrong. Please try again later.";
        header("Location: user_page.php");
        exit;
    }
} else {
    // Redirect to user page if accessed directly without POST method
    header("Location: user_page.php");
    exit;
}
?>