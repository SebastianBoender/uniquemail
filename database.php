<?php
try {
	$user = "uniquemail_root";
	$pass = "UniqueMail123!!";
    $db = new PDO('mysql:host=localhost;dbname=uniquemail_mail', $user, $pass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
