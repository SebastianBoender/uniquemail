<?php
class voertuigenController{

	public function getCars(){
		//Deze functie haalt verschillende tabellen op, bijvoorbeeld: inbox, outbox, trash en spam. Ook kan deze functie zoeken op bepaalde zoekwoorden in de geselecteerde map
		require('database.php');

		global $result;
		global $total_pages;
		global $page;

		$results_per_page = 10;	

				//Hier word gekeken op welke page we zitten, aangezien het maximaal te tonen resultaten per pagina op 10 staat
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else { 
			$page = 1; 
		}


		$start_from = ($page - 1) * $results_per_page;

		//De variable $table geeft aan om welke inbox het gaat (inbox, outbox, trash of spam). Als het om inbox of junk gaat dan word eerst alle nieuwe mail van de imap opgehaald via de getImapInbox() of getImapJunk() functie
		if(isset($_GET['soort']) && !empty($_GET['soort']) && isset($_GET['merk']) && !empty($_GET['merk'])){
		    $st = $db->prepare("SELECT * FROM antwoorden_259 WHERE vraag4 = :soort AND vraag3 = :merk LIMIT $start_from, $results_per_page");
		    $st->bindValue(':soort', $_GET['soort']);
		    $st->bindValue(':merk', $_GET['merk']);
		    $st->execute();

		    $result = $st->fetchAll();

		} elseif(isset($_GET['soort']) && !empty($_GET['soort'])) {
		    $st = $db->prepare("SELECT * FROM antwoorden_259 WHERE vraag4 = :soort LIMIT $start_from, $results_per_page");
		    $st->bindValue(':soort', $_GET['soort']);
		    $st->execute();

		    $result = $st->fetchAll();

		} else {
		    $st = $db->prepare("SELECT * FROM antwoorden_259 LIMIT $start_from, $results_per_page");
		    $st->execute();

		    $result = $st->fetchAll();
		}



		//De volgende if statements worden gebruikt om het totale aantal records te tellen, zodat er kan worden berekend over hoeveel pagina's de resultaten weergeven moeten worden
		$st = $db->prepare("SELECT COUNT(vraag1) AS total FROM antwoorden_259 WHERE userid = 28");
		$st->execute();

		$count = $st->fetch(PDO::FETCH_ASSOC);

		//In $total_pages word nu duidelijk hoeveel pagina's er nodig zijn voor alle resultaten
		$total_pages = ceil($count['total'] / $results_per_page);
	}

	public function getNewestCars(){
		require('database.php');

		global $result;

        $st = $db->prepare("SELECT * FROM antwoorden_259 WHERE vraag4 = 'VRACHTWAGEN' ORDER BY koppelid DESC LIMIT 3");
		$st->execute();
    

	    $result = $st->fetchAll();
	}


	public function countCars(){
		require('database.php');
		//Deze functie telt het aantal autos bij de merken en soorten

		global $merk_array;
		global $soort_array;

		$getMerkenQuery = "SELECT COUNT(koppelid), vraag3 FROM `antwoorden_259` group by vraag3";
		$st = $db->prepare($getMerkenQuery);
		$st->execute();

		$merk_array = $st->fetchAll();

		$getSoortQuery = "SELECT COUNT(koppelid), vraag4 FROM `antwoorden_259` group by vraag4";
		$st = $db->prepare($getSoortQuery);
		$st->execute();

		$soort_array = $st->fetchAll();

	}


	public function countDetails(){
		require('database.php');
		//Deze functie telt het aantal autos bij de verschillende eigenschappen als: automaat, handbak, benzine, diesel & laadklep

		global $transmissie_count;
		global $brandstof_count;
		global $laadklep_count;

		$getTransmissieQuery = "SELECT COUNT(koppelid), vraag7 FROM `antwoorden_259` group by vraag7";
		$st = $db->prepare($getTransmissieQuery);
		$st->execute();

		$transmissie_count = $st->fetchAll();

		$getBrandstofQuery = "SELECT COUNT(koppelid), vraag9 FROM `antwoorden_259` group by vraag9";
		$st = $db->prepare($getBrandstofQuery);
		$st->execute();

		$brandstof_count = $st->fetchAll();

		$getLaadklepQuery = "SELECT COUNT(koppelid), vraag10 FROM `antwoorden_259` WHERE vraag10 = 'j' group by vraag10";
		$st = $db->prepare($getLaadklepQuery);
		$st->execute();

		$laadklep_count = $st->fetchAll();
	}

}
?>