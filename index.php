<?php
$conn = new mysqli("localhost", "root", "", "forumdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Fetch posts with comment count, like count, and username
$result = $conn->query("
    SELECT posts.*, 
           users.username, 
           COUNT(comments.id) AS comment_count, 
           DATE(posts.created_at) AS created_date,
           (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count, 
           (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.user_id = $user_id) AS user_liked
    FROM posts 
    JOIN users ON posts.user_id = users.id
    LEFT JOIN comments ON posts.id = comments.post_id 
    GROUP BY posts.id 
    ORDER BY comment_count DESC, posts.created_at DESC
");

if (!$result) {
    die("Error fetching posts: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'header.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        a[href="add_post.php"] {
    display: inline-block;
    padding: 10px 20px;
    background-color: #28a745; /* Green background */
    color: #fff; /* White text */
    text-decoration: none; /* Remove underline */
    border-radius: 5px; /* Rounded corners */
    font-size: 16px;
    font-weight: bold;
    margin: 20px 0; /* Add spacing around the link */
    transition: background-color 0.3s ease;
    margin-left: 86%; /* Align to the left */
}

a[href="add_post.php"]:hover {
    background-color: #218838; /* Darker green on hover */
}

    </style>
</head>
<body>
    <a href="add_post.php">Create a Thread</a>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="post">
            <h2><a href="post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h2>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            <p class="post-meta">By: <?= htmlspecialchars($row['username']) ?></p> <!-- Added class "post-meta" -->
            <small class="post-meta">Posted on <?= $row['created_date'] ?></small>
            <div class="post-actions">
            <button id="like-btn-<?= $row['id'] ?>" class="like-btn" onclick="likePost(<?= $row['id'] ?>)">
    <?= $row['user_liked'] ? '❤️ ' : '🤍' ?>
</button>
                <span id="like-count-<?= $row['id'] ?>"><?= $row['like_count'] ?></span> Likes
                <a href="post.php?id=<?= $row['id'] ?>" class="comment-btn">
        💬 <span class="comment-count"><?= $row['comment_count'] ?></span>
    </a>
            </div>
        </div>
    <?php endwhile; ?>

    <script>
        function likePost(postId) {
    fetch('like_post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `post_id=${postId}`
    })
    .then(response => response.json()) // Expect JSON response from the server
    .then(data => {
        if (data.success) {
            // Update the like count and heart icon in the DOM
            const likeCountElement = document.querySelector(`#like-count-${postId}`);
            const likeButton = document.querySelector(`#like-btn-${postId}`);
            
            likeCountElement.textContent = data.like_count; // Update the like count
            likeButton.textContent = data.liked ? '❤️' : '🤍';  // Update the heart icon
        } else {
            alert(data.message); // Show error message if any
        }
    })
    .catch(error => console.error('Error:', error));
}
    </script>
</body>
</html>