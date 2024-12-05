<?php

include "modules/connection.php";
include "modules/taghtml.php";

$conn = new Connection;

if (isset($_GET['delpost'])) {
    try {
        $postid = $_GET['delpost'];

        $sql = "DELETE FROM `post` WHERE `post`.`id`=?";
        $stmt = $conn->getConnection()->prepare($sql);

        $stmt->bindValue(1,$postid);
        $stmt->execute();

        header("location:index.php");
        exit;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = $_POST['title'];
    $comment = $_POST['comment'];
    $owner = $_POST['owner'];

    if (!isset($owner) || $owner == "") {
        header("location:index.php");
        exit;
    }

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
    $sql = "SELECT `post`.`id`, `post`.`title`, `post`.`comment`, `user`.`name` AS 'username', `user`.`id` AS 'userid' FROM `post` 
    INNER JOIN `user` ON `post`.`owner`=`user`.`id` 
    ORDER BY `post`.`id` DESC";

    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as $key => $value) {
            $p_id = $value['id']; // post id
            $p_title = $value['title']; // post title
            $p_comment = $value['comment']; // post comment
            $p_username = $value['username']; // post user name
            $p_userid = $value['userid']; // post user id

            // essential
            $tagHidden = new tagHtml;
            $tagHidden->setTag("input", true);
            $tagHidden->addAtribute("type","hidden");
            $tagHidden->addAtribute("name","delpost");
            $tagHidden->addAtribute("value",$p_id);

            // header content
            $tagTitle = new tagHtml;
            $tagTitle->setTag("p");
            $tagTitle->addAtribute("class","h5");
            $tagTitle->setValue($p_title);

            $header_content = $tagTitle->mount();

            // body content
            $tagComment = new tagHtml;
            $tagComment->setTag("p");
            $tagComment->setValue(nl2br($p_comment));

            $body_content = $tagComment->mount();

            // footer content
            $labelOwner = new tagHtml;
            $labelOwner->setTag("a");
            $labelOwner->addAtribute("class","text-primary");
            $labelOwner->addAtribute("href","./user/" . $p_userid);
            $labelOwner->setValue($p_username);

            // hack to display corrected: "created by @username"
            // without hack: "created by@username"
            $credits =  "Created by " . $labelOwner->mount();

            $columnFirst = new tagHtml;
            $columnFirst->setTag("div");
            $columnFirst->addAtribute("class","col");
            $columnFirst->setValue($credits);

            $tagButton = new tagHtml;
            $tagButton->setTag("button");
            $tagButton->addAtribute("type","submit");
            $tagButton->addAtribute("class","col btn btn-outline-danger btn-block");
            $tagButton->setValue("Delete");

            $lineCredits = $columnFirst->mount() . $tagButton->mount();

            $tableHorizontal = new tagHtml;
            $tableHorizontal->setTag("div");
            $tableHorizontal->addAtribute("class","row");
            $tableHorizontal->setValue($lineCredits);

            $footer_content = $tableHorizontal->mount();

            // card construction
            $card_header = new tagHtml;
            $card_header->setTag("div");
            $card_header->addAtribute("class","card-header");
            
            $card_body = new tagHtml;
            $card_body->setTag("div");
            $card_body->addAtribute("class","card-body");
            
            $card_footer = new tagHtml;
            $card_footer->setTag("div");
            $card_footer->addAtribute("class","card-footer");
            
            // card content
            $card_header->setValue($header_content);
            $card_body->setValue($body_content);
            $card_footer->setValue($footer_content);

            // card mounted
            $card = $card_header->mount() . $card_body->mount() . $card_footer->mount();

            // form
            $post = new tagHtml;
            $post->setTag("form");
            $post->addAtribute("action","index.php");
            $post->addAtribute("method","get");
            $post->addAtribute("class","card mb-5");
            $post->setValue($tagHidden->mount() . $card);
            
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
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
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
                    <a href=""></a>
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
                            <?php echo $html_users; ?>
                        </select>               
                    </div>
                    <button type="submit" class="btn btn-outline-primary" id="btn-createpost">Create Post</button>
                </form>
            </div>
        </div>
    </div>
    <script>

        let title = document.getElementById("title");
        let comment = document.getElementById("comment");
        let owner_select = document.getElementById("owner");
        let btn = document.getElementById("btn-createpost");
        
        let options = owner_select.children

        let unknownOption = "<option value=\"\">Unknown</option>";

        if (options.length >= 1) {
            console.log("wee");
        } else {
            owner_select.innerHTML = unknownOption;
            owner_select.setAttribute("disabled","");

            title.setAttribute("disabled","");
            comment.setAttribute("disabled","");
            btn.setAttribute("disabled","");

            btn.remove();
        }

    </script>
</body>
</html>