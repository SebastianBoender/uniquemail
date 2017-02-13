<?php
class emailController extends imapController{

	private function index()
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

	public function addEmail($email, $userid, $afzender, $mailserver, $password)
	{
		require('controllers/database.php');
		$st = $db->prepare("INSERT INTO email_accounts(email, user_id, afzender, password, mail_server) VALUES(:email, :userid, :afzender, :password, :mail_server)");
		$st->execute(array(
			':email' => $email, 
			':userid' => $userid, 
			':afzender' => $afzender, 
			':password' => $password, 
			':mail_server' => $mailserver
			));

		makesafe($_SESSION['email_add'] = 'success');

		return emailController::index();
	}

	public function getSingleEmail($id, $userid)
	{
		require('controllers/database.php');
		$st = $db->prepare("SELECT * FROM email_accounts WHERE id = :id AND user_id = :userid LIMIT 1");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();

		if(empty($result)) {
			unset($_SESSION['data']);
			return "No records found!";
		}
		else
		{
			$_SESSION['data'] = $result;
		}
	}

	public function editEmail($email, $id, $userid, $password, $mailserver, $afzender)
	{
		require('controllers/database.php');
		$st = $db->prepare("UPDATE email_accounts SET email = :email, password = :password, mail_server = :mail_server, afzender = :afzender WHERE id = :id AND user_id = :userid");
		$st->execute(array(
			':email' => $email, 
			':id' => $id, 
			':userid' => $userid,
			':password' => $password,
			':mail_server' => $mailserver,
			':afzender' => $afzender
			));

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

		imapController::getImap();
		$st = $db->prepare("SELECT * FROM inbox WHERE email_id = :id AND user_id = :userid AND delete_date = '0000-00-00 00:00:00'");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();
		$_SESSION['data'] = $result;
		$_SESSION['dates'] = $date;
	}


	public function getOutbox($id, $userid)
	{
		require('controllers/database.php');
		$st = $db->prepare("SELECT * FROM outbox WHERE email_id = :id AND user_id = :userid");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();

		$_SESSION['outbox'] = $result;
	}

	public function getTrash($id, $userid)
	{
		require('controllers/database.php');
		$st = $db->prepare("SELECT * FROM inbox WHERE email_id = :id AND user_id = :userid AND delete_date != '0000-00-00 00:00:00'");
		$st->execute(array(
			':id' => $id, 
			':userid' => $userid
			));

		$result = $st->fetchAll();

		$_SESSION['trash'] = $result;
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
}