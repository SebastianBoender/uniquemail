<?php
//Include all controller files
require_once("controllers/database.php");
require_once("controllers/safety_controller.php");
require_once("controllers/imap_controller.php");
require_once("controllers/email_controller.php");

session_start();

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);

if(isset($_GET["id"])){
  $id = makesafe($_GET["id"]);
}

//Return succes or error after email insertion
if(isset($_SESSION["email_add"])) {
    if($_SESSION["email_add"] == "success") {
       echo '<div class="alert alert-success" style="margin-bottom:0;"><strong>Success! </strong>Email added succesfully!</div>';
    } else {
       echo '<div class="alert alert-warning" style="margin-bottom:0;"><strong>Oops! </strong> Something went wrong..</div>';
    }
    unset($_SESSION["email_add"]);
}

if(isset($_SESSION["email_delete"])) {
    if($_SESSION["email_delete"] == "success") {
        echo '<div class="alert alert-success" style="margin-bottom:0;"><strong>Success! </strong>Email deleted succesfully!</div>'; 
    } else {
      echo '<div class="alert alert-warning" style="margin-bottom:0;"><strong>Oops! </strong> Something went wrong..</div>';
    }
    unset($_SESSION["email_delete"]);
}

if(isset($_SESSION["email_edit"])) {
    if($_SESSION["email_edit"] == "success") {
        echo '<div class="alert alert-success" style="margin-bottom:0;"><strong>Success! </strong>Email editted succesfully!</div>'; 
    } else {
      echo '<div class="alert alert-warning" style="margin-bottom:0;"><strong>Oops! </strong> Something went wrong..</div>';
    }
    unset($_SESSION["email_edit"]);
}

if(isset($_SESSION["email_deletion"])) {
    if($_SESSION["email_deletion"] == "success") {
        echo '<div class="alert alert-success" style="margin-bottom:0;"><strong>Success! </strong>Email deleted succesfully!</div>'; 
    } else {
      echo '<div class="alert alert-warning" style="margin-bottom:0;"><strong>Oops! </strong> Something went wrong..</div>';
    }
    unset($_SESSION["email_deletion"]);
}

include("assets/navbar.php");
include("assets/sidebar.php");

?>