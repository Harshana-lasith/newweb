<?php
$conn = new mysqli("localhost", "root", "", "forumdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';

if (empty($query)) {
    echo "No search query provided.";
    exit;
}

// Search for threads by title
$result = $conn->query("
    SELECT * FROM posts 
    WHERE title LIKE '%$query%' 
    ORDER BY created_at DESC
");

if ($result->num_rows > 0): ?>
    <h1>Search Results for "<?= htmlspecialchars($query) ?>"</h1>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <a href="post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a>
                <small>Posted on <?= $row['created_at'] ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <h1>No results found for "<?= htmlspecialchars($query) ?>"</h1>
<?php endif;

$conn->close();
?>