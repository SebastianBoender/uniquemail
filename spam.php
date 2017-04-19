<?php

include("assets/header.php");

//Set user id (klant nummer)
$userid = 1;
$i = 0;
$id = makesafe($_GET['id']);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if(isset($userid)) {
   echo emailController::paginate("spam", $id, $userid);
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
    echo '<a style="font-weight: bolder;" href="readspam?message='.$time.'&id='.$id.'">'.$subject.'</a>';
  } else {
    echo '<a href="readspam?message='.$time.'&id='.$id.'">'.$subject.'</a>';
  }

  ?>

  </td>
    <td><?=$message['sender']?></td>
    <td><?=date('d/m/Y', $message['timestamp'])?></td>
    <td><?=$message['size']/1000?> kb</td>
    <td><a href="maildel?mailid=<?=$message['mid']?>&id=<?=$id?>"><span class="glyphicon glyphicon-trash"></span></td>

<?php
  if($message['flag'] == 1){
   echo '<td><a href="flag?id='.$id.'&message='.$time.'"><span class="glyphicon glyphicon-flag" style="color:red"></span></td>';
  } else {
   echo '<td><a href="flag?id='.$id.'&message='.$time.'"><span class="glyphicon glyphicon-flag"></span></td>';
  }
  echo '</tr>';

$i++;
}
endforeach;
?>
<a href="new?id=<?=$_GET['id']?>" class="btn btn-primary">New email</a>

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
            echo "<a href='spam?id=$id&user=sebas&page=".$i."'";
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
