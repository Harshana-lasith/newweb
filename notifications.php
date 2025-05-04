<?php
session_start();
$conn = new mysqli("localhost", "root", "", "forumdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to view notifications.'); window.location.href = 'login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch comments on the user's posts
$sql = "SELECT comments.comment, comments.created_at, posts.title , users.username AS commenter_name
        FROM comments 
        INNER JOIN posts ON comments.post_id = posts.id 
        INNER JOIN users ON comments.user_id = users.id
        WHERE posts.user_id = ? 
        ORDER BY comments.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
        .container { max-width: 800px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; }
        .notification { border-bottom: 1px solid #ddd; padding: 10px 0; }
        .notification:last-child { border-bottom: none; }
        .notification p { margin: 5px 0; }
        .notification .title { font-weight: bold; color: #007BFF; }
        .notification .time { font-size: 12px; color: #888; }
    </style>
</head>
<body>

<div class="container">
    <h2>Notifications</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification">
            <p class="title"><?php echo htmlspecialchars($row['commenter_name']); ?> commented on your post "<?php echo htmlspecialchars($row['title']); ?>"</p>
                <p><?php echo htmlspecialchars($row['comment']); ?></p>
                <p class="time"><?php echo htmlspecialchars($row['created_at']); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
