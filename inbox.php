<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);
$i = 0;
$id = makesafe($_GET['id']);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if(isset($userid)) {
	echo emailController::getInbox($id, $userid);
}


if(isset($_SESSION['dates'])) {
    $data = $_SESSION['dates'];
    unset($_SESSION['dates']);
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
foreach($data as $key=>$waarde):
if($i == 10){
	break;
} else {
?>
  <tr>
    <td><a href="read?message=<?=$date[$key]["timestamp"]?>&email=<?=$id?>"><?=$data[$key]['subject']?></a></td>
    <td><?=$data[$key]['from']?>@<?=$data[$key]['host']?></td>
    <td><?=date('d/m/Y', $data[$key]['date'])?></td>
    <td><?=$data[$key]['size']/1000?> kb</td>
    <td><a href="maildel?mid=<?=$i?>&id=<?=$id?>"><span class="glyphicon glyphicon-trash"></span></td>
        <td><a href="maildel?mailid=['id']"><span class="glyphicon glyphicon-floppy-disk"></span></td>
  </tr>


<?php
$i++;
}
endforeach;
?>

<a href="new?id=<?=$_GET['id']?>" class="btn btn-primary">New email</a>

 </tbody>
</table>
