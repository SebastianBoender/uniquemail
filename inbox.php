<?php

include("assets/header.php");

//Set user id (klant nummer)
$userid = 1;
$i = 0;
$id = makesafe($_GET['id']);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if(isset($userid)) {
   echo emailController::getInbox($id, $userid);
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

foreach($inbox as $message):
if($i == 10){
  break;
} else {
?>
  <tr>
    <td><a href="read?message=<?=$message["timestamp"]?>&id=<?=$id?>"><?=$message['subject']?></a></td>
    <td><?=$message['sender']?></td>
    <td><?=date('d/m/Y', $message['timestamp'])?></td>
    <td><?=$message['size']/1000?> kb</td>
    <td><a href="maildel?mailid=<?=$message['mid']?>&id=<?=$id?>"><span class="glyphicon glyphicon-trash"></span></td>
  </tr>


<?php
$i++;
}
endforeach;
?>

<a href="new?id=<?=$_GET['id']?>" class="btn btn-primary">New email</a>

 </tbody>
</table>
