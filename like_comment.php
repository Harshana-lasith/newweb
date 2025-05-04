<?php
session_start();
$conn = new mysqli("localhost", "root", "", "forumdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to like or unlike a comment.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user already liked the comment
    $stmt = $conn->prepare("SELECT * FROM comment_likes WHERE comment_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the user already liked the comment, remove the like
        $stmt = $conn->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $comment_id, $user_id);
        if ($stmt->execute()) {
            echo "Like removed.";
        } else {
            echo "Error removing like.";
        }
    } else {
        // If the user hasn't liked the comment, add the like
        $stmt = $conn->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $comment_id, $user_id);
        if ($stmt->execute()) {
            echo "Comment liked successfully!";
        } else {
            echo "Error liking the comment.";
        }
    }

    $stmt->close();
}

$conn->close();
?>