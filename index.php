<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if (isset($_POST['email']) && isset($_POST['mail_server']) && isset($_POST['afzender']) && isset($_POST['password']) && isset($_POST['ssl']) && isset($_POST['port']))
{
	$email = makesafe($_POST["email"]);
  $afzender = makesafe($_POST["afzender"]);
  $mailserver = makesafe($_POST["mail_server"]);
  $password = makesafe($_POST["password"]);
  $port = makesafe($_POST['port']);
  $ssl = makesafe($_POST['ssl']);

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Invalid email format'; 
  } else {
    echo emailController::addEmail($email, $userid, $afzender, $mailserver, $password, $port, $ssl, $mailto, $mailsender, $bcc, $cc);
    return;
  }
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


echo emailController::getEmails($userid);

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
<p>
  <label>Email</label>
  <input type="text" name="email" placeholder="Email">
</p>

<p>
  <label>Afzender naam</label>
  <input type="text" name="afzender" placeholder="Afzender">
</p>

<p>
  <label>Mail server</label>
  <input type="text" name="mail_server" placeholder="Mail server">
</p>

<p>
  <label>Poort</label>
  <input type="text" name="port" placeholder="Poort">
</p>

<p>
  <label>SSL</label>
  <input type="radio" name="ssl" value="ssl"> Yes
  <input type="radio" name="ssl" value="ssl/novalidate-cert"> No<br>
</p>

<p>
  <label>Wachtwoord</label>
  <input type="password" name="password" placeholder="Wachtwoord">
</p>

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