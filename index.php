<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($_POST['email'])) {
	$email = makesafe($_POST["email"]);
    echo addEmail($email, $userid);
    return;
}


?>


<div class="main col-md-9 main_panel">
<h1>Welkom Klant</h1>
                                         
<div class="table-responsive">          
<table class="table">
<thead>
  <tr>
    <th>Email</th>
    <th style="width: 5%;"></th>
    <th style="width: 5%;"></th>
    <th style="width: 5%;"></th>
  </tr>




<?php


echo getEmails($userid);

if(isset($_SESSION["data"])) {
    $data = $_SESSION["data"];
    unset($_SESSION["data"]);
} else {
    $data = "";
}

foreach($data as $user):
?>


  <tr>
    <td><?=$user["email"]?></td>
    <td><a href="inbox?id=<?=$user['id']?>&user=<?=$user['naam']?>" class="btn btn-success">Access</a></td>    
    <td><a href="edit?id=<?=$user['id']?>" class="btn btn-warning">Edit</a></td>    
    <td><a href="delete?id=<?=$user['id']?>" class="btn btn-danger">Delete</a></td> 
  </tr>
<?php
endforeach;
?>

 </tbody>
</table>

<form method="POST">
<input type="text" name="email">
<input type="hidden" name="userid" value="1">
<input type="submit" class="btn btn-primary" value="add email">
</form>

</div>
<!-- jQuery hosted library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Bootstrap library -->
<script src="js/bootstrap.min.js""></script>
</script>

</body>

</html>