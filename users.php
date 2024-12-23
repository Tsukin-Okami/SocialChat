<?php 

include "modules/connection.php";
include "modules/taghtml.php";

$conn = new Connection;

if (isset($_GET['deluser'])) {
    try {
        $userid = $_GET['deluser'];
    
        $sql = "DELETE FROM `user` WHERE `user`.`id`=?";
        $stmt = $conn->getConnection()->prepare($sql);
    
        $stmt->bindValue(1,$userid);
    
        $stmt->execute();
    
        header("location:users.php");
        exit;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

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

// get users
try {
    $sql = "SELECT * FROM user";
    $stmt = $conn->getConnection()->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($list as $key => $value) {
            $pid = new tagHtml;
            $pid->setTag("td");
            $pid->setValue($value['id']);

            $pname = new tagHtml;
            $pname->setTag("td");
            $pname->setValue($value['name']);

            // delete form
            $phidden = new tagHtml;
            $phidden->setTag("input", true);
            $phidden->addAtribute("type","hidden");
            $phidden->addAtribute("name","deluser");
            $phidden->addAtribute("value",$value['id']);

            $pbutton = new tagHtml;
            $pbutton->setTag("button");
            $pbutton->addAtribute("type","submit");
            $pbutton->addAtribute("class","btn btn-sm btn-outline-danger btn-block");
            $pbutton->setValue("Delete");

            $formjoint = $phidden->mount() . $pbutton->mount();

            $pform = new tagHtml;
            $pform->setTag("form");
            $pform->addAtribute("action","users.php");
            $pform->addAtribute("method","get");
            $pform->addAtribute("class","col d-grid");
            $pform->setValue($formjoint);

            $plink = new tagHtml;
            $plink->setTag("a");
            $plink->addAtribute("class","col btn btn-sm btn-outline-success btn-block");
            $plink->addAtribute("href","./user/" . $value['id']);
            $plink->setValue("Profile");

            $prow = $pform->mount(). $plink->mount();

            $pdelete = new tagHtml;
            $pdelete->setTag("td");
            $pdelete->addAtribute("class","row");
            $pdelete->setValue($prow);

            // joint
            $joint = $pid->mount() . $pname->mount() . $pdelete->mount();

            // table line
            $profile = new tagHtml;
            $profile->setTag("tr");
            $profile->setValue($joint);

            $html_users .= $profile->mount();
        }

        // table > thead
        $thId = new tagHtml;
        $thId->setTag("th");
        $thId->setValue("Id");

        $thUsername = new tagHtml;
        $thUsername->setTag("th");
        $thUsername->setValue("Username");

        $thState = new tagHtml;
        $thState->setTag("th");
        $thState->setValue("State");

        $thJoint = $thId->mount() . $thUsername->mount() . $thState->mount();

        $trhead = new tagHtml;
        $trhead->setTag("tr");
        $trhead->setValue($thJoint);

        $tablehead = new tagHtml;
        $tablehead->setTag("thead");
        $tablehead->setValue($trhead->mount());

        // table > tbody
        $tablebody = new tagHtml;
        $tablebody->setTag("tbody");
        $tablebody->setValue($html_users);

        // table > content
        $tablecontent = $tablehead->mount() . $tablebody->mount();

        // table
        $tagTable = new tagHtml;
        $tagTable->setTag("table");
        $tagTable->setValue($tablecontent);
        $tagTable->addAtribute("class","table table-hover");
        
        $html_users = $tagTable->mount();
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
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
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