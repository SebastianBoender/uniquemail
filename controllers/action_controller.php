<?php
class actionController extends inboxController{

	public function trashMessage($id, $userid, $emailid){
		//Deze functie verwijdert een email en zet het in de prullenbak, en plaatst het terug naar de inbox of spam indien het al in de prullenbak staat
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
		//Deze functie verwijdert een email uit de prullenbak
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

	public function flagEmail($id, $timestamp, $userid, $table)
	{
		//Deze functie kan een email markeren met een vlag, zodat hij altijd bovenaan staat
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
		//Deze functie markeert de geselecteerde berichten als gelezen
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

	public function markunRead($ids, $userid, $id)
	{
		//Deze functie markeert de geselecteerde emails als ongelezen
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

	public function saveconceptEmail($id, $timestamp, $userid, $message, $receiver, $subject, $bcc, $cc, $priority)
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
}