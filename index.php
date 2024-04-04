<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

// Include database connection
include_once 'db_connect.php';

// Function to retrieve entries from the database
function getEntries($mysqli) {
    $entries = array();

    // SQL to retrieve entries
    $sql = "SELECT * FROM wall ORDER BY created_at DESC";

    // Execute SQL query
    $result = $mysqli->query($sql);

    // Check if entries exist
    if ($result && $result->num_rows > 0) {
        // Fetch each row and add to entries array
        while ($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }
    }

    return $entries;
}

// Function to post a new entry
function postEntry($mysqli, $content) {
    // Prepare SQL statement to insert entry
    $sql = "INSERT INTO wall (author, author_url, avatar_url, post, created_at) VALUES (?, ?, ?, ?, NOW())";

    // Check if the statement can be prepared
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters
        $author = $_SESSION['username'];
        $author_url = 'user.php?id='.$_SESSION['id']; // Assuming there is no author URL stored for now
        $avatar_url = ''; // Assuming there is no avatar URL stored for now
        $stmt->bind_param('ssss', $author, $author_url, $avatar_url, $content);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Entry posted successfully
            return true;
        } else {
            // Error posting entry
            return false;
        }
        // Close statement
        $stmt->close();
    }
}

// Check if form is submitted to post a new entry
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST["content"];

    // Validate content
    if (!empty($content)) {
        // Post entry
        postEntry($mysqli, $content);
    }
}

// Retrieve entries
$entries = getEntries($mysqli);

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wall</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include "bar.php"; ?>
    <div class="wrapper">
        <h2>Wall</h2>
        <form action="post.php" method="post">
            <div class="form-group">
                <textarea name="content" class="form-control" rows="4" placeholder="Write something..."></textarea>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Post">
            </div>
        </form>
        
        <h3>Recent Entries</h3>
        <?php if (!empty($entries)): ?>
            <ul class="entry-list">
                <?php foreach ($entries as $entry): ?>
                    <li class="entry">
                        <strong><a href="<?php echo htmlentities($entry['author_url']);?>"><?php echo htmlspecialchars($entry['author']); ?></a></strong>
                        <span class="entry-time"><?php echo date('M j, Y H:i', strtotime($entry['created_at'])); ?></span>
                        <?php if ($_SESSION['username'] == $entry['author']): ?>
                            <!-- Only display edit and delete links if current user is the author of the post -->
                            <a href="editpost.php?id=<?php echo $entry['id']; ?>">Edit</a> |
                            <a href="removepost.php?id=<?php echo $entry['id']; ?>">Delete</a>
                        <?php endif; ?>
                        <br>
                        <?php echo htmlspecialchars($entry['post']); ?>
                        <?php if ($entry['edited']): ?>
                            <span style="color: gray; font-size: small;">(edited)</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No entries yet.</p>
        <?php endif; ?>
    </div>
    <?php include "footer.php"; ?>
</body>

</html>
