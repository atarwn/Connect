<?php
require "bbcode.php";
$bbcode = new BBCode;
// Initialize session
session_start();

// Include database connection
include_once 'db_connect.php';

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
    <title><?php echo htmlspecialchars($username); ?>'s page</title>
    <link rel="stylesheet" href="styles.css">
    <style><?php echo htmlspecialchars($custom_style); ?></style>
</head>

<body>
    <?php include "bar.php"; ?>
    <div class="wrapper">
        <h2><img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar" align="left" width="65" height="65"><?php echo htmlspecialchars($username); ?>'s page</h2>
        <p><?php echo $bbcode->toHTML($personal_info); ?></p>
    </div>
    <?php include "footer.php"; ?>
</body>

</html>
