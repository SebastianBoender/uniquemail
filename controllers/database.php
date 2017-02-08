<?php
try {
	$user = "root";
	$pass = "";
    $db = new PDO('mysql:host=localhost;dbname=uniquemail', $user, $pass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
