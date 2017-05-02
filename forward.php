<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);
$timestamp = makesafe($_GET['message']);
$id = makesafe($_GET['id']);s

if(isset($timestamp) && isset($_GET['message']) && isset($id) && isset($_GET['id'])){
	echo actionController::getEmailMessage($id, $timestamp, $userid);
}

if(isset($_SESSION['email_body'])){
	$email_body = $_SESSION['email_body'];
	unset($_SESSION['email_body']);
}


if(isset($_POST['concept'])){
	$timestamp = time();
	$subject = makesafe($_POST['subject']);
	$message = makesafe($_POST['message']);
	$receiver = makesafe($_POST['to']);
	$bcc = makesafe($_POST['bcc']);
	$cc = makesafe($_POST['cc']);
	$id = makesafe($_GET['id']);
	$priority = makesafe($_POST['priority']);

	echo emailController::conceptEmail($id, $timestamp, $userid, $message, $receiver, $subject, $bcc, $cc, $priority);
}

if(isset($_POST['send'])){
	$timestamp = time();
	$subject = makesafe($_POST['subject']);
	$message = makesafe($_POST['message']);
	$receiver = makesafe($_POST['to']);
	$bcc = makesafe($_POST['bcc']);
	$cc = makesafe($_POST['cc']);
	$id = makesafe($_GET['id']);
	$priority = makesafe($_POST['priority']);
	$bijlageArray = "";
	$emailid = $_GET['id'];

	echo emailController::storeEmail($receiver,$subject,"<body>".$message."</body>","<info@uniquemail.nl>",$bijlageArray,$cc, $emailid, $userid);
}	

?>


<script>
function hideShowCC() {
	var className = $('#cc').attr('class');
	console.log(className);

	if (className == 'hidden') {
		$('#cc').removeClass('hidden');
	} else {
		$('#cc').addClass('hidden');
	}
};

function hideShowBCC() {
	var className = $('#bcc').attr('class');
	console.log(className);

	if (className == 'hidden') {
		$('#bcc').removeClass('hidden');
	} else {
		$('#bcc').addClass('hidden');
	}
};
</script>

<div class="main col-md-9 main_panel">
<h1>Welkom Klant</h1>

<form method="POST">

<?php
foreach($email_body as $body):
?>

<p>
	<label>To:</label><br/>
	<input type="text" name="to" placeholder="To">
</p>

<p>
	<label>Subject</label><br/>
	<input type="text" name="subject" value="FW: <?=$body['subject']?>" placeholder="Subject">         
</p>

<label onclick="hideShowCC()">CC</label><br/>

<div id="cc" class="hidden">
	<p>
		<input type="text" name="cc" placeholder="CC">
	</p>
</div>

<label onclick="hideShowBCC()">BCC</label>

<div id="bcc" class="hidden">
	<p>
		<input type="text" name="bcc" placeholder="BCC">
	</p>
</div>

<p>
	<label>Priority:</label><br/>
	<select name="priority">
	  <option value="5">Low Priority</option>
	  <option value="3" selected="selected">Normal Priority</option>
	  <option value="1">High Priority</option>
	</select>
</p>

<p>
	<label>Message</label><br/>
	<textarea name="message" rows="30" cols="200">




_________________________________________________________
From: <?=$body['sender']?> &lt;<?=$body['sender_email']?>&gt;
Sent: <?=date('d/m/Y H:i:s', $body['timestamp'])?>

<?php
if($body['cc'])
{
	echo 'CC: '.$body['cc'].'
';
}
?>
Subject: <?=$body['subject']?>
 




	<?=$body['message']?>
		
	</textarea>
</p>

<p>
	<input type="submit" value="save as concept" name="concept" class="btn btn-primary">
</p>

<p>
	<input type="submit" value="send" name="send" class="btn btn-primary">
</p>

<?php
endforeach;
?>

</form>

<!-- jQuery hosted library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Bootstrap library -->
<script src="js/bootstrap.min.js""></script>
</script>