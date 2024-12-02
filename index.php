<?php

include "modules/connection.php";
include "modules/taghtml.php";


$conn = new Connection;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = $_POST['title'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO post(title, comment) VALUES (:title, :comment)";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->bindValue("title", $title, PDO::PARAM_STR);
    $stmt->bindValue("comment", $comment, PDO::PARAM_STR);

    header("location:index.php");
    exit;
}

function SetupPost(int $id, string $title, string $comment) {
    
}

try {
    $sql = "SELECT * FROM post";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Chat</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark justify-content-center">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Social Chat</a>
        </div>
        <ul class="navbar-nav me-3">
            <li class="nav-item">
                <a href="index.php" class="nav-link active">Home</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-5 shadow bg-white">
        <div class="row">
            <div class="col border p-3">
                <p class="h4">View Posts</p>
                <div class="container p-3">
                    post1
                </div>
            </div>
            <div class="col bg-light p-3">
                <p class="h4">Create Post</p>
                <form action="index.php" method="post" class="was-validated">
                    <div class="mt-3 mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" name="title" id="title" required class="form-control">                        
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment:</label>
                        <textarea name="comment" id="comment" rows="3" required class="form-control"></textarea>               
                    </div>
                    <button type="submit" class="btn btn-outline-primary">Create Post</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>