<?php 

include "modules/connection.php";
include "modules/taghtml.php";

$conn = new Connection;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    try {
        $username = $_POST['username'];

        $sql = "INSERT INTO `user`(name) VALUES (:name)";
        $stmt = $conn->getConnection()->prepare($sql);
    
        $stmt->bindValue("name", $username, PDO::PARAM_STR);
    
        $stmt->execute();
    
        header("location:users.php");
        exit;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

$html_users = "";

try {
    $sql = "SELECT * FROM user";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($list as $key => $value) {
            $pid = new tagHtml;
            $pid->setTag("p");
            $pid->addAtribute("class","col");
            $pid->setValue($value['id']);

            $pname = new tagHtml;
            $pname->setTag("p");
            $pname->addAtribute("class","col");
            $pname->setValue($value['name']);

            $joint = $pid->mount() . $pname->mount();

            $profile = new tagHtml;
            $profile->setTag("div");
            $profile->addAtribute("class","row");
            $profile->setValue($joint);

            $html_users .= $profile->mount();
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
    <title>Users - Social Chat</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark justify-content-center">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Social Chat</a>
        </div>
        <ul class="navbar-nav me-3">
            <li class="nav-item">
                <a href="index.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
                <a href="users.php" class="nav-link active">Users</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-5 shadow bg-white">
        <div class="row">
            <div class="col border p-3">
                <p class="h4">View Users</p>
                <div class="container p-3 col">
                    <?php echo $html_users; ?>
                </div>
            </div>
            <div class="col bg-light p-3">
                <p class="h4">Create User</p>
                <form action="users.php" method="post" class="was-validated">
                    <div class="mt-3 mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" name="username" id="username" required class="form-control">                        
                    </div>
                    <button type="submit" class="btn btn-outline-primary">Create User</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>