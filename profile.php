<?php
session_start();
$conn = new mysqli("localhost", "root", "", "forumdb");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
</head>
<body>

<h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
<p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
<p>Joined on: <?php echo htmlspecialchars($user['created_at']); ?></p>

<a href="index.php">Back to Forum</a> | 
<a href="logout.php">Logout</a>

</body>
</html>
