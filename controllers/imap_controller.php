<?php
class imapController{

	public function getImapInbox()
	{
		require('controllers/database.php');

		global $date;
		global $email_message;

		$date = array();
		$id = $_GET['id'];

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];

		$attachments = array();

		$mb = imap_open("{".$mailserver."}", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());
//		$mailboxes = imap_list($mb, "{".$mailserver."}", '*');
//		var_dump($mailboxes);

		$messageCount = imap_num_msg($mb);
		for( $MID = 1; $MID <= $messageCount; $MID++ )
		{
			   $EmailHeaders = imap_headerinfo( $mb, $MID ); 
			   $Body = imap_fetchbody( $mb, $MID, 1 );
			   $Body_html = imap_fetchbody( $mb, $MID, 2 );
		   	   $structure = imap_fetchstructure($mb, $MID);

//		echo '<pre>', print_r($EmailHeaders), '<pre>';

				if(isset($structure->parts) && count($structure->parts)) {

					for($i = 0; $i < count($structure->parts); $i++) {

						$attachments[$i] = array(
							'is_attachment' => false,
							'filename' => '',
							'name' => '',
							'attachment' => ''
						);
						
						if($structure->parts[$i]->ifdparameters) {
							foreach($structure->parts[$i]->dparameters as $object) {
								if(strtolower($object->attribute) == 'filename') {
									$attachments[$i]['is_attachment'] = true;
									$attachments[$i]['filename'] = $object->value;
									$date[$MID]['attachment_file'] = $object->value;
								}
							}
						}
						
						if($structure->parts[$i]->ifparameters) {
							foreach($structure->parts[$i]->parameters as $object) {
								if(strtolower($object->attribute) == 'name') {
									$attachments[$i]['is_attachment'] = true;
									$attachments[$i]['name'] = $object->value;
									$date[$MID]['attachment_file'] = $object->value;
								}
							}
						}
						
						if($attachments[$i]['is_attachment']) {
							$attachments[$i]['attachment'] = imap_fetchbody($mb, $MID, $i+1);
							if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
								$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
								file_put_contents('attachments/'.$attachments[$i]['filename'].'', $attachments[$i]['attachment']);
							}
							elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
								$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
							}
						}

						if($attachments[$i]['is_attachment'] == false || empty($attachments[$i]['is_attachment'])){
							$date[$MID]['attachment_file'] = 0;
						}
						
					}
				}

			   $date[$MID]['date'] = strtotime($EmailHeaders->date);
			   $date[$MID]['subject'] = $EmailHeaders->subject;

			   if($date[$MID]['subject'] == ""){
			   	$date[$MID]['subject'] = "(Geen onderwerp)";
			   }

			   $date[$MID]['cc'] = "";
			   if(isset($EmailHeaders->ccaddress)){
			   	$date[$MID]['cc'] = $EmailHeaders->ccaddress;
			   }
	   		   $date[$MID]['size'] = $EmailHeaders->Size;
	   		   $date[$MID]['timestamp'] = $EmailHeaders->udate;
	   		   $date[$MID]['message'] = $Body;
	   		   $date[$MID]['message_html'] = $Body_html;
	   		   $date[$MID]['mid'] = $MID;
			 
			if(isset($EmailHeaders->from)){
				foreach($EmailHeaders->from as $from )
				{
					if(isset($from->personal)){
					   $date[$MID]['personal'] = $from->personal;
					} else {
					   $date[$MID]['personal'] = "".$from->mailbox."@".$from->host."";
					}
			    $date[$MID]['from'] = "".$from->mailbox."@".$from->host."";
				}

			}
			rsort($date);

		}

//				   echo '<pre>', print_r($EmailHeaders), '<pre>';
				   file_put_contents('attachments/'.$attachments[1]['filename'].'', $attachments[1]['attachment']);

	imapController::storeImapInbox();
	}

	public function getImapOutbox(){
		require('controllers/database.php');

		global $outbox;
		global $email_message;
	
		$outbox = array();
		$id = $_GET['id'];

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];

		$mb = imap_open("{".$mailserver."}INBOX.Sent", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());

		$messageCount = imap_num_msg($mb);
		for( $MID = 1; $MID <= $messageCount; $MID++ )
		{
			   $EmailHeaders = imap_headerinfo( $mb, $MID );
			   $Body = imap_fetchbody( $mb, $MID, 1 );
			   $Body_html = imap_fetchbody( $mb, $MID, 2 );

			   $outbox[$MID]['date'] = strtotime($EmailHeaders->date);
			   $outbox[$MID]['receiver'] = $EmailHeaders->toaddress;
			   $outbox[$MID]['subject'] = $EmailHeaders->subject;

			   if($outbox[$MID]['subject'] == ""){
			   	$outbox[$MID]['subject'] = "(Geen onderwerp)";
			   }

	   		   $outbox[$MID]['size'] = $EmailHeaders->Size;
	   		   $outbox[$MID]['timestamp'] = $EmailHeaders->udate;
	   		   $outbox[$MID]['message'] = $Body;
   	   		   $outbox[$MID]['message_html'] = $Body_html;
		}
		rsort($outbox);
		var_dump($outbox);
	}

	public function getImapTrash(){
		require('controllers/database.php');

		global $trash;
		global $email_message;
	
		$trash = array();
		$id = $_GET['id'];

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];

		$mb = imap_open("{".$mailserver."}INBOX.Trash", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());

		$messageCount = imap_num_msg($mb);
		for( $MID = 1; $MID <= $messageCount; $MID++ )
		{
			   $EmailHeaders = imap_headerinfo( $mb, $MID );
			   $Body = imap_fetchbody( $mb, $MID, 1 );
			   $Body_html = imap_fetchbody( $mb, $MID, 2 );

			   $trash[$MID]['date'] = strtotime($EmailHeaders->date);
			   $trash[$MID]['receiver'] = $EmailHeaders->toaddress;
			   $trash[$MID]['subject'] = $EmailHeaders->subject;

			   if($trash[$MID]['subject'] == ""){
			   	$trash[$MID]['subject'] = "(Geen onderwerp)";
			   }

               foreach($EmailHeaders->from as $from )
			   {
			    $trash[$MID]['from'] = $from->mailbox;
			    $trash[$MID]['host'] = $from->host;
			   }

	   		   $trash[$MID]['size'] = $EmailHeaders->Size;
	   		   $trash[$MID]['timestamp'] = $EmailHeaders->udate;
	   		   $trash[$MID]['message'] = $Body;
	   		   $trash[$MID]['message_html'] = $Body_html;
	   		   $trash[$MID]['mid'] = $MID;
		}
		rsort($trash);

		imapController::storeImapTrash();
	}

	protected function storeImapInbox()
	{
		require('controllers/database.php');
		
		global $date;

		$emailid = $_GET['id'];
		$userid = 1;
		
//		$emailid = 40;
		foreach($date as $key=>$waarde):
			$st = $db->prepare("INSERT IGNORE INTO inbox(subject, message, message_html, sender, sender_email, cc, bcc, date, size, user_id, email_id, timestamp, attachment) VALUES(:subject, :message, :message_html, :sender, :sender_email, :cc, :bcc, :date, :size, :user_id, :email_id, :timestamp, :attachment)");

			$st->execute(array(
				':subject' => $date[$key]["subject"], 
				':message' => $date[$key]['message'],
				':message_html' => quoted_printable_decode($date[$key]['message_html']),
				':sender' => $date[$key]['personal'], 
				':sender_email' =>  $date[$key]['from'], 
				':cc' => $date[$key]["cc"],
				':bcc' => '',
				':date' => $date[$key]['date'], 
				':size' => $date[$key]["size"], 
				':user_id' => $userid, 
				':email_id' => $emailid, 
				':timestamp' => $date[$key]["timestamp"],
				':attachment' => $date[$key]["attachment_file"]
				));
		endforeach;
	}

	public function getImapJunk(){
		require('controllers/database.php');

		global $junk;

		$junk = array();
		$id = $_GET['id'];

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];

		$mb = imap_open("{".$mailserver."}INBOX.Spam", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());
//		$mailboxes = imap_list($mb, "{".$mailserver."}", '*');
//		var_dump($mb);

		$messageCount = imap_num_msg($mb);
		for( $MID = 1; $MID <= $messageCount; $MID++ )
		{
			   $EmailHeaders = imap_headerinfo( $mb, $MID );
			   $Body = imap_fetchbody( $mb, $MID, 1 );
			   $Body_html = imap_fetchbody( $mb, $MID, 2 );

			   $junk[$MID]['date'] = strtotime($EmailHeaders->date);
			   $junk[$MID]['receiver'] = $EmailHeaders->toaddress;
			   $junk[$MID]['subject'] = $EmailHeaders->subject;

			   if($junk[$MID]['subject'] == ""){
			   	$junk[$MID]['subject'] = "(Geen onderwerp)";
			   }

	   		   $junk[$MID]['size'] = $EmailHeaders->Size;
	   		   $junk[$MID]['timestamp'] = $EmailHeaders->udate;
	   		   $junk[$MID]['message'] = $Body;
	   		   $junk[$MID]['message_html'] = $Body_html;

	   		    foreach($EmailHeaders->from as $from )
				{
				   $junk[$MID]['from'] = $from->mailbox;
				   $junk[$MID]['host'] = $from->host;
				}

				$junk[$MID]['attachment_file'] = "";
		}
		rsort($junk);

		imapController::storeImapJunk();

		$_SESSION['junk'] = $junk;
	}

	protected function storeImapJunk()
	{
		require('controllers/database.php');
		
		global $junk;

		$emailid = $_GET['id'];
		$userid = 1;
		
//		$emailid = 40;
		foreach($junk as $key=>$waarde):
			$st = $db->prepare("INSERT IGNORE INTO junk(subject, message, message_html, sender, sender_email, date, size, user_id, email_id, timestamp, attachment) VALUES(:subject, :message, :message_html, :sender, :sender_email, :date, :size, :user_id, :email_id, :timestamp, :attachment)");

			$st->execute(array(
				':subject' => $junk[$key]["subject"], 
				':message' => $junk[$key]['message'],
				':message_html' => $junk[$key]['message_html'],
				':sender' => 'test', 
				':sender_email' =>  'test', 
				':date' => $junk[$key]['date'], 
				':size' => $junk[$key]["size"], 
				':user_id' => $userid, 
				':email_id' => $emailid, 
				':timestamp' => $junk[$key]["timestamp"],
				':attachment' => $junk[$key]["attachment_file"]
				));
		endforeach;
	}


	protected function storeImapTrash()
	{
		require('controllers/database.php');
		
		global $trash;

		$emailid = $_GET['id'];
		$userid = 1;
		
//		$emailid = 40;
		foreach($trash as $key=>$waarde):
			$st = $db->prepare("INSERT IGNORE INTO trash(subject, message, message_html, sender, sender_email, date, size, user_id, email_id, timestamp) VALUES(:subject, :message, :message_html, :sender, :sender_email, :date, :size, :user_id, :email_id, :timestamp)");

			$st->execute(array(
				':subject' => $trash[$key]["subject"],
				':message' => $trash[$key]['message'],
				':message_html' => $trash[$key]['message_html'],
				':sender' => 'test',
				':sender_email' =>  'test',
				':date' => $trash[$key]['date'],
				':size' => $trash[$key]["size"],
				':user_id' => $userid,
				':email_id' => $emailid,
				':timestamp' => $trash[$key]["timestamp"]
				));
		endforeach;
	}


	public function imapMoveToTrash($emailid, $id){
		require('controllers/database.php');

		$userid = 1;

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];


		$mb = imap_open("{".$mailserver."}", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());
		$mailboxes = imap_list($mb, "{".$mailserver."}", '*');

		var_dump($mailboxes, $emailid, $id);

		imap_mail_move($mb, $emailid, "INBOX.Trash");
		imap_expunge($mb);

		return emailController::index();
	}


	public function deleteFromTrash($emailid, $id){
		require('controllers/database.php');
		
		$userid = 1;

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];


		$mb = imap_open("{".$mailserver."}INBOX.Trash", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());
		$mailboxes = imap_list($mb, "{".$mailserver."}", '*');

		var_dump($mailboxes, $emailid, $id);

		imap_delete($mb, $emailid);
		imap_expunge($mb);

		return emailController::index();
	}


	public function undelete($emailid, $id){
		require('controllers/database.php');
		
		$userid = 1;

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];


		$mb = imap_open("{".$mailserver."}INBOX.Trash", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());
		$mailboxes = imap_list($mb, "{".$mailserver."}", '*');

		var_dump($mailboxes, $emailid, $id);

		imap_mail_move($mb, $emailid, "INBOX");
		imap_expunge($mb);

		return emailController::index();
	}
}

?>