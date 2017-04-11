<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);
$timestamp = makesafe($_GET['message']);
$id = makesafe($_GET['id']);

if(isset($timestamp) && isset($_GET['message']) && isset($id) && isset($_GET['id'])){
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

<a href="reply?message=<?=$_GET['message']?>&id=<?=$id?>" class="btn btn-primary">Reply</a>
<a href="reply?message=<?=$_GET['message']?>&id=<?=$id?>&all=1" class="btn btn-primary">Reply All</a>
<a href="forward?message=<?=$_GET['message']?>&id=<?=$id?>" class="btn btn-primary">Forward</a>

<br/><br/>

<p>
	<label>From:</label><br/>
	<?=$body['sender']?>
</p>

<?php
if($body['cc'] != ""){

echo '<p>
		<label>CC:</label><br/>
		'.$body["cc"].'
	 </p>';
}
?>

<p>
	<label>Subject:</label><br/>
	<?=$body['subject']?>       
</p>

<p>
	<label>Date:</label><br/>
	<?=date('d/m/Y', $body['date'])?>       
</p>


<br/><br/>

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
	<?php
	if(!$body['message_html']){
		$bodyMessage = str_replace(array("=20", "="), "", $body['message']);
		echo nl2br(emailController::makelinks($bodyMessage));
	} else {
		echo quoted_printable_decode($body['message_html']);
	}
	?>
</p>

<?php
endforeach;
?>


<!-- jQuery hosted library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Bootstrap library -->
<script src="js/bootstrap.min.js""></script>
</script>