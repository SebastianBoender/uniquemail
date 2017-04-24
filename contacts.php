<?php

include("assets/header.php");

//Set user id (klant nummer)
$userid = 1;
$i = 0;
$id = makesafe($_GET['id']);
$name = "";
$email = "";

//Check if post exists, and make variables safe to prevent XSS attacks/exploiting
if(isset($_POST['email'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $company = $_POST['company'];
  contactController::addContacts($name, $email, $company, $userid, $id);
}

if(isset($userid, $id)) {
  contactController::getContacts($userid, $id);
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
  <input type="text" name="name" placeholder="Name"/>
</p>

<p>
  <label>Email</label>
  <input type="text" name="email" placeholder="Email"/>
</p>

<p>
  <label>Company</label>
  <input type="text" name="company" placeholder="Company"/>
</p>

<input type="submit" value="Save"/>
</form>

<table id="myTable" class="table">
<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Company</th>
</tr>
</thead>
<tbody>
<?php
foreach($data as $contact):
?>
<tr>
<td><?=$contact['name']?></td>
<td><?=$contact['email']?></td>
<td><?=$contact['company']?></td>
</tr>
<?php
endforeach;
?>
</tbody>
</table>

</div>
</div>
