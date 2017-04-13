<?php
include("assets/header.php");

$userid = 1;
$id = $_GET['id'];
$timestamp = $_GET['message'];

echo emailController::flagEmail($id, $timestamp, $userid);
?>