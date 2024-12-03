<?php

include "modules/connection.php";
include "modules/taghtml.php";

$conn = new Connection;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = $_POST['title'];
    $comment = $_POST['comment'];
    $owner = $_POST['owner'];

    $sql = "INSERT INTO `post`(title, comment, owner) VALUES (:title, :comment, :owner)";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->bindValue("title", $title, PDO::PARAM_STR);
    $stmt->bindValue("comment", $comment, PDO::PARAM_STR);
    $stmt->bindValue("owner", $owner, PDO::PARAM_INT);

    $stmt->execute();

    header("location:index.php");
    exit;
}

$html_posts = "";
$html_users = "";

// get posts
try {
    $sql = "SELECT * FROM post";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as $key => $value) {
            $pid = $value['id'];
            $ptitle = $value['title'];
            $pcomment = $value['comment'];
            $powner = $value['owner'];
            
            $tagOwner = new tagHtml;
            $tagOwner->setTag("p");
            $tagOwner->setValue("owner: " . $powner);

            $tagTitle = new tagHtml;
            $tagTitle->setTag("p");
            $tagTitle->addAtribute("class","h5");
            $tagTitle->setValue($ptitle);

            $tagComment = new tagHtml;
            $tagComment->setTag("p");
            $tagComment->setValue($pcomment);

            $tagHidden = new tagHtml;
            $tagHidden->setTag("input", true);
            $tagHidden->addAtribute("type","hidden");
            $tagHidden->addAtribute("name","id");
            $tagHidden->addAtribute("value",$pid);

            $tagButton = new tagHtml;
            $tagButton->setTag("button");
            $tagButton->addAtribute("type","submit");
            $tagButton->setValue("Get");

            $all = $tagOwner->mount() . $tagTitle->mount() . $tagComment->mount() . $tagHidden->mount() . $tagButton->mount();

            $post = new tagHtml;
            $post->setTag("form");
            $post->addAtribute("action","index.php");
            $post->addAtribute("method","get");
            $post->setValue($all);
            
            $html_posts .= $post->mount();
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}

// get users
try {
    $sql = "SELECT * FROM user";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($list as $key => $value) {
            $option = new tagHtml;
            $option->setTag("option");
            $option->setValue($value['id'] . ": " . $value['name']);
            $option->addAtribute("value",$value['id']);

            $html_users .= $option->mount();
        }
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
            <li class="nav-item">
                <a href="users.php" class="nav-link">Users</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-5 shadow bg-white">
        <div class="row">
            <div class="col border p-3">
                <p class="h4">View Posts</p>
                <div class="container p-3">
                    <div class="card">
                        <div class="card-header ">
                            <p class="h5">Titulo do post</p>
                        </div>
                        <div class="card-body">
                            <p>Descrição do post</p>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col">
                                    <label>Created by</label> <label>Tsukin</label>
                                </div>
                                <button type="submit" class="btn col btn-outline-info btn-block">Information</button>
                            </div>
                        </div>
                    </div>
                    <?php echo $html_posts; ?>
                </div>
            </div>
            <div class="col bg-light p-3">
                <p class="h4">Create Post</p>
                <form action="index.php" method="post" class="was-validated">
                    <div class="mt-3 mb-3">
                        <label for="title" class="form-label">Title of the post:</label>
                        <input type="text" name="title" id="title" required class="form-control">                        
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment of the post:</label>
                        <textarea name="comment" id="comment" rows="3" required class="form-control"></textarea>               
                    </div>
                    <div class="mb-3">
                        <label for="owner" class="form-label">Owner of the post:</label>
                        <select name="owner" id="owner" class="form-select">
                            <option value="">Select a user</option>
                            <?php echo $html_users; ?>
                        </select>               
                    </div>
                    <button type="submit" class="btn btn-outline-primary">Create Post</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>