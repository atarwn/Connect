<?php
require "bbcode.php";
$bbcode = new BBCode;
// Initialize session
session_start();

// Check if user is not logged in
if (!isset($_SESSION['loggedin'])) {
    // Redirect to login page
    header('location: login.php');
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
            $personal_info = $row['personal_info'];
            $email = $row['email'];
            $custom_style = $row['custom_style'];
            
        } else {
            // Redirect to error page if user does not exist
            header('location: login.php');
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
    <title>Your Page</title>
    <link rel="stylesheet" href="styles.css">
    <style><?php echo htmlspecialchars($custom_style); ?></style>
</head>

<body>
    <?php include "bar.php"; ?>
    <div class="wrapper">
    <h1>Welcome here, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>You can find your public page <a href="user.php?id=<?php echo $_SESSION['id']; ?>">here</a></p>
        <hr>
        <!--<h3><em>Page preview - id <?php echo $_SESSION['id']; ?></em></h3>-->
        
        <h2 class="title"><img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar" align="left" width="65" height="65"><?php echo htmlspecialchars($username); ?>'s page</h2>
        <p><?php echo $bbcode->toHTML($personal_info); ?></p>
        <hr>
        <h3>Update profile</h3>
        <p>Confused? Then you should visit our <a href="help.htm">help page</a></p>
        <form action="update_personal_info.php" method="post">
            <div class="form-group">
                <label>Update avatar:</label><br>
                <textarea type="text" name="avatar" class="form-control" rows="1"><?php echo htmlspecialchars($avatar); ?></textarea><br>
                <label>Update textarea:</label><br>
                <textarea name="personal_info" class="form-control" rows="4"><?php echo htmlspecialchars($personal_info); ?></textarea><br>
                <label>Update custom css:</label><br>
                <textarea name="custom_style" class="form-control" rows="4"><?php echo htmlspecialchars($custom_style); ?></textarea><br>
                <input type="submit" class="btn btn-primary" value="Save">
        
            </div>
        </form>
            <br>
        <a href="user_account.php">Update account</a>
        
    </div>
    <?php include "footer.php"; ?>
</body>

</html>
