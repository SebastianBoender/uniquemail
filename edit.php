<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$id = makesafe($_GET["id"]);
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($_POST['email']) && isset($_POST['afzender']) && isset($_POST['mail_server']) && isset($_POST['password'])) {
	$email = makesafe($_POST["email"]);
	$afzender = makesafe($_POST["afzender"]);
	$mailserver = makesafe($_POST["mail_server"]);
	$password = makesafe($_POST["password"]);
    echo emailController::editEmail($email, $id, $userid, $password, $mailserver, $afzender);
    return;
}

echo emailController::getSingleEmail($id, $userid);

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
<p>
	<label>Email</label>
	<input type="text" name="email" placeholder="Email" value="<?=$user['email']?>">
</p>

<p>
	<label>Afzender naam</label>
	<input type="text" name="afzender" placeholder="Afzender" value="<?=$user['afzender']?>">
</p>

<p>
	<label>Mail server</label>
	<input type="text" name="mail_server" placeholder="Mail server" value="<?=$user['mail_server']?>">
</p>

<p>
	<label>Wachtwoord</label>
	<input type="password" name="password" placeholder="Wachtwoord">
</p>

<p>
	<input type="submit" class="btn btn-primary" value="update">
</p>
</form>

<?php
endforeach;
}
?>
