<?php

include("assets/header.php");

//Set user id (klant nummer)
$_SESSION['user_id'] = 1;
$userid = makesafe($_SESSION["user_id"]);

$receiver = "";
$subject = "";
$priority = "";
$message = "";
$bcc = "";
$cc = "";

$bijlageArray = array();

if(isset($_GET['email'])){
	$emailid = makesafe($_GET['email']);
}

if(isset($_GET['message'])){
  $timestamp = $_GET['message'];

  echo inboxController::getConceptEmail($emailid, $timestamp, $userid);
}


if(isset($_SESSION['concept'])) {
    $data = $_SESSION['concept'];
    unset($_SESSION['concept']);
} else {
    $data = "";
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

	echo actionController::saveconceptEmail($id, $timestamp, $userid, $message, $receiver, $subject, $bcc, $cc, $priority);
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
	$emailid = $_GET['id'];
	$from = "test";

	if($_FILES['file']['tmp_name'] != ""){
		$file = $_FILES['file'];
		echo sendmailController::attachment($receiver, $subject,"<body>".$message."</body>",$from, $bijlageArray, $cc, $cc, $emailid, $userid, $file);
	} else {
		echo sendmailController::storeEmail($receiver,$subject,"<body>".$message."</body>","<info@uniquemail.nl>",$bijlageArray,$cc, $emailid, $userid);
	}
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

$(document).ready(function(){
	var counter = 2;
	$('#del_file').hide();
	$('span#add_file').click(function(){
		$('#file_tools').before('<div class="file_upload" id="f'+counter+'"><input name="file[]" type="file">'+counter+'</div>');
		$('#del_file').fadeIn(0);
	counter++;
	});
});
</script>

<div class="main col-md-9 main_panel">
<h1>Welkom Klant</h1>
                                         
<div class="table-responsive" id="inbox">

<form method="POST" enctype="multipart/form-data">

<?php
if(isset($_GET['email'])){
foreach($data as $values):
?>

<p>
	<label>To:</label><br/>
	<input type="text" name="to" id="tags" value="<?=$values['receiver']?>" placeholder="To">
</p>

<p>
	<label>Subject</label><br/>
	<input type="text" name="subject" value="<?=$values['subject']?>" placeholder="Subject">         
</p>

<label onclick="hideShowCC()">CC</label><br/>

<div id="cc" class="hidden">
	<p>
		<input type="text" name="cc" value="<?=$values['cc']?>"" placeholder="CC">
	</p>
</div>

<label onclick="hideShowBCC()">BCC</label>

<div id="bcc" class="hidden">
	<p>
		<input type="text" name="bcc" value="<?=$values['bcc']?>" placeholder="BCC">
	</p>
</div>

<?php
if($values['priority']){
echo '<p>
	<label>Priority:</label><br/>
	<select name="priority">';
	if($values['priority'] == 5){
	 echo '<option value="5" selected="selected">Low Priority</option>
	 <option value="3">Normal Priority</option>
	  <option value="1">High Priority</option>';
	}elseif($values['priority'] == 3){
	 echo '<option value="5" selected="selected">Low Priority</option>
	 <option value="3" selected="selected">Normal Priority</option>
	  <option value="1">High Priority</option>';
	}elseif($values['priority'] == 1){
			 echo '<option value="5" selected="selected">Low Priority</option>
	  <option value="3">Normal Priority</option>
	  <option value="1" selected="selected">High Priority</option>';
	}

echo '</select>
</p>';
} else {
	echo 'empty';
	echo $values['priority'];
}
?>




<p>
	<label>Message</label><br/>
	<textarea name="message" rows="4" cols="50"><?=$values['message']?></textarea>
</p>

<p>
	<input type="submit" value="save as concept" name="concept" class="btn btn-primary">
</p>

<p>
	<input type="submit" value="send" name="concept" class="btn btn-primary">
</p>

<?php
endforeach;
} else {
?>

<p>
	<label>To:</label><br/>
	<input type="text" name="to"  placeholder="To">
</p>

<p>
	<label>Subject</label><br/>
	<input type="text" name="subject" placeholder="Subject">         
</p>

<label onclick="hideShowCC()">CC</label><br/>

<div id="cc" class="hidden">
	<p>
		<input type="text" name="cc"  placeholder="CC">
	</p>
</div>

<label onclick="hideShowBCC()">BCC</label>

<div id="bcc" class="hidden">
	<p>
		<input type="text" name="bcc"  placeholder="BCC">
	</p>
</div>

<div class='file_upload' id='f1'><input name='file[]' type='file'/>1</div>
	<div id='file_tools'>
		<span class="glyphicon glyphicon-plus-sign" id='add_file'>
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
	<textarea name="message" rows="4" cols="50"></textarea>
</p>

<p>
	<input type="submit" value="save as concept" name="concept" class="btn btn-primary">
</p>

<p>
	<input type="submit" value="send" name="send" class="btn btn-primary">
</p>

<?php
}
?>

</form>

<!-- jQuery hosted library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Bootstrap library -->
<script src="js/bootstrap.min.js""></script>
</script>