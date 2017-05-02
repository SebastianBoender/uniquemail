<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);
$timestamp = makesafe($_GET['message']);
$id = makesafe($_GET['id']);

if(isset($timestamp) && isset($_GET['message']) && isset($id) && isset($_GET['id'])){
	echo inboxController::getSentEmail($id, $timestamp, $userid);
}

if(isset($_SESSION['sent'])){
	$email_body = $_SESSION['sent'];
	unset($_SESSION['sent']);
}


?>

<div class="main col-md-9 main_panel">
<h1>Welkom Klant</h1>
                                         
<div class="table-responsive" id="inbox">

<?php
foreach($email_body as $body):
?>

<br/><br/>

<p>
	<label>To:</label><br/>
	<?=$body['receiver']?>
</p>

<p>
	<label>Subject:</label><br/>
	<?=$body['subject']?>       
</p>


<p>
	<label>Message:</label><br/>
	<?php

		echo $body['message'];

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