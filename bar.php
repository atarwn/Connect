<?php
if (isset($_SESSION['loggedin'])) {
    $link = '<a href="user_page.php">Your page</a> | <a href="list.php">Explore</a> | <a href="logout.php">Logout</a>';
} else {
    $link = '<a href="login.php">Log in</a>';
}
?>

[Connect] <a href="index.php">Main</a> | <?php echo $link; ?>
<hr>