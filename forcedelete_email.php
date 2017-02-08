<?php
//Include all controller files
foreach (glob("controllers/*.php") as $filename)
{
    include $filename;
}

session_start();

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$emailid = makesafe($_GET["mailid"]);
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($emailid)) {
    echo forcedeleteEmail($emailid, $userid);
    return;
}

if(isset($_SESSION["error"])) {
    $error = $_SESSION["error"];
    unset($_SESSION["error"]);
} else {
    $error = "";
}
?>