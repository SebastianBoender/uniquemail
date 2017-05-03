<?php

include("assets/header.php");

//Set user id (klant nummer)
$userid = 1;
$i = 0;
$id = makesafe($_GET['id']);
$name = "";
$email = "";
$contactid = makesafe($_GET['contact']); 

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if(isset($_POST['email'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $company = $_POST['company'];
  contactController::editContacts($name, $email, $company, $userid, $id, $contactid);
}

if(isset($userid, $id, $contactid)) {
  contactController::getSingleContact($userid, $id, $contactid);
  if(isset($_SESSION['contacts'])){
     $data = $_SESSION['contacts'];
    unset($_SESSION["contacts"]);
  }
}
?>


<div class="main col-md-9 main_panel">
<h1>Welkom Klant</h1>                                      
<div class="table-responsive" id="inbox">

<form method="POST">

<p>
  <label>Name</label>
  <input type="text" name="name" value="<?=$contact['name']?>" />
</p>

<p>
  <label>Email</label>
  <input type="text" name="email" value="<?=$contact['email']?>" />
</p>

<p>
  <label>Company</label>
  <input type="text" name="company" value="<?=$contact['company']?>" />
</p>

<input type="submit" value="Update"/>
</form>

</div>
</div>
