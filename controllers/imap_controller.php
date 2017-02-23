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
			   $Body = imap_fetchbody( $mb, $MID, 2 );
		   	   $structure = imap_fetchstructure($mb, $MID);

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

	   		   $date[$MID]['size'] = $EmailHeaders->Size;
	   		   $date[$MID]['timestamp'] = $EmailHeaders->udate;
	   		   $date[$MID]['message'] = $Body;
			 
			foreach($EmailHeaders->from as $from )
			{
			   $date[$MID]['from'] = $from->mailbox;
			   $date[$MID]['host'] = $from->host;
			}
			rsort($date);

		}

				//	   echo '<pre>', print_r($attachments), '<pre>';
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

			   $outbox[$MID]['date'] = strtotime($EmailHeaders->date);
			   $outbox[$MID]['receiver'] = $EmailHeaders->toaddress;
			   $outbox[$MID]['subject'] = $EmailHeaders->subject;

			   if($outbox[$MID]['subject'] == ""){
			   	$outbox[$MID]['subject'] = "(Geen onderwerp)";
			   }

	   		   $outbox[$MID]['size'] = $EmailHeaders->Size;
	   		   $outbox[$MID]['timestamp'] = $EmailHeaders->udate;
	   		   $outbox[$MID]['message'] = $Body;
		}
		rsort($outbox);
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
		}
		rsort($trash);
	}

	protected function storeImapInbox()
	{
		require('controllers/database.php');
		
		global $date;

		$emailid = $_GET['id'];
		$userid = 1;
		
//		$emailid = 40;
		foreach($date as $key=>$waarde):
			$st = $db->prepare("INSERT IGNORE INTO inbox(subject, message, sender, sender_email, date, size, user_id, email_id, timestamp, attachment) VALUES(:subject, :message, :sender, :sender_email, :date, :size, :user_id, :email_id, :timestamp, :attachment)");

			$st->execute(array(
				':subject' => $date[$key]["subject"], 
				':message' => $date[$key]['message'],
				':sender' => 'test', 
				':sender_email' =>  'test', 
				':date' => $date[$key]['date'], 
				':size' => $date[$key]["size"], 
				':user_id' => $userid, 
				':email_id' => $emailid, 
				':timestamp' => $date[$key]["timestamp"],
				':attachment' => $date[$key]["attachment_file"]
				));
		endforeach;
	}

	public function imapSend(){
		//for demo purposes we are gonna send an email to ourselves
		$to = "sebas@youmad.nl";
		$subject = "Test Email";
		$body = "This is only a test.";
		$headers = "From: test@youmad.nl\r\n".
		           "Reply-To: test@youmad.nl\r\n";
		$cc = null;
		$bcc = null;
		$return_path = "test@youmad.nl";
		//send the email using IMAP
		$a = imap_mail($to, $subject, $body, $headers, $cc, $bcc, $return_path);
		echo "Email sent!<br />";
	}

	public function imapMoveToTrash(){
		require('controllers/database.php');

		$emailid = $_GET['id'];
		$userid = 1;

		$EmailHeaders = imap_headerinfo( $mb, $MID );

	}
}

?>