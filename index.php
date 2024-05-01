<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Include database connection
include_once 'system/db_connect.php';

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
    <link rel="stylesheet" href="styles.css?v=2">
</head>

<body>
<?php include "bar.php";?>
    <div class="wrapper">
        <h2>Wall</h2>
        <form action="system/post.php" method="post">
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
                        [ <a href="system/postedit.php?id=<?php echo $entry['id']; ?>">Edit</a> |
                        <a href="system/postremove.php?id=<?php echo $entry['id']; ?>">Delete</a> ]
                    <?php endif; ?>
                    <?php if ($_SESSION['username'] != $entry['author'] && $_SESSION['ismod'] == 1): ?>
                        [ <a href="system/postremove.php?id=<?php echo $entry['id']; ?>">Delete</a> ]
                    <?php endif; ?>
                    <br>
                    <?php echo nl2br(htmlspecialchars($entry['post'])); ?>
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
    <?php include "system/online.php"; ?>
</body>

</html>
