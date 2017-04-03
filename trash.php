<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$id = makesafe($_GET["id"]);
$i = 0;
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($userid)) {
	echo imapController::getImapTrash($id, $userid);
}

if(isset($_SESSION['trash'])) {
    $data = $_SESSION['trash'];
    unset($_SESSION['trash']);
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
foreach($trash as $user):
?>


  <tr>
    <td><a href="readtrash?message=<?=$user["timestamp"]?>&id=<?=$id?>"><?=$user['subject']?></a></td>
    <td><?=$user["from"]?>@<?=$user["host"]?></td>
    <td><?=date('d/m/Y', $user["date"])?></td>
    <td><?=$user["size"]?> kb</td>
    <td><a href="forcedeletemessage?mailid=<?=$user['mid']?>&id=<?=$id?>"><span class="glyphicon glyphicon-trash"></span></td>
    <td><a href="undelete?mailid=<?=$user['mid']?>&id=<?=$id?>"><span class="glyphicon glyphicon-share-alt"></span></td>
  </tr>


<?php
endforeach;
?>

 </tbody>
</table>
