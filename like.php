<?php
include './models/database.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ./views/login.php");
    exit();
}

$tweet_id = $_GET['tweet_id'];
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_id = $stmt->get_result()->fetch_assoc()['id'];

$stmt = $conn->prepare("SELECT * FROM likes WHERE user_id=? AND tweet_id=?");
$stmt->bind_param("ii", $user_id, $tweet_id);
$stmt->execute();
$already_liked = $stmt->get_result()->num_rows > 0;

if ($already_liked) {
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id=? AND tweet_id=?");
} else {
    $stmt = $conn->prepare("INSERT INTO likes (user_id, tweet_id) VALUES (?, ?)");
}

$stmt->bind_param("ii", $user_id, $tweet_id);
if ($stmt->execute()) {
    $referer = $_SERVER['HTTP_REFERER'];
    header("Location: $referer");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
