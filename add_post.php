<?php
session_start();

// Redirect guest users to the login page
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to create a post.'); window.location.href = 'login.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "forumdb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $category = $conn->real_escape_string($_POST['category']); // Get the selected category
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    // Insert the post into the database
    if ($conn->query("INSERT INTO posts (title, content, category, user_id) VALUES ('$title', '$content', '$category' $user_id)")){
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Create a Thread</h1>
    <form class="add_post" action="add_post.php" method="post">
        <label for="title">Thread Title:</label>
        <input type="text" id="title" name="title" placeholder="Enter thread title" required>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
        <option value="" disabled selected>Select a category</option>
        <option value="General">General</option>
        <option value="Technology">Technology</option>
        <option value="Health">Health</option>
        <option value="Education">Education</option>
    </select>

       <label for="content">Thread Content:</label>
       <textarea id="content" name="content" placeholder="Write your idea..." required></textarea>
         
       <a href="index.php">cancel</a>
        <button type="submit">Post Thread</button>
        
    </form>
</body>
</html>
