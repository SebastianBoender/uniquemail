<?php
class emailController extends imapController{

	public function index()
	{
		if(isset($_SERVER['HTTP_REFERER'])) {
	    	$previous = $_SERVER['HTTP_REFERER'];
	    	header('Location: '.$previous.'');
	    	exit;
		}
		else
		{
			$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			header('Location: '.$actual_link.'');
			exit;
		}
	}

	public function getEmails($userid)
	{
		require('controllers/database.php');
		$st = $db->prepare("SELECT * FROM email_accounts WHERE user_id = :userid AND delete_date = '0000-00-00 00:00:00'");
		$st->bindValue(':userid', $userid);
		$st->execute();

		$result = $st->fetchAll();

		$_SESSION['data'] = $result;
	}

	public function addEmail($email, $userid, $afzender, $mailserver, $password, $port, $ssl)
	{
		require('controllers/database.php');
		$mailserver_final = "".$mailserver.":".$port."/".$ssl."";

		$st = $db->prepare("INSERT INTO email_accounts(email, user_id, afzender, password, mail_server) VALUES(:email, :userid, :afzender, :password, :mail_server)");
		$st->execute(array(
			':email' => $email, 
			':userid' => $userid, 
			':afzender' => $afzender, 
			':password' => $password, 
			':mail_server' => $mailserver_final
			));

		makesafe($_SESSION['email_add'] = 'success');

		return emailController::index();
	}


	public function multiexplode ($delimiters,$string) {
	    global $launch;

	    $ready = str_replace($delimiters, $delimiters[0], $string);
	    $launch = explode($delimiters[0], $ready);
	    return  $launch;
	}

	public function getSingleEmail($id, $userid)
	{
		global $launch;
		require('controllers/database.php');
		$st = $db->prepare("SELECT * FROM email_accounts WHERE id = :id AND user_id = :userid LIMIT 1");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();

		foreach($result as $server){
			emailController::multiexplode(array(":","/"),$server['mail_server']);
		}

		if(empty($result)) {
			unset($_SESSION['data']);
			return "No records found!";
		}
		else
		{
			$_SESSION['data'] = $result;
		}
	}

	public function editEmail($email, $id, $userid, $password, $mailserver, $afzender, $port, $ssl)
	{
		require('controllers/database.php');
		$mailserver_final = "".$mailserver.":".$port."/".$ssl."";

		if(empty($password)){
		$st = $db->prepare("UPDATE email_accounts SET email = :email, mail_server = :mail_server, afzender = :afzender WHERE id = :id AND user_id = :userid");
		$st->execute(array(
			':email' => $email, 
			':id' => $id, 
			':userid' => $userid,
			':mail_server' => $mailserver_final,
			':afzender' => $afzender
			));
		} else {
		$st = $db->prepare("UPDATE email_accounts SET email = :email, password = :password, mail_server = :mail_server, afzender = :afzender WHERE id = :id AND user_id = :userid");
		$st->execute(array(
			':email' => $email, 
			':id' => $id, 
			':userid' => $userid,
			':password' => $password,
			':mail_server' => $mailserver_final,
			':afzender' => $afzender
			));
		}

		makesafe($_SESSION['email_edit'] = 'success');

		return emailController::index();
	}

	public function deleteEmailAddress($id, $userid)
	{
		require('controllers/database.php');
		$st = $db->prepare("UPDATE email_accounts SET delete_date = :delete_date WHERE id = :id AND user_id = :userid");
		$st->execute(array(
			':delete_date' => date("Y-m-d H:i:s"), 
			':id' => $id, 
			':userid' => $userid
			));

		session_start();

		makesafe($_SESSION['email_delete'] = 'success');

		return emailController::index();
	}

	public function getInbox($id, $userid)
	{
		require('controllers/database.php');

		global $date;
		$i = 0;

		imapController::getImapInbox();

		$_SESSION['dates'] = $date;
	}


	public function getOutbox($id, $userid)
	{
		require('controllers/database.php');
		
		$i = 0;

		$st = $db->prepare("SELECT * FROM outbox WHERE email_id = :id AND user_id = :userid AND concept = 0");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();

		$_SESSION['sent'] = $result;
	}

	public function getConcepten($id, $userid)
	{
		require('controllers/database.php');
		
		$i = 0;

		$st = $db->prepare("SELECT * FROM outbox WHERE email_id = :id AND user_id = :userid AND concept = '1'");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();

		$_SESSION['concepten'] = $result;
	}

		public function getSpam($id, $userid)
	{
		require('controllers/database.php');
		
		global $date;
		$i = 0;

		imapController::getImapTrash();
	}

	public function getTrash($id, $userid)
	{
		require('controllers/database.php');

		global $trash;
		$i = 0;

		imapController::getImapTrash();

		$st = $db->prepare("SELECT * FROM inbox WHERE email_id = :id AND user_id = :userid AND delete_date != '0000-00-00 00:00:00'");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();

		$_SESSION['trash'] = $trash;
	}


	public function deleteEmail($emailid, $userid)
	{
		require('controllers/database.php');
		$st = $db->prepare("UPDATE inbox SET delete_date = :delete_date WHERE id = :emailid AND user_id = :userid");
		$st->execute(array(
			':delete_date' => date("Y-m-d H:i:s"), 
			':emailid' => $emailid, 
			':userid' => $userid));

		session_start();

		makesafe($_SESSION['email_deletion'] = 'success');

		return emailController::index();
	}


	public function forcedeleteEmail($emailid, $userid)
	{
		require('controllers/database.php');
		$st = $db->prepare("DELETE FROM inbox WHERE id = :emailid AND user_id = :userid");
		$st->execute(array(
			':emailid' => $emailid, 
			':userid' => $userid
			));

		session_start();

		makesafe($_SESSION['email_deletion'] = 'success');

		return emailController::index();
	}



	public function restoreEmail($emailid, $userid)
	{
		require('controllers/database.php');
		$st = $db->prepare("UPDATE inbox SET delete_date = :delete_date WHERE id = :emailid AND user_id = :userid");
		$st->execute(array(
			':delete_date' => date("Y-m-d H:i:s"), 
			':emailid' => $emailid, 
			':userid' => $userid
			));

		session_start();

		makesafe($_SESSION['email_deletion'] = 'success');

		return emailController::index();
	}


	public function getEmailMessage($id, $timestamp, $userid)
	{
		require('controllers/database.php');

		$st = $db->prepare("SELECT * FROM inbox WHERE email_id = :id AND user_id = :userid AND timestamp = :timestamp LIMIT 1");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid, 
			':timestamp' => $timestamp
			));

		$result = $st->fetchAll();

		$_SESSION['email_body'] = $result;
	}

	public function getEmailJunk($id, $timestamp, $userid)
	{
		require('controllers/database.php');

		$st = $db->prepare("SELECT * FROM junk WHERE email_id = :id AND user_id = :userid AND timestamp = :timestamp LIMIT 1");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid, 
			':timestamp' => $timestamp
			));

		$result = $st->fetchAll();

		$_SESSION['email_body'] = $result;
	}

	public function getEmailTrash($id, $timestamp, $userid)
	{
		require('controllers/database.php');

		$st = $db->prepare("SELECT * FROM trash WHERE email_id = :id AND user_id = :userid AND timestamp = :timestamp LIMIT 1");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid, 
			':timestamp' => $timestamp
			));

		$result = $st->fetchAll();

		$_SESSION['email_body'] = $result;
	}

	public function moveEmailTrash($id, $timestamp, $userid)
	{
		require('controllers/database.php');
	}

	public function conceptEmail($id, $timestamp, $userid, $message, $receiver, $subject, $bcc, $cc, $priority)
	{
		require('controllers/database.php');

		$st = $db->prepare("INSERT INTO outbox(subject, message, receiver, date, user_id, email_id, bcc, cc, priority, concept) VALUES(:subject, :message, :receiver, :stamp, :user_id, :email_id, :bcc, :cc, :priority, 1)");
		$st->execute(array(
			':subject' => $subject, 
			':message' => $message, 
			':receiver' => $receiver, 
			':stamp' => $timestamp, 
			':user_id' => $userid,
			':email_id' => $id,
			':bcc' => $bcc,
			':cc' => $cc,
			':priority' => $priority
			));
	}

	public function getConceptEmail($emailid, $timestamp, $userid)
	{
		require('controllers/database.php');

		$st = $db->prepare("SELECT * FROM outbox WHERE email_id = :id AND user_id = :userid AND date = :timestamp");
		$st->execute(array(
			':id' => $emailid, 
			':userid' => $userid, 
			':timestamp' => $timestamp
			));

		$result = $st->fetchAll();

		$_SESSION['concept'] = $result;
	}

	public function getSentEmail($emailid, $timestamp, $userid)
	{
		require('controllers/database.php');

		$st = $db->prepare("SELECT * FROM outbox WHERE email_id = :id AND user_id = :userid AND date = :timestamp");
		$st->execute(array(
			':id' => $emailid, 
			':userid' => $userid, 
			':timestamp' => $timestamp
			));

		$result = $st->fetchAll();

		$_SESSION['sent'] = $result;
	}

	public function storeEmail($receiver,$subject, $message,$from,$bijlageArray,$cc,$emailid,$userid)
	{
		require('controllers/database.php');

		if (!filter_var($receiver, FILTER_VALIDATE_EMAIL)) {
			makesafe($_SESSION['email_sent'] = 'email wrong');
		} else {
			
			if(empty($subject)) {
				$subject = "(no subject)";
			}

			$timestamp = time();

			$st = $db->prepare("INSERT INTO outbox(subject, message, receiver, date, user_id, email_id, bcc, cc, priority) VALUES(:subject, :message, :receiver, :stamp, :user_id, :email_id, :bcc, :cc, :priority)");
			$st->execute(array(
				':subject' => $subject, 
				':message' => $message, 
				':receiver' => $receiver, 
				':stamp' => $timestamp, 
				':user_id' => $userid,
				':email_id' => $userid,
				':bcc' => "",
				':cc' => $cc,
				':priority' => ""
				));

			echo sendmailController::sendsmtp($receiver,$subject,$message,"<info@uniquemail.nl>",$bijlageArray,$cc);

			makesafe($_SESSION['email_sent'] = 'success');
		}
		echo emailController::index();
	}

	public function attachment($receiver, $subject, $message, $from, $bijlageArray, $cc, $emailid, $userid, $file)
	{
		$target_dir = "attachments/";
		$target_file = $target_dir . basename($file["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		$bijlageArray[0]["locatie"] = "/attachments/".$file['name']."";
		$bijlageArray[0]["naam"] = $file['name'];

		// Check if image file is a actual image or fake image
		if(isset($file)) {
		    $check = getimagesize($file["tmp_name"]);

		    if($check !== false) {
		        echo "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } else {
		        echo "File is not an image.";
		        $uploadOk = 0;
		    }
		}
		// Check if file already exists
		if (file_exists($target_file)) {
		    echo "Sorry, file already exists.";
		    $uploadOk = 0;
		}
		// Check file size
		if ($file["size"] > 500000) {
		    echo "Sorry, your file is too large.";
		    $uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "xlcs" 
		&& $imageFileType != "txt" && $imageFileType != "php" && $imageFileType != "rar"
		&& $imageFileType != "zip" && $imageFileType != "html" && $imageFileType != "css") 
		{
		    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($file["tmp_name"], $target_file)) {
		        echo "The file ". basename( $file["name"]). " has been uploaded.";
		    } else {
		        echo "Sorry, there was an error uploading your file.";
		    }
		}

		emailController::storeEmail($receiver,$subject,"<body>".$message."</body>","<info@uniquemail.nl>",$bijlageArray,$cc, $emailid, $userid);
	}
}

