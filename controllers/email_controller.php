<?php
class emailController extends imapController{

	public function index()
	{
		//Deze functie redirect terug naar de vorige pagina indien er een vorige pagina was, anders redirect hij naar de homepage
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
		//Deze functie haalt alle email accounts (inboxen) van de klant op
		require('controllers/database.php');
		$st = $db->prepare("SELECT * FROM email_accounts WHERE user_id = :userid AND delete_date = '0000-00-00 00:00:00'");
		$st->bindValue(':userid', $userid);
		$st->execute();

		$result = $st->fetchAll();

		$_SESSION['data'] = $result;
	}

	public function addEmail($email, $userid, $afzender, $mailserver, $password, $port, $ssl)
	{
		//Met deze functie kan de klant nieuwe email accounts (inboxen) toevoegen aan de database
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

	public function getSingleEmail($id, $userid)
	{
		//Deze functie haalt het geselecteerde email account uit de database als de klant een email wilt editten
		global $launch;
		require('controllers/database.php');
		$st = $db->prepare("SELECT * FROM email_accounts WHERE id = :id AND user_id = :userid LIMIT 1");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();

		foreach($result as $server){
			generalController::multiexplode(array(":","/"),$server['mail_server']);
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
		//Als de klant een email account (inbox) bewerkt, dan word deze functie gebruikt om de nieuwe gegevens op te slaan in de database
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
		//Deze functie verwijderd het geselecteerde email account uit de database
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

	public function getConcepten($id, $userid)
	{
		//Deze functie haalt alle emails op uit de database die opgeslagen zijn als concept
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

		$st = $db->prepare("SELECT * FROM inbox WHERE email_id = :id AND user_id = :userid AND timestamp = :timestamp AND type = 1 LIMIT 1; 
							UPDATE inbox
							SET unread = 0
							WHERE email_id = :id AND user_id = :userid AND timestamp = :timestamp LIMIT 1;");
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

		$st = $db->prepare("SELECT * FROM inbox WHERE email_id = :id AND user_id = :userid AND timestamp = :timestamp AND type = 2 LIMIT 1;
							UPDATE inbox
							SET unread = 0
							WHERE email_id = :id AND user_id = :userid AND timestamp = :timestamp AND type = 2 LIMIT 1;");
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

	public function conceptEmail($id, $timestamp, $userid, $message, $receiver, $subject, $bcc, $cc, $priority)
	{
		//Deze functie slaat een email op in de database als concept
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
		//Deze functie haalt het geselecteerde concept op uit de database zodat de gebruiker hem kan bewerken
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

	public function flagEmail($id, $timestamp, $userid, $table)
	{
		require('controllers/database.php');

		if($table == "inbox"){
			$type = 1;
		} else {
			$type = 2;
		}

		$st = $db->prepare("UPDATE inbox SET flag = CASE 
							WHEN flag = 0 THEN 1
							ELSE flag = 0
							END WHERE email_id = :id AND user_id = :userid AND timestamp = :timestamp AND type = :type LIMIT 1");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid, 
			':timestamp' => $timestamp,
			':type' => $type
			));

		return emailController::index();
	}

	public function markRead($ids, $userid, $id)
	{
		require('controllers/database.php');

		$st = $db->prepare("UPDATE inbox SET unread = CASE 
							WHEN unread = 1 THEN 0
							ELSE unread = 0
							END WHERE timestamp IN (".implode(',',$ids).")");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid,
			':timestamp' => implode(',',$ids)
			));

		return emailController::index();
	}

	public function trashMessage($id, $userid, $emailid){
		require('controllers/database.php');

		$st = $db->prepare("UPDATE inbox SET deleted = CASE 
							WHEN deleted = 1 THEN 0
							ELSE deleted = 0
							END WHERE timestamp = :timestamp AND user_id = :userid AND email_id = :id");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid,
			':timestamp' => $emailid
			));

		return emailController::index();
	}

	public function deletetrashMessage($id, $userid, $emailid){
		require('controllers/database.php');

		$st = $db->prepare("UPDATE inbox SET deleted = 2
							WHERE timestamp = :timestamp AND user_id = :userid AND email_id = :id");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid,
			':timestamp' => $emailid
			));

		return emailController::index();
	}

	public function markunRead($ids, $userid, $id)
	{
		//Deze functie markeert de geselecteerde emails als gelezen
		require('controllers/database.php');

		$st = $db->prepare("UPDATE inbox SET unread = CASE 
							WHEN unread = 0 THEN 1
							ELSE unread = 1
							END WHERE timestamp IN (".implode(',',$ids).")");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid,
			':timestamp' => implode(',',$ids)
			));

		return emailController::index();
	}

	public function getInboxes($table, $id, $userid){
		require('controllers/database.php');

		global $paginate_result;
		global $total_pages;
		global $page;

		$results_per_page = 10;	

		if($table == "inbox"){
			$type = 1;
			imapController::getImapInbox();
		} elseif($table == "spam"){
			$type = 2;
			imapController::getImapJunk();
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else { 
			$page = 1; 
		}

		$start_from = ($page - 1) * $results_per_page;

		if(isset($_POST['search'])){
			if(!empty($_POST['searchquery'])){
			    $searchquery = $_POST['searchquery'];

			    if($table == "inbox" || $table == "spam"){
				    $st = $db->prepare("SELECT * FROM inbox WHERE type = $type AND user_id = :userid AND email_id = :id AND deleted = 0 AND subject LIKE :searchquery ORDER BY flag DESC LIMIT $start_from, $results_per_page");
					$st->execute(array(
						':id' => $id,
						':userid' => $userid,
						':searchquery' => '%'.$searchquery.'%'
						));

					$paginate_result = $st->fetchAll();

				} elseif($table == "trash"){
					$st = $db->prepare("SELECT * FROM inbox WHERE user_id = :userid AND email_id = :id AND deleted = 1 AND subject LIKE :searchquery ORDER BY flag DESC LIMIT $start_from, $results_per_page");
					$st->execute(array(
						':id' => $id,
						':userid' => $userid,
						':searchquery' => '%'.$searchquery.'%'
						));

					$paginate_result = $st->fetchAll();
						
				} elseif($table == "outbox"){
					$st = $db->prepare("SELECT * FROM outbox WHERE user_id = :userid AND email_id = :id AND subject LIKE :searchquery ORDER BY flag DESC LIMIT $start_from, $results_per_page");
					$st->execute(array(
						':id' => $id,
						':userid' => $userid,
						':searchquery' => '%'.$searchquery.'%'
						));

					$paginate_result = $st->fetchAll();
				}
		  }

		} elseif($table == "inbox" || $table == "spam") {
			$st = $db->prepare("SELECT * FROM inbox WHERE type = $type AND email_id = $id AND user_id = $userid AND deleted = 0 ORDER BY flag DESC LIMIT $start_from, $results_per_page");
			$st->execute();

			$paginate_result = $st->fetchAll();
		} elseif($table == "trash"){
			$st = $db->prepare("SELECT * FROM inbox WHERE email_id = $id AND user_id = $userid AND deleted = 1 ORDER BY flag DESC LIMIT $start_from, $results_per_page");
			$st->execute();

			$paginate_result = $st->fetchAll();
		} elseif($table == "outbox"){
			$st = $db->prepare("SELECT * FROM outbox WHERE email_id = $id AND user_id = $userid AND concept = 0 ORDER BY flag DESC LIMIT $start_from, $results_per_page");
			$st->execute();

			$paginate_result = $st->fetchAll();
		}

		if($table == "inbox" || $table == "spam"){
			$st = $db->prepare("SELECT COUNT(ID) AS total FROM inbox WHERE type = $type AND deleted = 0");
			$st->execute();
		} elseif ($table == "trash"){
			$st = $db->prepare("SELECT COUNT(ID) AS total FROM inbox WHERE deleted = 1");
			$st->execute();
		} elseif ($table == "outbox"){
			$st = $db->prepare("SELECT COUNT(ID) AS total FROM outbox WHERE concept = 0");
			$st->execute();
		}

		$count = $st->fetch(PDO::FETCH_ASSOC);

		$total_pages = ceil($count['total'] / $results_per_page);
	
	}
}