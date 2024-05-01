<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Include database connection
include_once 'db_connect.php';

// Check if post ID is provided in the URL
if (!isset($_GET['id'])) {
    header('location: index.php');
    exit;
}

$post_id = $_GET['id'];

// Retrieve the post from the database
$sql = "SELECT * FROM wall WHERE id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('i', $post_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $post = $result->fetch_assoc();
        } else {
            // Post not found
            header('location: index.php');
            exit;
        }
    } else {
        // Error executing query
        echo 'Error retrieving post.';
        exit;
    }
    $stmt->close();
}

// Check if the current user is the author of the post
if ($_SESSION['username'] !== $post['author']) {
    // Redirect to index if not authorized
    header('location: index.php');
    exit;
}

// Check if form is submitted to update the post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];

    // Update the post in the database
    $sql = "UPDATE wall SET post = ?, edited = 1 WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param('si', $content, $post_id);
        if ($stmt->execute()) {
            // Post updated successfully
            header('location: ../index.php');
            exit;
        } else {
            // Error updating post
            echo 'Error updating post.';
        }
        $stmt->close();
    }
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="styles.css?v=2">
</head>

<body>
    <?php include "bar.php"; ?><?php include "system/online.php"; ?>
    <div class="wrapper">
        <h2>Edit Post</h2>
        <form action="postedit.php?id=<?php echo $post_id; ?>" method="post">
            <div class="form-group">
                <textarea name="content" class="form-control" rows="4"><?php echo htmlspecialchars($post['post']); ?></textarea>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Save Changes">
            </div>
        </form>
    </div>
    <?php include "footer.php"; ?>
</body>

</html>
