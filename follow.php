<?php
include 'models/database.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: views/login.php");
    exit();
}


$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header("Location: error.php");
    exit();
}
$user_id = $result->fetch_assoc()['id'];

$username_to_follow = $_POST['username'];

$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username_to_follow);
$stmt->execute();
$user_to_follow = $stmt->get_result();
if ($user_to_follow->num_rows == 0) {
    header("Location: views/error.php");
    exit();
}
$followed_id = $user_to_follow->fetch_assoc()['id'];

$is_following = $conn->prepare("SELECT * FROM follows WHERE follower_id=? AND followed_id=?");
$is_following->bind_param("ii", $user_id, $followed_id);
$is_following->execute();
$is_following_result = $is_following->get_result()->num_rows > 0;

if ($is_following_result) {
    $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id=? AND followed_id=?");
} else {
    $stmt = $conn->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
}
$stmt->bind_param("ii", $user_id, $followed_id);

if ($stmt->execute()) {
    header("Location: views/profile.php?username=" . urlencode($username_to_follow));
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
