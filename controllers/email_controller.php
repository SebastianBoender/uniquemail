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

	public function lastActive($id, $userid){
		require('controllers/database.php');
		$st = $db->prepare("UPDATE email_accounts set last_active = :currentTime WHERE id = :emailid AND user_id = :userid");
		$st->execute(array( 
			':currentTime' => time(),
			':emailid' => $id,
			':userid' => $userid
			));

		echo time();
	}
}