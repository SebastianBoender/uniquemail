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

		$mb = imap_open("{".$mailserver."}", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());
//		$mailboxes = imap_list($mb, "{".$mailserver."}", '*');
//		var_dump($mailboxes);

		$messageCount = imap_num_msg($mb);
		for( $MID = 1; $MID <= $messageCount; $MID++ )
		{
			   $EmailHeaders = imap_headerinfo( $mb, $MID );
			   $Body = imap_fetchbody( $mb, $MID, 1 );
		   	   $structure = imap_fetchstructure($mb, $MID);

			   $date[$MID]['date'] = $EmailHeaders->date;
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

	imapController::storeImapInbox();
	echo '<pre>', print_r($structure), '<pre>';
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

			   $outbox[$MID]['date'] = $EmailHeaders->date;
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

	protected function storeImapInbox()
	{
		require('controllers/database.php');
		
		global $date;

		$emailid = $_GET['id'];
		$userid = 1;
		
//		$emailid = 40;
		foreach($date as $key=>$waarde):
			$st = $db->prepare("INSERT IGNORE INTO inbox(subject, message, sender, sender_email, date, size, user_id, email_id, timestamp) VALUES(:subject, :message, :sender, :sender_email, :date, :size, :user_id, :email_id, :timestamp)");

			$st->execute(array(
				':subject' => $date[$key]["subject"], 
				':message' => $date[$key]['message'],
				':sender' => 'test', 
				':sender_email' =>  'test', 
				':date' => $date[$key]['date'], 
				':size' => $date[$key]["size"], 
				':user_id' => $userid, 
				':email_id' => $emailid, 
				':timestamp' => $date[$key]["timestamp"]
				));
		endforeach;
	}
}

?>