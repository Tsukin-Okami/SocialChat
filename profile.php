<?php

include "modules/connection.php";
include "modules/taghtml.php";

$conn = new Connection;

if (isset($_GET['id'])) {
    $userid = $_GET['id'];

    $sql = "SELECT * FROM user WHERE user.id=?";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->bindParam(1, $userid);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        die("User not found");
    }

} else {
    header("location:index.php");
    exit;
}

$user_posts = "";

// get user posts
try {
    $sql = "SELECT * FROM `post` WHERE `post`.`owner`=:owner ORDER BY `post`.`id` DESC";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->bindParam("owner", $userid);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($list as $key => $value) {
            $pid = $value['id'];
            $ptitle = $value['title'];
            $pcomment = $value['comment'];

            // header content
            $tagTitle = new tagHtml;
            $tagTitle->setTag("p");
            $tagTitle->addAtribute("class","h5");
            $tagTitle->setValue($ptitle);

            $header_content = $tagTitle->mount();

            // body content
            $tagComment = new tagHtml;
            $tagComment->setTag("p");
            $tagComment->setValue(nl2br($pcomment));

            $body_content = $tagComment->mount();

            // card construction
            $card_header = new tagHtml;
            $card_header->setTag("div");
            $card_header->addAtribute("class","card-header");
            
            $card_body = new tagHtml;
            $card_body->setTag("div");
            $card_body->addAtribute("class","card-body");
            
            // card content
            $card_header->setValue($header_content);
            $card_body->setValue($body_content);

            // card mounted
            $card = $card_header->mount() . $card_body->mount();

            // form
            $post = new tagHtml;
            $post->setTag("div");
            $post->addAtribute("class","card mb-5");
            $post->setValue($card);
            
            $user_posts .= $post->mount();
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
    <title>Profile - Social Chat</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark justify-content-center">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Social Chat</a>
        </div>
        <ul class="navbar-nav me-3">
            <li class="nav-item">
                <a href="../index.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
                <a href="../users.php" class="nav-link">Users</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-5 p-3 shadow bg-white col">
        <p class="h3 border-bottom">Profile</p>
        <div class="container p-3">
            <div class="container mb-3 row">
                <p class="col" id="field_username">Username</p>
                <p class="col" id="field_id">Id</p>
            </div> 
            <p class="h5 border-bottom">Biography</p>   
            <div class="container">
                <p id="field_bio">...</p>
            </div>
        </div>
        <form action="./<?php echo $userid; ?>/edit" method="post" class="clearfix">
            <input type="hidden" name="id">
            <button type="submit" class="float-end btn btn-outline-primary">Edit Profile</button>
        </form>
    </div>
    <div class="container mt-5 p-3 shadow bg-white col">
        <p class="h3 border-bottom">User posts</p>
        <div class="container p-3">
            <?php echo $user_posts; ?>
        </div>
    </div>
    <script>
        let field_username = document.getElementById("field_username");
        let field_id = document.getElementById("field_id");
        let field_bio = document.getElementById("field_bio");

        let userinfo = <?php echo json_encode($userinfo); ?>;
        
        if (userinfo != null) {
            field_username.innerText = userinfo['name'];
            field_id.innerText = userinfo['id'];
            field_bio.innerText = userinfo['bio'] || "...";
        }
    </script>
</body>
</html>