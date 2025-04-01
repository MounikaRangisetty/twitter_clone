<?php
include './models/database.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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

// Get tweets for the profile page along with like counts
$stmt = $conn->prepare("
    SELECT tweets.id, tweets.content, tweets.image, tweets.created_at,
           (SELECT COUNT(*) FROM likes WHERE likes.tweet_id = tweets.id) AS likes_count,
           (SELECT COUNT(*) > 0 FROM likes WHERE likes.tweet_id = tweets.id AND likes.user_id = ?) AS is_liked
    FROM tweets 
    WHERE tweets.user_id=? 
    ORDER BY tweets.created_at DESC
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$tweets_result = $stmt->get_result();
$tweets = [];
while ($tweet = $tweets_result->fetch_assoc()) {
    $tweets[] = $tweet;
}

// Get the list of users whom the profile user is following
$stmt = $conn->prepare("
    SELECT users.id, users.username 
    FROM follows 
    JOIN users ON follows.followed_id = users.id 
    WHERE follows.follower_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$following_users_result = $stmt->get_result();
$following_users = [];
while ($user = $following_users_result->fetch_assoc()) {
    $following_users[] = $user;
}

// Return data as an associative array
$data = [
    'username' => $username,
    'follower_count' => $follower_count,
    'following_count' => $following_count,
    'post_count' => $post_count,
    'tweets' => $tweets,
    'following_users' => $following_users
];

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
