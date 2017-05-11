<?php
class imapController{

	public function getImapInbox()
	{
		require('controllers/database.php');

		global $date;
		global $email_message;

		$date = array();
		$id = $_GET['id'];

		//We halen nu de email, mail_server en het wachtwoord uit de database
		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		//De array word nu opgesplit in strings
		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];

		$attachments = array();

		//Hier word een connectie aangeroepen met de mailserver
		$mb = imap_open("{".$mailserver."}", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());

		$messageCount = imap_num_msg($mb);

		//Als er geen nieuwe emails in de imap staan dan word de if overgeslagen en gaan we verder met de spam folder, als er wel emails in staan gaan we nu verder
		if(isset($_GET['refresh']) && $messageCount > 0 ){

			for( $MID = 1; $MID <= $messageCount; $MID++ )
			{
					//We halen nu de email headers, body, html body en structure (attachments) op van de emails.
				   $EmailHeaders = imap_headerinfo( $mb, $MID ); 
				   $Body = imap_fetchbody( $mb, $MID, 1 );
				   $Body_html = imap_fetchbody( $mb, $MID, 2 );
			   	   $structure = imap_fetchstructure($mb, $MID);

			   	   //Als er een attachment in zit dan word deze nu geladen en in apparte variables gezet
					if(isset($structure->parts) && count($structure->parts)) {

						for($i = 0; $i < count($structure->parts); $i++) {

							$attachments[$i] = array(
								'is_attachment' => false,
								'filename' => '',
								'name' => '',
								'attachment' => ''
							);
							 
							//een attachment kan in de object met idfparameters komen of ifparameters, vandaar 2 dezelfde if checks met dezelfde functie. ik zoek nog naar een mooie oplossing om dit te herschrijven
							if($structure->parts[$i]->ifdparameters) {
								foreach($structure->parts[$i]->dparameters as $object) {
									if(strtolower($object->attribute) == 'filename') {
										$attachments[$i]['is_attachment'] = true;
										$attachments[$i]['filename'] = $object->value;
										$date[$MID]['attachment_file'] = $object->value;
									}
								}
							}
							
							//een attachment kan in de object met idfparameters komen of ifparameters, vandaar 2 dezelfde if checks met dezelfde functie. ik zoek nog naar een mooie oplossing om dit te herschrijven
							if($structure->parts[$i]->ifparameters) {
								foreach($structure->parts[$i]->parameters as $object) {
									if(strtolower($object->attribute) == 'name') {
										$attachments[$i]['is_attachment'] = true;
										$attachments[$i]['name'] = $object->value;
										$date[$MID]['attachment_file'] = $object->value;
									}
								}
							}
							
							//Indien er een attachment in de email zat word de path nu opgeslagen in de $attachments variable
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

							//als er geen attachment is gevonden dan geeven we de $date[$MID]['attachment_file'] een value = 0, om eventuele errors te voorkomen
							if($attachments[$i]['is_attachment'] == false || empty($attachments[$i]['is_attachment'])){
								$date[$MID]['attachment_file'] = 0;
							}
							
						}
					}

					//We halen nu de datum en het onderwerp op uit de email headers, als de email geen onderwerp heeft dan krijgt hij automatisch (Geen onderwerp) als onderwerp toegewezen
				   $date[$MID]['date'] = strtotime($EmailHeaders->date);
				   $date[$MID]['subject'] = $EmailHeaders->subject;

				   if($date[$MID]['subject'] == ""){
				   	$date[$MID]['subject'] = "(Geen onderwerp)";
				   }

				   //De cc krijgt nu een lege value
				   $date[$MID]['cc'] = "";
				    //Nu pas word de CC opgehaald, en indien er een CC in de email zat bijgevoegd word de oude value overschreven
				   if(isset($EmailHeaders->ccaddress)){
				   	$date[$MID]['cc'] = $EmailHeaders->ccaddress;
				   }

				   //We halen nu de grootte, de timestamp, de message en de html message van de email op
		   		   $date[$MID]['size'] = $EmailHeaders->Size;
		   		   $date[$MID]['timestamp'] = $EmailHeaders->udate;
		   		   $date[$MID]['message'] = $Body;
		   		   $date[$MID]['message_html'] = $Body_html;
		   		   $date[$MID]['mid'] = $MID;
				 
				if(isset($EmailHeaders->from)){
					foreach($EmailHeaders->from as $from )
					{
						//Nu halen we de afzender op, als de afzender geen naam heeft dus alleen een email adres, dan word de afzendernaam vervangen met het email adres
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
				//Als er een attachment was meegestuurd dan word de file nu opgeslagen dmv een file_put_contents
				file_put_contents('attachments/'.$attachments[1]['filename'].'', $attachments[1]['attachment']);

				imapController::storeImapInbox();
			} else {
				header("Location: http://".$_SERVER["HTTP_HOST"]."/uniquemail/inbox?id=".$id);		
			}
	}

	protected function storeImapInbox()
	{
		require('controllers/database.php');
		
		global $date;

		$emailid = $_GET['id'];
		$userid = 1;

		//De nieuwe emails worden nu in de database opgeslagen
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
				':email_id' => $emailid, 
				':timestamp' => $date[$key]["timestamp"],
				':attachment' => $date[$key]["attachment_file"]
				));
		endforeach;

		imapController::cleanImap($emailid, "");
	}

	public function getImapJunk(){
		require('controllers/database.php');

		global $junk;

		$junk = array();
		$id = $_GET['id'];

		//We halen nu de email, mail_server en het wachtwoord uit de database
		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		//De array word nu opgesplit in strings
		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];

		//Hier word een connectie aangeroepen met de mailserver
		$mb = imap_open("{".$mailserver."}INBOX.Spam", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());

		$messageCount = imap_num_msg($mb);

		//Als er geen nieuwe emails in de imap staan dan word de if overgeslagen en gaan we verder met de spam folder, als er wel emails in staan gaan we nu verder
		if(isset($_GET['refresh']) && $messageCount > 0 ){

				for( $MID = 1; $MID <= $messageCount; $MID++ )
				{
						//We halen nu de email headers, body, html body en structure (attachments) op van de emails.
					   $EmailHeaders = imap_headerinfo( $mb, $MID );
					   $Body = imap_fetchbody( $mb, $MID, 1 );
					   $Body_html = imap_fetchbody( $mb, $MID, 2 );
					   $structure = imap_fetchstructure($mb, $MID);

   			   	   	//Als er een attachment in zit dan word deze nu geladen en in apparte variables gezet
					if(isset($structure->parts) && count($structure->parts)) {

						for($i = 0; $i < count($structure->parts); $i++) {

							$attachments[$i] = array(
								'is_attachment' => false,
								'filename' => '',
								'name' => '',
								'attachment' => ''
							);
							 
							//een attachment kan in de object met idfparameters komen of ifparameters, vandaar 2 dezelfde if checks met dezelfde functie. ik zoek nog naar een mooie oplossing om dit te herschrijven
							if($structure->parts[$i]->ifdparameters) {
								foreach($structure->parts[$i]->dparameters as $object) {
									if(strtolower($object->attribute) == 'filename') {
										$attachments[$i]['is_attachment'] = true;
										$attachments[$i]['filename'] = $object->value;
										$junk[$MID]['attachment_file'] = $object->value;
									}
								}
							}
							
							//een attachment kan in de object met idfparameters komen of ifparameters, vandaar 2 dezelfde if checks met dezelfde functie. ik zoek nog naar een mooie oplossing om dit te herschrijven
							if($structure->parts[$i]->ifparameters) {
								foreach($structure->parts[$i]->parameters as $object) {
									if(strtolower($object->attribute) == 'name') {
										$attachments[$i]['is_attachment'] = true;
										$attachments[$i]['name'] = $object->value;
										$junk[$MID]['attachment_file'] = $object->value;
									}
								}
							}
							
							//Indien er een attachment in de email zat word de path nu opgeslagen in de $attachments variable
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

							//als er geen attachment is gevonden dan geeven we de $junk[$MID]['attachment_file'] een value = 0, om eventuele errors te voorkomen
							if($attachments[$i]['is_attachment'] == false || empty($attachments[$i]['is_attachment'])){
								$junk[$MID]['attachment_file'] = 0;
							}
							
						}
					}

						//We halen nu de datum en het onderwerp op uit de email headers, als de email geen onderwerp heeft dan krijgt hij automatisch (Geen onderwerp) als onderwerp toegewezen
					   $junk[$MID]['date'] = strtotime($EmailHeaders->date);
					   $junk[$MID]['receiver'] = $EmailHeaders->toaddress;
					   $junk[$MID]['subject'] = $EmailHeaders->subject;

					   if($junk[$MID]['subject'] == ""){
					   	$junk[$MID]['subject'] = "(Geen onderwerp)";
					   }

					    //De cc krijgt nu een lege value
					   $junk[$MID]['cc'] = "";
				    	//Nu pas word de CC opgehaald, en indien er een CC in de email zat bijgevoegd word de oude value overschreven
					   if(isset($EmailHeaders->ccaddress)){
					   	$junk[$MID]['cc'] = $EmailHeaders->ccaddress;
					   }

					   //We halen nu de grootte, de timestamp, de message en de html message van de email op
			   		   $junk[$MID]['size'] = $EmailHeaders->Size;
			   		   $junk[$MID]['timestamp'] = $EmailHeaders->udate;
			   		   $junk[$MID]['message'] = $Body;
			   		   $junk[$MID]['message_html'] = $Body_html;

	    				if(isset($EmailHeaders->from)){
							foreach($EmailHeaders->from as $from )
							{
								//Nu halen we de afzender op, als de afzender geen naam heeft dus alleen een email adres, dan word de afzendernaam vervangen met het email adres
								if(isset($from->personal)){
								   $junk[$MID]['personal'] = $from->personal;
								} else {
								   $junk[$MID]['personal'] = "".$from->mailbox."@".$from->host."";
								}
						    $junk[$MID]['from'] = "".$from->mailbox."@".$from->host."";
							}
						}
				}
				rsort($junk);

				imapController::storeImapJunk();
		} else {
			header("Location: http://".$_SERVER["HTTP_HOST"]."/uniquemail/spam?id=".$id);		
		}
	}

	protected function storeImapJunk()
	{
		require('controllers/database.php');
		
		global $junk;

		$emailid = $_GET['id'];
		$userid = 1;
		
		foreach($junk as $key=>$waarde):
			$st = $db->prepare("INSERT IGNORE INTO inbox(subject, message, message_html, sender, sender_email, date, size, user_id, email_id, timestamp, attachment, type) VALUES(:subject, :message, :message_html, :sender, :sender_email, :date, :size, :user_id, :email_id, :timestamp, :attachment, 2)");

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

		imapController::cleanImap($emailid, "INBOX.Spam");

	}

	public function cleanImap($id, $imapfolder){
		require('controllers/database.php');
		
		$userid = 1;

		$st = $db->prepare("SELECT email, mail_server, password FROM email_accounts WHERE id = :id");
		$st->bindValue(':id', $id);
		$st->execute();

		$result = $st->fetchAll();

		$email_account = $result[0][0];
		$mailserver = $result[0][1];
		$password = $result[0][2];


		$mb = imap_open("{".$mailserver."}$imapfolder", $email_account, $password) or die('Failed to connect to the server: <br/>' . imap_last_error());
		$mailboxes = imap_list($mb, "{".$mailserver."}", '*');

		imap_delete($mb, '1:*');
		imap_expunge($mb);

		if($imapfolder == ""){
			header("Location: http://".$_SERVER["HTTP_HOST"]."/uniquemail/inbox?id=".$id);
		} elseif ($imapfolder == "INBOX.Spam"){
			header("Location: http://".$_SERVER["HTTP_HOST"]."/uniquemail/spam?id=".$id);
		}
	}
}

?>