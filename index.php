<?php
include './models/database.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: /views/login.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: ./views/error.php");
    exit();
}

$user_id = $user['id'];

// Handle tweet submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tweet'])) {
    $tweet_content = trim($_POST['tweet']);
    $image = $_FILES['image']['name'];

    if (!empty($tweet_content)) {
        // Handle file upload
        if ($image) {
            $target_dir = "images/";
            $target_file = $target_dir . basename($image);
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        // Insert new tweet into the database
        $stmt = $conn->prepare("INSERT INTO tweets (user_id, content, image, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $tweet_content, $image);
        $stmt->execute();
    }

    // Redirect to avoid form resubmission
    header("Location: index.php");
    exit();
}

// Get tweets from users the current user follows
$stmt = $conn->prepare("
    SELECT tweets.*, users.username 
    FROM tweets 
    JOIN users ON tweets.user_id = users.id 
    WHERE tweets.user_id IN (
        SELECT followed_id FROM follows WHERE follower_id = ?
    ) 
    ORDER BY tweets.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tweets = $stmt->get_result();

// Get profiles of users the current user is not following
$stmt = $conn->prepare("
    SELECT users.id, users.username 
    FROM users 
    WHERE users.id != ? 
    AND users.id NOT IN (
        SELECT followed_id FROM follows WHERE follower_id = ?
    )
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$non_followed_users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tweet</title>
    <link rel="stylesheet" href="./views/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tweet-form {
            background-color: #f5f8fa;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
        }
        
        .tweet-form h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        
        .tweet-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccd6dd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        
        .tweet-form input[type="file"] {
            margin-bottom: 10px;
        }
        
        .tweet-form button {
            background-color: #1da1f2;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
        }
        
        .tweet-form button:hover {
            background-color: #1991db;
        }
        
        .tweet {
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
        }

        .tweet img {
            max-width: 100%;
            border-radius: 8px;
        }

        .tweett {
            background-color: #f5f8fa;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Tweet</h1>
        <nav>
            <a href="./views/user_profile.php">Profile</a>
            <a href="./views/logout.php">Logout</a><a></a>
        </nav>
    </header>

    <div class="container">
       
        <div class="tweet-form">
            <h2>Create a New Tweet</h2>
            <form action="index.php" method="POST" enctype="multipart/form-data">
                <textarea name="tweet" rows="4" placeholder="What's happening?" required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit">Tweet</button>
            </form>
        </div>

        <h2>Latest Tweets from Users You Follow</h2>

        <?php if ($tweets->num_rows > 0) { ?>
            <?php while ($tweet = $tweets->fetch_assoc()) { 
                // Check if the current user has liked this tweet
                $stmt = $conn->prepare("SELECT * FROM likes WHERE tweet_id=? AND user_id=?");
                $stmt->bind_param("ii", $tweet['id'], $user_id);
                $stmt->execute();
                $is_liked = $stmt->get_result()->num_rows > 0;
                
                // Get like count
                $stmt = $conn->prepare("SELECT COUNT(*) AS likes FROM likes WHERE tweet_id=?");
                $stmt->bind_param("i", $tweet['id']);
                $stmt->execute();
                $likes = $stmt->get_result()->fetch_assoc()['likes'];
            ?>
                <div class="tweet">
                    <p>
                        <strong>
                            <a href="./views/profile.php?username=<?php echo htmlspecialchars($tweet['username']); ?>">
                                <?php echo htmlspecialchars($tweet['username']); ?>
                            </a>
                        </strong>
                    </p>
                    <?php if ($tweet['image']) { ?>
                        <img src="images/<?php echo htmlspecialchars($tweet['image']); ?>" alt="Tweet Image">
                    <?php } ?>
                    <p><?php echo htmlspecialchars($tweet['content']); ?></p>
                    <p>
                        <span><?php echo $likes; ?></span>
                        <a href="like.php?tweet_id=<?php echo $tweet['id']; ?>">
                            <i class="fa<?php echo $is_liked ? 's' : 'r'; ?> fa-thumbs-up"></i>
                        </a>
                    </p>
                </div>
            <?php } ?>
        <?php } else { ?><br>
        <p>No tweets to show.</p><br>
        <?php } ?>


        <h2>Users You Might Want to Follow</h2>
        
        <?php while ($user = $non_followed_users->fetch_assoc()) { ?>
            <div class="tweett">
                <p>
                    <?php echo htmlspecialchars($user['username']); ?>
                </p>
                <a href="./views/profile.php?username=<?php echo htmlspecialchars($user['username']); ?>">
                    View
                </a>
            </div>
        <?php } ?>
    </div>
</body>
</html>
