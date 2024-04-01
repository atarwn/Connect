<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include "bar.php"; ?>
    <div class="wrapper">
        <h2><img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar" align="left" width="65" height="65"><?php echo htmlspecialchars($username); ?>'s page</h2>
        <p><?php echo $bbcode->toHTML($personal_info); ?></p>
    </div>
</body>

</html>
