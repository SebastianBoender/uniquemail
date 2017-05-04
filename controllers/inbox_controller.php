<?php
class inboxController extends imapController{

	public function getInboxes($table, $id, $userid){
		//Deze functie haalt verschillende tabellen op, bijvoorbeeld: inbox, outbox, trash en spam. Ook kan deze functie zoeken op bepaalde zoekwoorden in de geselecteerde map
		require('controllers/database.php');

		emailController::lastActive($id, $userid);

		global $paginate_result;
		global $total_pages;
		global $page;

		$results_per_page = 10;	

		//De variable $table geeft aan om welke inbox het gaat (inbox, outbox, trash of spam). Als het om inbox of junk gaat dan word eerst alle nieuwe mail van de imap opgehaald via de getImapInbox() of getImapJunk() functie
		if($table == "inbox"){
			$type = 1;
			if(isset($_GET['refresh'])){
				if($_GET['refresh'] == 1){
				imapController::getImapInbox();
				}
			}
		} elseif($table == "spam"){
			$type = 2;
			if(isset($_GET['refresh'])){
				if($_GET['refresh'] == 1){
				imapController::getImapJunk();
				}
			}
		}

		//Hier word gekeken op welke page we zitten, aangezien het maximaal te tonen resultaten per pagina op 10 staat
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else { 
			$page = 1; 
		}

		$start_from = ($page - 1) * $results_per_page;

		//Als er een zoekwoord is ingevuld, word er doormiddel van deze functie gezocht in de geselecteerde inbox (inbox, spam, outbox of trash)
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
		//Als er geen zoekwoord is opgegeven dan word de gehele inbox opgehaald, de volgende if statements maken onderscheid tussen inbox/spam, outbox en trash
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

		//De volgende if statements worden gebruikt om het totale aantal records te tellen, zodat er kan worden berekend over hoeveel pagina's de resultaten weergeven moeten worden
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

		//In $total_pages word nu duidelijk hoeveel pagina's er nodig zijn voor alle resultaten
		$total_pages = ceil($count['total'] / $results_per_page);
	
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

	
	public function getEmailMessage($id, $timestamp, $userid)
	//Deze functie
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
		//Deze functie haalt een junk email op uit de database zodat je hem kan uitlezen
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
		//Deze functie haalt een trash message op uit de database zodat je hem kan uitlezen
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
		//De 
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
}