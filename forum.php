<?php
$conn = new mysqli("localhost", "root", "", "forumdb"); // Update database details

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST["post_id"];
    $parent_id = $_POST["parent_id"]; // This helps in replying to a comment
    $user_name = $_POST["user_name"];
    $comment = $_POST["comment"];

    $stmt = $conn->prepare("INSERT INTO comments (post_id, parent_id, user_name, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $post_id, $parent_id, $user_name, $comment);

    if ($stmt->execute()) {
        echo "Comment added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
