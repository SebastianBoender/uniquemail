<?php

$secret = '0c130e57504eaef0c8487351b891bd3b';
if($_SERVER["argv"][1] != $secret)
{
    die("Invalid Access Denied");
}

require('../controllers/database.php');
require('../controllers/imap_controller.php');

global $date;
global $email_message;

$date = array();
$userid = 1;

$oldTime = strtotime("-30 minutes");

$st = $db->prepare("SELECT * FROM email_accounts WHERE last_active > :oldTime");
$st->bindValue(':oldTime', $oldTime);
$st->execute();

$result = $st->fetchAll();

if($result){
	foreach($result as $account){

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :accountid");
		$st->bindValue(':accountid', $account['id']);
		$st->execute();

		$account_data = $st->fetchAll();

		$email_account = $account_data[0][0];
		$mailserver = $account_data[0][1];
		$password = $account_data[0][2];

		$mb = imap_open("{".$mailserver."}", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());

		$messageCount = imap_num_msg($mb);

		if($messageCount > 0 ){
			for( $MID = 1; $MID <= $messageCount; $MID++ )
			{
				   $EmailHeaders = imap_headerinfo( $mb, $MID ); 
				   $Body = imap_fetchbody( $mb, $MID, 1 );
				   $Body_html = imap_fetchbody( $mb, $MID, 2 );
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
				
				if($attachments){
					file_put_contents('attachments/'.$attachments[1]['filename'].'', $attachments[1]['attachment']);
				}

				if($date){
					foreach($date as $key=>$waarde):
						$st = $db->prepare("INSERT IGNORE INTO inbox(subject, message, message_html, sender, sender_email, cc, bcc, date, size, user_id, email_id, timestamp, attachment, type) VALUES(:subject, :message, :message_html, :sender, :sender_email, :cc, :bcc, :date, :size, :user_id, :email_id, :timestamp, :attachment, 1)");

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
							':email_id' => $account['id'], 
							':timestamp' => $date[$key]["timestamp"],
							':attachment' => $date[$key]["attachment_file"]
							));
					endforeach;

					imap_delete($mb, '1:*');
					imap_expunge($mb);
				}
		}
	}
}

?>