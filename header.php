<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forum</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { background: #333; color: white; padding: 15px; text-align: center; }
        .nav { text-align: right; background: #444; padding: 10px; }
        .nav a { color: white; margin: 0 15px; text-decoration: none; font-size: 18px; }
        .nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="header">
    <h1>My Forum</h1>
</div>

<div class="nav">
    <?php if (isset($_SESSION['user_id'])): ?> 
        <!-- If user is logged in, show username and logout -->
        <a href="notifications.php">Notifications</a>
        <a href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
        <a href="logout.php">Logout</a>
        
    <?php else: ?> 
        <!-- If user is NOT logged in, show login and signup -->
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
    <?php endif; ?>
    <!-- Search Bar -->
<div class="search-bar">
    <form action="search.php" method="get">
        <input type="text" name="query" placeholder="Search threads..." required>
        <button type="submit">🔍</button>
    </form>
</div>

<style>
    .search-bar {
        text-align: center;
        margin: 20px 0;
    }
    .search-bar form {
        display: inline-flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 25px;
        overflow: hidden;
    }
    .search-bar input[type="text"] {
        border: none;
        padding: 10px 15px;
        font-size: 16px;
        outline: none;
        width: 300px;
    }
    .search-bar button {
        background: #007BFF;
        color: white;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 16px;
    }
    .search-bar button:hover {
        background: #0056b3;
    }
</style>
</div>
