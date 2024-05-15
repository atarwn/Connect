<?php
// Include database connection
include_once 'system/db_connect.php';
session_start();
// Function to get list of users
function getUsers($mysqli) {
    $users = array();

    // SQL to retrieve users
    $sql = "SELECT id, username, avatar, personal_info FROM users";

    // Execute SQL query
    $result = $mysqli->query($sql);

    // Check if users exist
    if ($result && $result->num_rows > 0) {
        // Fetch each row and add to users array
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    return $users;
}

// Get list of users
$users = getUsers($mysqli);

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Users</title>
    <link rel="stylesheet" href="styles.css?v=2">
    <style>
        /* Some styles for table */
        table {
            width: 100%;
            border-collapse: collapse
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left
        }

        img {
            width: 100%;
            height: auto;
        }
        .user {
            width: 20%
        }
        .desc {
            width: 80%
        }
        .footer2 {
            display: none;
        }
    </style>
</head>

<body>
<?php include "bar.php"; ?>
    <h2>List of Users</h2>
    <table>
        <thead>
            <tr>
                <th class="user">User</th>
                <!-- <th>User</th> -->
                <th class="desc">Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td class="user">
                        <a href="user.php?id=<?php echo $user['id'] ?>"><?php echo $user['username']; ?></a>
                        <img src="<?php echo $user['avatar']; ?>">
                    </td>
                    <!-- <td class=""></td> -->
                    <td class="desc"><?php echo $user['personal_info'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php include "footer.php"; ?>
</body>

</html>
