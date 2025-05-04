<?php
$conn = new mysqli("localhost", "root", "", "forumdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$post = $conn->query("SELECT * FROM posts WHERE id = $post_id")->fetch_assoc();
if (!$post) {
    die("Post not found.");
}

$comments_result = $conn->query("
    SELECT comments.*, users.username 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.post_id = $post_id 
    ORDER BY comments.created_at ASC
");
$comments = $comments_result->fetch_all(MYSQLI_ASSOC);

function display_comments($comments, $parent_id = NULL) {
    foreach ($comments as $comment) {
        if ($comment['parent_id'] == $parent_id) {
            echo '<div class="comment">';
            echo '<strong>' . htmlspecialchars($comment['username']) . '</strong>';
            echo '<p>' . nl2br(htmlspecialchars($comment['comment'])) . '</p>';
            echo '<small>Posted on ' . $comment['created_at'] . '</small>';
            echo '<button class="reply-btn" onclick="toggleReplyForm(' . $comment['id'] . ')">Reply</button>';
            echo '<form class="reply-form" id="reply-form-' . $comment['id'] . '" action="add_comment.php" method="post" style="display: none;">';
            echo '<input type="hidden" name="post_id" value="' . $comment['post_id'] . '">';
            echo '<input type="hidden" name="parent_id" value="' . $comment['id'] . '">';
            echo '<textarea name="comment" placeholder="Reply to this comment..." required></textarea>';
            echo '<button type="submit">Submit</button>';
            echo '</form>';
            echo '<div class="replies">';
            display_comments($comments, $comment['id']);
            echo '</div>';
            echo '</div>';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'header.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
    <small>Posted on <?= $post['created_at'] ?></small>
    <hr>

    <h2>Comments</h2>
    <div class="comments">
        <?php display_comments($comments); ?>
    </div>

    <h3>Add a Comment</h3>
    <form action="add_comment.php" method="post">
        <input type="hidden" name="post_id" value="<?= $post_id ?>">
        <textarea name="comment" required></textarea>
        <button type="submit">Submit</button>
    </form>

    <a href="index.php">Back to Home</a>

    <script>
        function toggleReplyForm(commentId) {
            var form = document.getElementById("reply-form-" + commentId);
            if (form.style.display === "none") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }
    </script>
</body>
</html>
