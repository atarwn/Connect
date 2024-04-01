<?php
require "bbcode.php";
$bbcode = new BBCode;
// Initialize session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
} else {
    header('location: user_page.php');
    exit;
}

// Include database connection
include_once 'db_connect.php';

// Retrieve user data from the database
$sql = 'SELECT * FROM users WHERE id = ?';

if ($stmt = $mysqli->prepare($sql)) {
    // Bind parameters to the prepared statement
    $stmt->bind_param('i', $param_id);

    // Set parameters
    $param_id = $_SESSION['id'];

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
        } else {
            // Redirect to error page if user does not exist
            header('location: error.php');
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
    <title>Welcome!</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        
    </style>
</head>

<body>
    <?php include "bar.php"; ?>
    <div class="wrapper">
    <h2><img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar" align="left" width="65" height="65"><?php echo htmlspecialchars($username); ?>'s page</h2>
        <p><?php echo $bbcode->toHTML($personal_info); ?></p>
        <br><hr>
        <h3>Welcome here, <?php echo htmlspecialchars($username); ?>!</h3>
        <p>Welcome to Connect - your gateway to a new era of social networking. Connect with friends, share updates, and discover new connections.</p>
        <p>Fill the fields and join the digital community!</p>
        <form action="update_personal_info.php" method="post">
            <div class="form-group">
                <label>Update textarea:</label><br>
                <textarea name="personal_info" class="form-control" rows="4"><?php echo htmlspecialchars($personal_info); ?></textarea><br>
                <label>Update avatar:</label><br>
                <textarea type="text" name="avatar" class="form-control" rows="1"><?php echo htmlspecialchars($avatar); ?></textarea><br>
                <label>Update custom css:</label><br>
                <textarea name="custom_style" class="form-control" rows="4"><?php echo htmlspecialchars($custom_style); ?></textarea><br>
            </div>
            <p>Now, save your profile:</p>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>
        
    </div>
</body>

</html>
