<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);
$i = 0;
$id = makesafe($_GET['id']);
$emailid = makesafe($_GET['email']);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if(isset($userid)) {
	echo emailController::getConcepten($id, $userid);
}



if(isset($_SESSION['concepten'])) {
    $data = $_SESSION['concepten'];
    unset($_SESSION['concepten']);
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
    <th style="width: 10%;">Ontvanger</th>
    <th style="width: 10%;">Datum</th>
    <th style="width: 5%;"></th>
  </tr>

<?php
foreach($data as $concept):
if($i == 10){
	break;
} else {
?>
  <tr>
    <td><a href="new?message=<?=$concept["date"]?>&id=<?=$id?>"><?=$concept['subject']?></a></td>
    <td><?=$concept['receiver']?></td>
    <td><?=date('d/m/Y', $concept['date'])?></td>
    <td><a href="maildel?mailid=['id']"><span class="glyphicon glyphicon-trash"></span></td>
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
