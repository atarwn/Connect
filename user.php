<?php
require "system/bbcode.php";
$bbcode = new BBCode;

// Initialize session
session_start();

// Include database connection
include_once 'system/db_connect.php';

// Retrieve user data from the database
$sql = 'SELECT * FROM users WHERE id = ?';

if ($stmt = $mysqli->prepare($sql)) {
    // Bind parameters to the prepared statement
    $stmt->bind_param('i', $param_id);

    // Set parameters
    $param_id = $_GET['id'];

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        // Store result
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows == 1) {
            // Fetch user data
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $username = $row['username'];
            $avatar = $row['avatar'];
            $custom_style = $row['custom_style'];
            $personal_info = $row['personal_info'];
            $banner_url = ''; // Initialize banner URL variable
            
            // Check if banner URL exists
            $sql_banner = 'SELECT banner_url FROM banners WHERE user_id = ?';
            if ($stmt_banner = $mysqli->prepare($sql_banner)) {
                $stmt_banner->bind_param('i', $param_id);
                if ($stmt_banner->execute()) {
                    $result_banner = $stmt_banner->get_result();
                    if ($result_banner->num_rows == 1) {
                        $row_banner = $result_banner->fetch_array(MYSQLI_ASSOC);
                        $banner_url = $row_banner['banner_url'];
                    }
                }
                $stmt_banner->close();
            }
            
            // Check online status
            $last_visit_time = strtotime($row['lastvisittime']);
            $current_time = time();
            $time_difference = $current_time - $last_visit_time;
            $online = $time_difference <= 300; // User is considered online if last visit time is within last 5 minutes (300 seconds)
        } else {
            // Redirect to error page if user does not exist
            header('location: err/404.php');
            exit;
        }
    } else {
        echo 'Oops! Something went wrong. Please try again later.';
    }

    // Close statement
    $stmt->close();
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($username); ?>'s page</title>
    <link rel="stylesheet" href="styles.css?v=2">
    <style><?php echo htmlspecialchars($custom_style); ?></style>
</head>

<body>
    <?php include "bar.php"; ?>
    <div class="wrapper">
        <p class="online">
            <?php if ($online): ?>
                <span class="status-online" style="color: green;">(Online)</span>
            <?php else: ?>
                <span class="status-offline" style="color: red;">(Offline)</span>
                <span class="lastseen" style="color: gainsboro;">Last seen at <?php echo date('M j, Y H:i:s', $last_visit_time); ?></span>
            <?php endif; ?>
        </p>
        <!-- Display banner -->
        <?php if (!empty($banner_url)): ?>
            <div class="banner">
            <img src="<?php echo htmlspecialchars($banner_url); ?>" alt="Banner" class="banner-img" width="100%">
            </div>
        <?php endif; ?>
        <h2 class="title">
            <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar" align="left" width="65" height="65">
            <?php echo htmlspecialchars($username); ?>'s page
        </h2>
        <p><?php echo $bbcode->toHTML($personal_info); ?></p>
    </div>
    <?php include "footer.php"; ?>
</body>

</html>
