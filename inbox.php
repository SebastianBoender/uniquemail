<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if(isset($userid)) {
	echo getInbox($id, $userid);
}


if(isset($_SESSION['data'])) {
    $data = $_SESSION['data'];
    unset($_SESSION['data']);
} else {
    $data = "";
}
?>


<div class="main col-md-9 main_panel">
<h1>Welkom Klant</h1>
                                         
<div class="table-responsive" id="inbox">          
<table class="table">
<thead>
  <tr>
    <th style="width: 65%;">Onderwerp</th>
    <th style="width: 10%;">Afzender</th>
    <th style="width: 10%;">Datum</th>
    <th style="width: 10%;">Grootte</th>
    <th style="width: 5%;"></th>
  </tr>

<?php
foreach($data as $user):
?>


  <tr>
    <td><?=$user["subject"]?></td>
    <td><?=$user["sender"]?></td>
    <td><?=$user["date"]?></td>
    <td><?=$user["size"]?> kb</td>
    <td><a href="maildel?mailid=<?=$user['id']?>"><span class="glyphicon glyphicon-trash"></span></td>
  </tr>


<?php
endforeach;
?>

 </tbody>
</table>
