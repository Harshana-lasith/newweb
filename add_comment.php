<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to create a post.'); window.location.href = 'login.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "forumdb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $post_id = (int)$_POST['post_id'];
    $parent_id = isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : 'NULL';
    $comment = $conn->real_escape_string($_POST['comment']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session

    $commenter_result = $conn->query("SELECT username FROM users WHERE id = $user_id");
    $commenter_name = $commenter_result->fetch_assoc()['username'];

    $conn->query("INSERT INTO comments (post_id, user_id, comment, parent_id) VALUES ($post_id, $user_id, '$comment', $parent_id)");
    header("Location: post.php?id=$post_id");
    exit();

    if ($user_id && $post_id && $comment) {
        // Insert the comment
        $conn->query("INSERT INTO comments (user_id, post_id, content) VALUES ($user_id, $post_id, '$comment')");
    
        // Fetch the post owner's user_id
        $post_owner_result = $conn->query("SELECT user_id FROM posts WHERE id = $post_id");
        if ($post_owner_result->num_rows > 0) {
            $post_owner = $post_owner_result->fetch_assoc()['user_id'];
    
            // Insert a notification for the post owner
            if ($post_owner != $user_id) { // Avoid notifying the user if they comment on their own post
                $message = "$commenter_name commented on your post.";
                $conn->query("INSERT INTO notifications (user_id, post_id, message) VALUES ($post_owner, $post_id, '$message')");
            }
        }
        echo json_encode(['success' => true, 'message' => 'Comment added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
}
$conn->close();
?>