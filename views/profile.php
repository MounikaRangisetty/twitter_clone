<?php
include '../models/database.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_GET['username'];
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: error.php");
    exit();
}

$user_id = $user['id'];
$logged_in_user = $_SESSION['username'];

$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $logged_in_user);
$stmt->execute();
$logged_in_user_id = $stmt->get_result()->fetch_assoc()['id'];

// Check if the logged-in user is following this profile user
$stmt = $conn->prepare("SELECT * FROM follows WHERE follower_id=? AND followed_id=?");
$stmt->bind_param("ii", $logged_in_user_id, $user_id);
$stmt->execute();
$is_following = $stmt->get_result()->num_rows > 0;

// Get number of followers
$stmt = $conn->prepare("SELECT COUNT(*) AS follower_count FROM follows WHERE followed_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$follower_count = $stmt->get_result()->fetch_assoc()['follower_count'];

// Get number of users being followed
$stmt = $conn->prepare("SELECT COUNT(*) AS following_count FROM follows WHERE follower_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$following_count = $stmt->get_result()->fetch_assoc()['following_count'];

// Get number of posts
$stmt = $conn->prepare("SELECT COUNT(*) AS post_count FROM tweets WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$post_count = $stmt->get_result()->fetch_assoc()['post_count'];

// Get tweets for the profile page
$stmt = $conn->prepare("SELECT * FROM tweets WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tweets = $stmt->get_result();

// Get the list of users whom the profile user is following (for later use)
$stmt = $conn->prepare("
    SELECT users.id, users.username 
    FROM follows 
    JOIN users ON follows.followed_id = users.id 
    WHERE follows.follower_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$following_users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($username); ?>'s Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .following-list {
            display: none;
            margin-top: 20px;
        }
        .following-list p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($username); ?>'s Profile</h1>
        <nav>
            <a href="../index.php">Home</a>
            <a href="logout.php">Logout</a><a></a>
        </nav>
    </header>
    <br>
    <div class="container">
        <div class="profile-stats">
            <p><strong>Followers:</strong> <?php echo htmlspecialchars($follower_count); ?></p>
            <p><a href="#" onclick="toggleFollowingList();" style="text-decoration:none"><strong>Following:</strong> <?php echo htmlspecialchars($following_count); ?></a></p>
            <p><strong>Posts:</strong> <?php echo htmlspecialchars($post_count); ?></p>
        </div>

        <!-- Following Users List -->
        <div class="following-list" id="following-list">
            <h2>Following Users</h2>
            <?php while ($following = $following_users->fetch_assoc()) { ?>
                <div class="user-profile">
                    <p>
                        <a href="profile.php?username=<?php echo htmlspecialchars($following['username']); ?>">
                            <strong><?php echo htmlspecialchars($following['username']); ?></strong>
                        </a>
                    </p>
                </div>
            <?php } ?>
        </div>

        <?php if ($username != $_SESSION['username']) { ?>
            <form method="post" action="../follow.php" style="margin-bottom: 20px;">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <button type="submit" style="padding: 10px 20px; border: none; background-color: <?php echo $is_following ? 'red' : '#1da1f2'; ?>; color: white; border-radius: 5px;">
                    <?php echo $is_following ? 'Unfollow' : 'Follow'; ?>
                </button>
            </form>
        <?php } ?>

        <h2>Tweets</h2>
        <?php if ($tweets->num_rows > 0) { ?>
            <?php while ($tweet = $tweets->fetch_assoc()) { ?>
                <div class="tweet">
                    <?php if ($tweet['image']) { ?>
                        <img src="../images/<?php echo htmlspecialchars($tweet['image']); ?>" alt="Tweet Image">
                    <?php } ?>
                    <p><?php echo htmlspecialchars($tweet['content']); ?></p>
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) AS likes FROM likes WHERE tweet_id=?");
                    $stmt->bind_param("i", $tweet['id']);
                    $stmt->execute();
                    $likes = $stmt->get_result()->fetch_assoc()['likes'];

                    // Check if the current user has liked the tweet
                    $stmt = $conn->prepare("SELECT * FROM likes WHERE tweet_id=? AND user_id=?");
                    $stmt->bind_param("ii", $tweet['id'], $logged_in_user_id);
                    $stmt->execute();
                    $is_liked = $stmt->get_result()->num_rows > 0;
                    ?>
                    <span><?php echo $likes; ?></span>
                    <a href="../like.php?tweet_id=<?php echo $tweet['id']; ?>">
                        <i class="fa<?php echo $is_liked ? 's' : 'r'; ?> fa-thumbs-up"></i>
                    </a>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No tweets to show.</p>
        <?php } ?>
    </div>

    <script>
        function toggleFollowingList() {
            var list = document.getElementById('following-list');
            list.style.display = list.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
