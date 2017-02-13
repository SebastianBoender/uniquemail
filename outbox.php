<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$id = makesafe($_GET["id"]);
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($userid)) {
	echo emailController::getOutbox($id, $userid);
}

if(isset($_SESSION['outbox'])) {
    $data = $_SESSION['outbox'];
    unset($_SESSION['outbox']);
} else {
    $data = "";
}
?>


<div class="main col-md-9 main_panel">
<h1>Welkom <?=$_SESSION['name']?></h1>
                                         
<div class="table-responsive" id="inbox">          
<table class="table">
<thead>
  <tr>
    <th style="width: 70%;">Onderwerp</th>
    <th style="width: 10%;">Afzender</th>
    <th style="width: 10%;">Datum</th>
    <th style="width: 10%;">Grootte</th>
  </tr>

<?php
foreach($data as $user):
?>


  <tr>
    <td><?=$user["subject"]?></td>
    <td><?=$user["receiver_email"]?></td>
    <td><?=$user["date"]?></td>
    <td><?=$user["size"]?> kb</td>
  </tr>


<?php
endforeach;
?>

 </tbody>
</table>
