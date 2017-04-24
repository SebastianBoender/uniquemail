<?php
class contactController{

	public function getContacts($userid, $id)
	{
		require('controllers/database.php');

		$st = $db->prepare("SELECT * FROM contacts WHERE user_id = :userid AND email_id = :emailid");
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

}

?>