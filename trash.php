<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$id = makesafe($_GET["id"]);
$i = 0;
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if(isset($userid)) {
   echo emailController::paginate("trash", $id, $userid);
}

if(isset($_POST['save'])){
  if(isset($_POST['formaction'])) {
     $ids = $_POST['ids'];

    if($_POST['formaction'] == 'markread'){
       echo emailController::markRead($ids, $userid, $id);
    }
    if($_POST['formaction'] == 'markunread'){
       echo emailController::markunRead($ids, $userid, $id);
    } 
  }
}
?>


<div class="main col-md-9 main_panel">
<h1>Welkom Klant</h1>
                                         
<div class="table-responsive" id="inbox">

<form method="POST" id="formIds">    
<table id="myTable" class="table">
<thead>
  <tr>
  <th></th>
    <th style="width: 65%;">Onderwerp</th>
    <th style="width: 10%;">Afzender</th>
    <th style="width: 10%;">Datum</th>
    <th style="width: 10%;">Grootte</th>
    <th style="width: 5%;"></th>
  </tr>
</thead>

 <tbody>
<?php

foreach($paginate_result as $message):
if($i == 10){
  break;
} else {
?>
  <tr>
  <td><input type="checkbox" name="ids[]" value="<?=$message['timestamp']?>"></td>
  <td>
  <?php
  $time = $message["timestamp"];
  $subject = $message["subject"];
  
  if($message['unread'] == 1){
    echo '<a style="font-weight: bolder;" href="read?message='.$time.'&id='.$id.'">'.$subject.'</a>';
  } else {
    echo '<a href="read?message='.$time.'&id='.$id.'">'.$subject.'</a>';
  }

  ?>

  </td>
    <td><?=$message['sender']?></td>
    <td><?=date('d/m/Y', $message['timestamp'])?></td>
    <td><?=$message['size']/1000?> kb</td>
    <td><a href="forcedeletemessage?mailid=<?=$message['timestamp']?>&id=<?=$id?>"><span class="glyphicon glyphicon-trash"></span></a></td>
    <td><a href="undelete?mailid=<?=$message['timestamp']?>&id=<?=$id?>"><span class="glyphicon glyphicon-refresh"></span></a></td>
<?php


$i++;
}
endforeach;
?>
<a href="new?id=<?=$_GET['id']?>" class="btn btn-primary">New email</a>
<a href="contacts?id=<?=$_GET['id']?>" class="btn btn-primary">Contacts</a>

<select name="formaction">
  <option value="markread">Mark as read</option>
  <option value="markunread">Mark as unread</option>
  <option value="flag">Flag</option>
  <option value="unflag">Unflag</option>
</select>

<input type="submit" class="btn btn-primary" name="save" value="Save changes">
<input type="text" name="searchquery" placeholder="Search...">
<input type="submit" class="btn btn-primary" name="search" value="Search">


<?php
for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
            echo "<a href='inbox?id=$id&user=sebas&page=".$i."'";
            if ($i==$page)  echo " class='curPage'";
            echo ">".$i."</a> "; 
};
?>

 </tbody>
</table>

<script>

$(document).ready(function(){ 
  $('#myTable').tablesorter(); 
});
</script>


</form>
