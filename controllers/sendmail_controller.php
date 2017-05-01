<?php

/*
voorbeeld gebruik bijlagen (sendsmtp() functie):

$bijlageArray = array();
$bijlageArray[0]["locatie"] = "/tmp/tmp.pdf";
$bijlageArray[0]["naam"] = "Bestandsnaam.pdf";
$bijlageArray[1]["locatie"] = "/tmp/tmp2.pdf";
$bijlageArray[1]["naam"] = "Bestandsnaam2.pdf";

sendsmtp("info@uniquewebdesign.nl","Titel","<body>blabla</body>","FROM:Afzender <afzender@uniquewebdesign>",$bijlageArray,"cc@uniquewebdesign.nl");
*/

class sendmailController extends emailController {


	public function attachment($receiver, $subject, $message, $from, $bijlageArray, $stylesheet, $cc, $emailid, $userid, $file)
	{
		//Indien er een of meerdere attachments worden verstuurd, slaat deze functie de attachment(s) op, en geeft ze vervolgens mee aan de volgende functie: StoreEmail()

		$target_dir = "attachments/";
		$count = count($file['name']);
		$o = 0;
		$i = 1;

		//Deze if statement checked of er 1 of meerdere attachments zijn toegevoegd aan de email, en slaat ze vervolgens op op de server en stopt ze in de $bijlageArray[]
		if($count > 1){
			while($i <= $count){
				$target_file = $target_dir . basename($file["name"][$o]);
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$bijlageArray[$o]["locatie"] = "/attachments/".$file['name'][$o]."";
				$bijlageArray[$o]["naam"] = $file['name'][$o];

				move_uploaded_file($file["tmp_name"][$o], $target_file);

				$i++;
				$o++;
			}
		}else{
			$target_file = $target_dir . basename($file["name"][0]);
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			$bijlageArray[0]["locatie"] = "/attachments/".$file['name'][0]."";
			$bijlageArray[0]["naam"] = $file['name'][0];

			move_uploaded_file($file["tmp_name"][0], $target_file);
		}

		sendmailController::storeEmail($receiver,$subject,"<body>".$message."</body>","<info@uniquemail.nl>",$bijlageArray,$cc, $emailid, $userid);
	}

public function storeEmail($receiver,$subject, $message,$from,$bijlageArray,$cc,$emailid,$userid)
	{
		//De storeEmail() functie slaat de email op in de outbox (verzonden berichten)
		require('controllers/database.php');

		if (!filter_var($receiver, FILTER_VALIDATE_EMAIL)) {
			makesafe($_SESSION['email_sent'] = 'email wrong');
		} else {
			
			if(empty($subject)) {
				$subject = "(no subject)";
			}

			$timestamp = time();
			$from = "";
			$cc = "";
			$stylesheet = "";

			$st = $db->prepare("INSERT INTO inbox(subject, message, receiver, date, user_id, email_id, bcc, cc, priority) VALUES(:subject, :message, :receiver, :stamp, :user_id, :email_id, :bcc, :cc, :priority)");
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

			echo sendmailController::sendsmtp($receiver,$subject,$message,$from,$bijlageArray,$cc, $cc, $userid, $emailid);

			makesafe($_SESSION['email_sent'] = 'success');
		}

	}


	function sendsmtp($to,$titel,$html,$from,$bijlageArray,$stylesheet,$cc, $userid, $emailid)
	{
		global $_SERVER;
		//De email word nu met het geselecteerde email adres naar de ontvanger gestuurd d.m.v van een php Mail() functie, indien er bijlage zijn geupload worden deze meegestuurd
		require('controllers/database.php');
		$st = $db->prepare("SELECT email FROM email_accounts WHERE user_id = :userid AND id = :emailid LIMIT 1");
		$st->execute(array(
			':userid' => $userid, 
			':emailid' => $emailid
			));

		$result = $st->fetch();
		$from = $result['email'];

		$html = str_replace('&quot;','"',((($html))));
		$html = nl2br($html);
		
		preg_match_all('/"\"file:(.*)\""/',$html,$matches); 

		for ($i=0; $i< count($matches[1]); $i++) 
		{
		    $html=str_replace('file:'.$matches[1][$i],'cid:'.base64_encode(substr($matches[1][$i],max(strrpos($matches[1][$i],"/"),strrpos($matches[1][$i],"\\"))+1)),$html);  
		}	
			
		$aantal_bijlages = count($bijlageArray);
		
		$headers  = 'MIME-Version: 1.0' . "\n";
		if ($aantal_bijlages > 0)
		{
			$random_hash = md5(date('r', time())); 
			$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\n"; 
		}
		else
		{
			$random_hash = md5(date('r', time())); 
			$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\n"; 
		}
				
		$from = str_replace("(", "<", $from);
		$from = str_replace(")", ">", $from);
		
		$chunks1 = explode("<",$from);
		$aantal_chunks1 = count($chunks1);
		
		if ($aantal_chunks1 > 2)
		{
			$from = "";
			
			foreach ($chunks1 as $key => $waarde)
			{
				if ($key == 0)
				{
					$from .= $waarde;
				}
				else
				{
					if (($key+1) == $aantal_chunks1)
					{
						$from .= "<" . $waarde;
					}
					else
					{
						$from .= "(" . str_replace(">",")",$waarde);
					}
				}
			}
		}
		
		$chunks2 = explode("<",$from);
		$aantal_chunks2 = count($chunks2);
		$laatste_chunk = ($aantal_chunks2-1);
		
		$chunks3 = explode(">",$chunks2[$laatste_chunk]);
		$from_email = $chunks3[0];
		
		$headers .= "From: ". $from . "\n";
		
		if ($cc)
		{
			$headers .= "Cc: " . $cc . "\n";
		}
		
		$headers .= "Reply-To: ". $from . "\n";
		$headers .= "Return-path: ". $from_email . "\n";
		
		$myreturnpath = "-f" . $from_email; 
		
		$headers .= 'X-Mailer: PHP/' . phpversion() . "\n";
		
		$body_chunk2 = explode("<body>",$html);
		
		$text = strip_tags($body_chunk2[1]);
		
		$text = str_replace("\r\n","\n",$text);
		
		$OB="----=_OuterBoundary_000";  
		$IB="----=_InnerBoundery_001";  
		
		ob_start(); //Turn on output buffering 	
		
		echo "--PHP-mixed-" .  $random_hash . "\n";
		echo "Content-Type: multipart/alternative; boundary=\"PHP-alt-" . $random_hash . "\"\n"; 
		echo "\n";
		echo "--PHP-alt-" .  $random_hash . "\n";
		//echo "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
		echo "Content-Type: text/plain; charset=\"utf-8\"\n";
		echo "Content-Transfer-Encoding: 7bit\n";
		echo "\n";
		echo ($text) . "\n";
		echo "\n";
		echo "--PHP-alt-" . $random_hash . "\n";
		//echo "Content-Type: text/html; charset=\"iso-8859-1\"\n"; 
		echo "Content-Type: text/html; charset=\"utf-8\"\n";
		echo "Content-Transfer-Encoding: 7bit\n";
		echo "\n";
		echo ($html) . "\n";
		echo "\n";
		
		if (is_array($bijlageArray))
		{
			echo "--PHP-alt-" . $random_hash . "--\n";
					
			foreach ($bijlageArray as $key=>$waarde)
			{
				$file = $bijlageArray[$key]["locatie"];
				if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $file))
				{
					$ext2 = "";
					$array1 = explode(".",$file);
					$last_chunk = (count($array1)-1);
					$ext = strtolower($array1[$last_chunk]);
					
					$ext2Array = array("pdf"=>"application/pdf","exe"=>"application/octet-stream","zip"=>"application/zip","doc"=>"application/msword","xls"=>"application/vnd.ms-excel","ppt"=>"application/vnd.ms-powerpoint","gif"=>"image/gif","png"=>"image/png","jpeg"=>"image/jpg","jpg"=>"image/jpg","mp3"=>"audio/mpeg","wav"=>"audio/x-wav","mpeg"=>"video/mpeg","mpg"=>"video/mpeg","mpe"=>"video/mpeg","mov"=>"video/quicktime","avi"=>"video/x-msvideo");
					$ext2 = $ext2Array[$ext];
					
					if (!$ext2)
					{
						$ext2 = $ext;
					}
					
					echo "--PHP-mixed-" . $random_hash . "\n";
					
					echo "Content-Type: " . $ext2 . "; name=\"" . $bijlageArray[$key]["naam"] . "\"\n";  
					echo "Content-Transfer-Encoding: base64  \n";
					echo "Content-Disposition: attachment  \n";
					echo "\n";
					$attachment = chunk_split(base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $bijlageArray[$key]["locatie"]))); 
					echo $attachment . "\n";
					echo "\n";
				}
				else
				{
					$this_error = "file [" . $_SERVER["DOCUMENT_ROOT"] . "/" . $file . "] niet gevonden";
				}
			}
			
			echo "--PHP-mixed-" . $random_hash . "--\n"; 			
		}
		
		$html = ob_get_clean(); 
		
		if ($this_error)
		{
			
		}
		elseif (mail($to, $titel, $html, $headers , $myreturnpath))
		{
			return true;
		}
		else
		{
			$this_error = " kon email niet verzenden";
		}
		
		if ($this_error)
		{
			mail("info@uniquewebdesign.nl", "Fout bij $cfg[websitenaam]", "Fout bij SMTP: $this_error", "FROM: info@uniquewebdesign.nl<info@uniquewebdesign.nl>");
			return false;
		}
	}
}
?>