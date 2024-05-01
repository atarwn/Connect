<?php
include_once 'system/db_connect.php';
$sql_count_users = "SELECT COUNT(*) FROM users";
if ($result = $mysqli->query($sql_count_users)) {
    $row = $result->fetch_row();
    $user_count = $row[0];
    $result->free();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css?v=2">
</head>

<body>
    [Connect] <a href="">Main</a> | <a href="">Log in</a>
	<hr>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Your email</label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                <span class="help-block"><?php echo htmlspecialchars($email_err); ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" readonly>
                <span class="help-block"><?php echo htmlspecialchars($password_err); ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="">Sign up now</a>.</p>
        </form>
        <hr>
        <!-- Add this line to display the user count -->
        <p>There are currently <?php echo htmlspecialchars($user_count); ?> registered users</p>
    </div>
    <?php include "footer.php"; ?>
</body>

</html>