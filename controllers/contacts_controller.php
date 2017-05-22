<?php
class contactController{

	public function getContacts($userid, $id)
	{
		require('controllers/database.php');

		$st = $db->prepare("SELECT * FROM contacts WHERE user_id = :userid AND email_id = :emailid AND deleted = 0");
		$st->execute(array(
			':userid' => $userid,
			':emailid' => $id
			));

		$result = $st->fetchAll();

		$_SESSION['contacts'] = $result;
	}

	public function addContacts($name, $email, $company, $userid, $id)
	{
		require('controllers/database.php');

		$st = $db->prepare("INSERT INTO contacts(name, email, company, user_id, email_id) VALUES(:name, :email, :company, :user_id, :email_id)");
		$st->execute(array(
			':name' => $name, 
			':email' => $email, 
			':company' => $company, 
			':user_id' => $userid, 
			':email_id' => $id
			));

		makesafe($_SESSION['contacts_add'] = 'success');
	}

	public function editContacts($name, $email, $company, $userid, $id, $contactid)
	{
		require('controllers/database.php');

		$st = $db->prepare("UPDATE contacts SET name = :name, company = :company, email = :email WHERE user_id = :userid AND email_id = :emailid AND id = :contactid");
		$st->execute(array(
			':name' => $name, 
			':email' => $email, 
			':company' => $company, 
			':userid' => $userid, 
			':emailid' => $id,
			':contactid' => $contactid
			));

		makesafe($_SESSION['contacts_edit'] = 'success');
	}

	public function getSingleContact($userid, $id, $contactid)
	{
		require('controllers/database.php');

		global $contact;

		$st = $db->prepare("SELECT * FROM contacts WHERE user_id = :userid AND email_id = :emailid AND id = :contactid LIMIT 1");
		$st->execute(array(
			':userid' => $userid,
			':emailid' => $id,
			':contactid' => $contactid
			));

		$contact = $st->fetch(PDO::FETCH_ASSOC);
	}

	public function deleteContact($userid, $id, $contactid)
	{
		require ('controllers/database.php');

		$st = $db->prepare("UPDATE contacts SET deleted = 1 WHERE email_id = :emailid AND user_id = :userid AND id = :contactid");
		$st->execute(array(
			':userid' => $userid, 
			':emailid' => $id, 
			':contactid' => $contactid
			));

		emailController::index();
	}

}

?>