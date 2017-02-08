<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$id = makesafe($_GET["id"]);
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($_POST['email'])) {
	$email = makesafe($_POST["email"]);
    echo editEmail($email, $id, $userid);
    return;
}

echo getSingleEmail($id, $userid);

if(isset($_SESSION["data"])) {
    $data = $_SESSION["data"];
} else {
    $data = "";
}

if(isset($_SESSION["error"])) {
    $error = $_SESSION["error"];
    unset($_SESSION["error"]);
} else {
    $error = "";
}

echo $error;

if(isset($_SESSION["data"])) {
    $data = $_SESSION["data"];
foreach($data as $user):

?>

<div class="main col-md-9 main_panel">
<h1>Email wijzigen</h1>

<form method="POST">
<input type="text" name="email" value="<?=$user['email']?>">
<input type="submit" class="btn btn-primary" value="update">
</form>

<?php
endforeach;
}
?>
