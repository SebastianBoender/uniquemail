<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);
$timestamp = makesafe($_GET['message']);
$id = makesafe($_GET['email']);

if(isset($timestamp) && isset($_GET['message']) && isset($id) && isset($_GET['email'])){
	echo emailController::getEmailMessage($id, $timestamp, $userid);
}

if(isset($_SESSION['email_body'])){
	$email_body = $_SESSION['email_body'];
	unset($_SESSION['email_body']);
}


?>

<div class="main col-md-9 main_panel">
<h1>Welkom Klant</h1>
                                         
<div class="table-responsive" id="inbox">

<?php
foreach($email_body as $body):
?>

<p>
	<label>From:</label><br/>
	<?=$body['sender']?>
</p>

<p>
	<label>Subject:</label><br/>
	<?=$body['subject']?>       
</p>

<?php
if($body['attachment'] != '0'){
echo '
	<p>
		<label>Attachments:</label><br/>
		<a href="attachments/'.$body['attachment'].'">'.$body['attachment'].'</a>
	</p>';
}
?>

<p>
	<label>Message:</label><br/>
	<?=$body['message']?>
</p>

<?php
endforeach;
?>


<!-- jQuery hosted library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Bootstrap library -->
<script src="js/bootstrap.min.js""></script>
</script>