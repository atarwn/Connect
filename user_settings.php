<?php
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

// Define variables and initialize with empty values
$email = $new_password = $confirm_password = '';
$email_err = $new_password_err = $confirm_password_err = '';

// Processing form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } else {
        $email = trim($_POST['email']);
    }

    // Validate new password
    if (!empty($_POST['new_password'])) {
        if (strlen($_POST['new_password']) < 6) {
            $new_password_err = 'Password must be at least 6 characters long.';
        } else {
            $new_password = trim($_POST['new_password']);
        }
    }

    // Validate confirm password
    if (!empty($_POST['confirm_password'])) {
        if ($_POST['confirm_password'] !== $new_password) {
            $confirm_password_err = 'Passwords do not match.';
        } else {
            $confirm_password = trim($_POST['confirm_password']);
        }
    }

    // Check input errors before updating the database
    if (empty($email_err) && empty($new_password_err) && empty($confirm_password_err)) {
        // Prepare an update statement
        $sql = 'UPDATE users SET email = ?';

        // Add password update if new password is provided
        if (!empty($new_password)) {
            $sql .= ', password = ?';
        }

        $sql .= ' WHERE id = ?';

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param('s', $param_email);
            $param_email = $email;

            // Add password binding if new password is provided
            if (!empty($new_password)) {
                $stmt->bind_param('s', $param_password);
                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            }

            $stmt->bind_param('i', $param_id);
            $param_id = $_SESSION['id'];

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Password updated successfully, destroy the session and redirect to login page
                session_destroy();
                header('location: login.php');
                exit;
            } else {
                echo 'Oops! Something went wrong. Please try again later.';
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include "bar.php"; ?>
    <div class="wrapper">
        <h2>User Settings</h2>
        <p>Please fill in the form to update your email and/or password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="user.php?id=<?php echo $_SESSION['id']; ?>">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>
