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
include_once 'system/db_connect.php';

// Define variables and initialize with empty values
$email = $new_password = $confirm_password = $old_password = '';
$email_err = $new_password_err = $confirm_password_err = $old_password_err = '';

// Processing form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // If email form is submitted
    if (isset($_POST['email_form'])) {
        // Validate email
        if (empty(trim($_POST['email']))) {
            $email_err = 'Please enter your email.';
        } else {
            $email = trim($_POST['email']);
        }

        // Validate old password
        if (empty(trim($_POST['old_password']))) {
            $old_password_err = 'Please enter your old password.';
        } else {
            $old_password = trim($_POST['old_password']);
        }

        // Check input errors before updating the database
        if (empty($email_err) && empty($old_password_err)) {
            // Prepare a select statement to retrieve the user's password
            $sql = 'SELECT password FROM users WHERE id = ?';

            if ($stmt = $mysqli->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param('i', $param_id);
                $param_id = $_SESSION['id'];

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Store result
                    $stmt->store_result();

                    // Check if email exists, if yes then verify password
                    if ($stmt->num_rows == 1) {
                        // Bind result variables
                        $stmt->bind_result($hashed_password);
                        if ($stmt->fetch()) {
                            if (password_verify($old_password, $hashed_password)) {
                                // Password is correct, update the email
                                $stmt->close();
                                // Prepare an update statement
                                $sql = 'UPDATE users SET email = ? WHERE id = ?';

                                if ($stmt = $mysqli->prepare($sql)) {
                                    // Bind parameters
                                    $stmt->bind_param('si', $param_email, $param_id);
                                    $param_email = $email;

                                    // Attempt to execute the prepared statement
                                    if ($stmt->execute()) {
                                        // Redirect to user page
                                        header('location: user.php?id=' . $_SESSION['id']);
                                        exit;
                                    } else {
                                        echo 'Oops! Something went wrong. Please try again later.';
                                    }

                                    // Close statement
                                    $stmt->close();
                                }
                            } else {
                                $old_password_err = 'The old password you entered is incorrect.';
                            }
                        }
                    }
                } else {
                    echo 'Oops! Something went wrong. Please try again later.';
                }

                // Close statement
                $stmt->close();
            }
        }
    }

    // If password form is submitted
    if (isset($_POST['password_form'])) {
        // Process password update
        // Define variables and initialize with empty values
        $new_password = $confirm_password = '';
        $new_password_err = $confirm_password_err = '';

        // Validate new password
        if (empty($_POST['new_password'])) {
            $new_password_err = 'Please enter your new password.';
        } elseif (strlen($_POST['new_password']) < 6) {
            $new_password_err = 'Password must be at least 6 characters long.';
        } else {
            $new_password = trim($_POST['new_password']);
        }

        // Validate confirm password
        if (empty($_POST['confirm_password'])) {
            $confirm_password_err = 'Please confirm your password.';
        } else {
            $confirm_password = trim($_POST['confirm_password']);
            if ($new_password != $confirm_password) {
                $confirm_password_err = 'Passwords do not match.';
            }
        }

        // Validate old password
        if (empty(trim($_POST['old_password']))) {
            $old_password_err = 'Please enter your old password.';
        } else {
            $old_password = trim($_POST['old_password']);
        }

        // Check input errors before updating the database
        if (empty($new_password_err) && empty($confirm_password_err) && empty($old_password_err)) {
            // Prepare a select statement to retrieve the user's password
            $sql = 'SELECT password FROM users WHERE id = ?';

            if ($stmt = $mysqli->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param('i', $param_id);
                $param_id = $_SESSION['id'];

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Store result
                    $stmt->store_result();

                    // Check if email exists, if yes then verify password
                    if ($stmt->num_rows == 1) {
                        // Bind result variables
                        $stmt->bind_result($hashed_password);
                        if ($stmt->fetch()) {
                            if (password_verify($old_password, $hashed_password)) {
                                // Password is correct, update the password
                                $stmt->close();
                                // Prepare an update statement
                                $sql = 'UPDATE users SET password = ? WHERE id = ?';

                                if ($stmt = $mysqli->prepare($sql)) {
                                    // Bind parameters
                                    $stmt->bind_param('si', $param_password, $param_id);
                                    $param_password = password_hash($new_password, PASSWORD_DEFAULT);

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
                            } else {
                                $old_password_err = 'The old password you entered is incorrect.';
                            }
                        }
                    }
                } else {
                    echo 'Oops! Something went wrong. Please try again later.';
                }

                // Close statement
                $stmt->close();
            }
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
    <link rel="stylesheet" href="styles.css?v=2">
</head>

<body>
    <?php include "bar.php"; ?>
    <div class="wrapper">
        <h2>User Settings</h2>
        <div>
            <h3>Update Email</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($old_password_err)) ? 'has-error' : ''; ?>">
                    <label>Old Password</label>
                    <input type="password" name="old_password" value="<?php echo $old_password; ?>">
                    <span class="help-block"><?php echo $old_password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="hidden" name="email_form" value="1">
                    <input type="submit" class="btn btn-primary" value="Update Email">
                </div>
            </form>
        </div>
        <div>
            <h3>Update Password</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                    <label>New Password</label>
                    <input type="password" name="new_password" value="">
                    <span class="help-block"><?php echo $new_password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" value="">
                    <span class="help-block"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($old_password_err)) ? 'has-error' : ''; ?>">
                    <label>Old Password</label>
                    <input type="password" name="old_password" value="<?php echo $old_password; ?>">
                    <span class="help-block"><?php echo $old_password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="hidden" name="password_form" value="1">
                    <input type="submit" class="btn btn-primary" value="Update Password">
                </div>
            </form>
        </div>
    </div>
<?php include "footer.php"; ?>
</body>

</html>
