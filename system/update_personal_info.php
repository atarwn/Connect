<?php
// Initialize session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Include database connection
include_once 'db_connect.php';

// Validate and sanitize input
$new_personal_info = htmlspecialchars($_POST['personal_info']);
$new_avatar = trim($_POST['avatar']);
$new_custom_style = trim($_POST['custom_style']);
$banner_url = $_POST['banner_url'];

// Prepare update statement for user profile
$sql_update_profile = "UPDATE users SET personal_info = ?, avatar = ?, custom_style = ? WHERE id = ?";

if ($stmt_update_profile = $mysqli->prepare($sql_update_profile)) {
    // Bind parameters to the prepared statement
    $stmt_update_profile->bind_param("sssi", $param_personal_info, $param_avatar, $param_custom_style, $param_id);

    // Set parameters
    $param_personal_info = $new_personal_info;
    $param_avatar = $new_avatar;
    $param_custom_style = $new_custom_style;
    $param_id = $_SESSION['id'];

    // Attempt to execute the prepared statement to update user profile
    if ($stmt_update_profile->execute()) {
        // Check if the user already has a banner
        $sql_check_banner = "SELECT id FROM banners WHERE user_id = ?";
        if ($stmt_check_banner = $mysqli->prepare($sql_check_banner)) {
            // Bind parameters
            $stmt_check_banner->bind_param('i', $_SESSION['id']);

            // Attempt to execute the prepared statement
            if ($stmt_check_banner->execute()) {
                $stmt_check_banner->store_result();
                // If a banner exists, update it; otherwise, insert a new banner
                if ($stmt_check_banner->num_rows > 0) {
                    $sql_update_banner = "UPDATE banners SET banner_url = ? WHERE user_id = ?";
                    if ($stmt_update_banner = $mysqli->prepare($sql_update_banner)) {
                        // Bind parameters
                        $stmt_update_banner->bind_param('si', $banner_url, $_SESSION['id']);

                        // Attempt to execute the prepared statement to update the banner
                        if ($stmt_update_banner->execute()) {
                            $_SESSION['success_message'] = "Profile updated successfully and banner replaced.";
                            header("Location: ../user_page.php");
                            exit;
                        } else {
                            $_SESSION['error_message'] = "Error updating banner.";
                            header("Location: ../user_page.php");
                            exit;
                        }
                        // Close statement
                        $stmt_update_banner->close();
                    }
                } else {
                    // If no banner exists, insert a new banner
                    $sql_insert_banner = "INSERT INTO banners (user_id, banner_url) VALUES (?, ?)";
                    if ($stmt_insert_banner = $mysqli->prepare($sql_insert_banner)) {
                        // Bind parameters
                        $stmt_insert_banner->bind_param('is', $_SESSION['id'], $banner_url);

                        // Attempt to execute the prepared statement to insert the new banner
                        if ($stmt_insert_banner->execute()) {
                            $_SESSION['success_message'] = "Profile updated successfully and banner added.";
                            header("Location: ../user_page.php");
                            exit;
                        } else {
                            $_SESSION['error_message'] = "Error adding banner.";
                            header("Location: ../user_page.php");
                            exit;
                        }
                        // Close statement
                        $stmt_insert_banner->close();
                    }
                }
            } else {
                $_SESSION['error_message'] = "Error checking banner.";
                header("Location: ../user_page.php");
                exit;
            }
            // Close statement
            $stmt_check_banner->close();
        }
    } else {
        $_SESSION['error_message'] = "Oops! Something went wrong while updating profile.";
        header("Location: ../user_page.php");
        exit;
    }
    // Close statement
    $stmt_update_profile->close();
} else {
    $_SESSION['error_message'] = "Oops! Something went wrong while updating profile.";
    header("Location: ../user_page.php");
    exit;
}

// Close connection
$mysqli->close();
?>
