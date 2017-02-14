<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$id = makesafe($_GET["id"]);
$userid = makesafe($_SESSION["user_id"]);
$i = 0;

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
<h1>Welkom Klant</h1>
                                         
<div class="table-responsive" id="inbox">          
<table class="table">
<thead>
  <tr>
    <th style="width: 70%;">Onderwerp</th>
    <th style="width: 10%;">Ontvanger</th>
    <th style="width: 10%;">Datum</th>
    <th style="width: 10%;">Grootte</th>
  </tr>

<?php
foreach($outbox as $key=>$waarde):
if($i == 10){
  break;
} else {
?>
  <tr>
    <td><?=$outbox[$key]['subject']?></td>
    <td><?=$outbox[$key]['receiver']?></td>
    <td><?=$outbox[$key]['date']?></td>
    <td><?=$outbox[$key]['size']/1000?> kb</td>
    <td><a href="maildel?mailid=['id']"><span class="glyphicon glyphicon-trash"></span></td>
  </tr>


<?php
$i++;
}
endforeach;
?>

 </tbody>
</table>
