<?php
include("assets/header.php");

$userid = 1;
$id = $_GET['id'];
$timestamp = $_GET['message'];
$table = $_GET['table'];

echo actionController::flagEmail($id, $timestamp, $userid, $table);
?>