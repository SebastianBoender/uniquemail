<?php
//Include all controller files
require_once("controllers/database.php");
require_once("controllers/safety_controller.php");
require_once("controllers/imap_controller.php");
require_once("controllers/email_controller.php");

session_start();

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$mid = makesafe($_GET["mid"]);
$userid = makesafe($_SESSION["user_id"]);
$id = makesafe($_GET["id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($mid)) {
    echo imapController::imapMoveToTrash($mid, $id);
    return;
}

if(isset($_SESSION["error"])) {
    $error = $_SESSION["error"];
    unset($_SESSION["error"]);
} else {
    $error = "";
}
?>