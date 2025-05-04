<?php
include "comment.php";
fetchComments(1);
$conn = new mysqli("localhost", "root", "", "forumdb"); // Update database details

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post_id = 1; // Replace with dynamic post ID

function fetchComments($parent_id = 0, $level = 0) {
    global $conn, $post_id;
    $result = $conn->query("SELECT * FROM comments WHERE post_id = $post_id AND parent_id = $parent_id ORDER BY created_at ASC");

    while ($row = $result->fetch_assoc()) {
        echo "<div style='margin-left:" . ($level * 40) . "px; border-left: 2px solid #ccc; padding-left: 10px;'>";
        echo "<p><strong>" . htmlspecialchars($row['user_name']) . ":</strong> " . htmlspecialchars($row['comment']) . "</p>";
        
        // Reply Form
        echo "<form action='save_comment.php' method='POST'>
                <input type='hidden' name='post_id' value='{$post_id}'>
                <input type='hidden' name='parent_id' value='{$row['id']}'>
                <input type='text' name='user_name' placeholder='Your Name' required>
                <textarea name='comment' placeholder='Reply...' required></textarea>
                <button type='submit'>Reply</button>
              </form>";
        
        // Fetch replies (recursive)
        fetchComments($row['id'], $level + 1);

        echo "</div>";
    }
}

fetchComments(); // Load main comments first

$conn->close();
?>
