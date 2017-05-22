<?php

include("assets/header.php");

//Set user id (klant nummer)
$userid = 1;
$i = 0;
$id = makesafe($_GET['id']);
$name = "";
$email = "";
$contactid = makesafe($_GET['contact']); 

if(isset($userid, $id, $contactid)) {
  contactController::deleteContact($userid, $id, $contactid);
}
?>
