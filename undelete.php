<?php
//Include all controller files
include("assets/header.php");

session_start();

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$emailid = makesafe($_GET["mailid"]);
$userid = makesafe($_SESSION["user_id"]);
$id = makesafe($_GET["id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($emailid)) {
    echo actionController::trashMessage($id, $userid, $emailid);
    return;
}

if(isset($_SESSION["error"])) {
    $error = $_SESSION["error"];
    unset($_SESSION["error"]);
} else {
    $error = "";
}
?>