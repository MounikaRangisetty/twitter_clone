<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Tweet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Tweet</h1>
        <?php include('topNavigation.php'); ?>
        <br>
        <nav>
            <a href="profile.php?username=<?php echo urlencode($username); ?>">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <br>
    <div class="container">
        <h2>Post a New Tweet</h2>
        <form method="post" enctype="multipart/form-data">
            <textarea name="tweet" rows="4" placeholder="What's happening?" required></textarea>
            <input type="file" name="image">
            <button type="submit">Tweet</button>
        </form>

        <h2>Your Tweets and Tweets from People You Follow</h2>
        <?php while ($tweet = $tweets->fetch_assoc()) { ?>
            <div class="tweet">
                <p><strong><?php echo htmlspecialchars($tweet['username']); ?>:</strong> <?php echo htmlspecialchars($tweet['content']); ?></p>
                <?php if ($tweet['image']) { ?>
                    <img src="images/<?php echo htmlspecialchars($tweet['image']); ?>" alt="Tweet Image">
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>