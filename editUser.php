<?php

include "modules/connection.php";
include "modules/taghtml.php";

$conn = new Connection;

if (isset($_GET['id'])) {
    $userid = $_GET['id'];
} else {
    header("location:index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && (isset($_POST['bio']))) {
    $field_bio = $_POST['bio'];

    try {
        $sql = "";

        // TODO: enviar para o banco de dados as novas informacoes do usuario

        header("location:.././" .$userid);
        exit;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

// get user info
try {
    $sql = "SELECT * FROM user WHERE user.id=?";
    $stmt = $conn->getConnection()->prepare($sql);
    
    $stmt->bindParam(1, $userid);
    
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        die("User not found");
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
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark justify-content-center">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Social Chat</a>
        </div>
        <ul class="navbar-nav me-3">
            <li class="nav-item">
                <a href="../../index.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
                <a href="../../users.php" class="nav-link">Users</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-5 p-3 shadow bg-white col">
        <form action="edit" method="post">
            <p class="h3 border-bottom">Profile</p>
            <div class="container p-3">
                <div class="container mb-3 row">
                    <input type="text" name="username" id="field_username" class="col border" required>
                    <p class="col border" id="field_id">Id</p>
                </div> 
                <p class="h5 border-bottom">Biography</p>   
                <div class="container">
                    <textarea name="bio" id="field_bio" rows="3" class="form-control">
                    </textarea>
                </div>
            </div>
            <div class="clearfix">
                <button type="submit" class="float-end btn btn-outline-primary">Confirm Profile</button>
            </div>
        </form>
    </div>
    <script>
        let field_username = document.getElementById("field_username");
        let field_id = document.getElementById("field_id");
        let field_bio = document.getElementById("field_bio");

        let userinfo = <?php echo json_encode($userinfo); ?>;
        
        if (userinfo != null) {
            field_username.setAttribute("value",userinfo['name']);
            field_id.innerText = userinfo['id'];
            field_bio.innerText = userinfo['bio'] || "...";
        }
    </script>
</body>
</html>