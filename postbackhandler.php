<?php
try {
	$user = "office3_user28";
	$pass = "yGjl9huW";
    $db = new PDO('mysql:host=localhost;dbname=office3_database28', $user, $pass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$actie = $_POST['actie'];
$userid = 28;

$hexonnr = "";
$kmstand = "";
$merk = "";
$bouwjaar = "";
$voertuigsoort = "";
$registratiejaar = "";
$vermogen = "";
$transmissie = "";
$brandstof = "";
$laadklep = "";
$schadevoertuig = ""; 
$body = "";
$kenteken = "";
$bestandsnaam = "";
$model = "";
$verkoopprijs = "";

if(!empty($_POST['voertuignr_hexon'])){
	$hexonnr = $_POST['voertuignr_hexon'];
}

if(!empty($_POST['tellerstand'])){
	$kmstand = $_POST['tellerstand'];
}

if(!empty($_POST['merk'])){
	$merk = $_POST['merk'];
}

if(!empty($_POST['bouwjaar'])){
	$bouwjaar = $_POST['bouwjaar'];
}

if(!empty($_POST['voertuigsoort'])){
	$voertuigsoort = $_POST['voertuigsoort'];
}

if(!empty($_POST['datum_deel_1'])){
	$registratiejaar = date("Y-m-d", strtotime($_POST['datum_deel_1']));
}

if(!empty($_POST['vermogen_motor_pk'])){
	$vermogen = $_POST['vermogen_motor_pk'];
}

if(!empty($_POST['transmissie'])){
	$transmissie = $_POST['transmissie'];
}

if(!empty($_POST['brandstof'])){
	$brandstof = $_POST['brandstof'];
}

if(!empty($_POST['laadklep'])){
	$laadklep = $_POST['laadklep'];
}

if(!empty($_POST['schadevoertuig'])){
	$schadevoertuig = $_POST['schadevoertuig'];
}

if(!empty($_POST['kenteken'])){
	$kenteken = $_POST['kenteken'];
}

if(!empty($_POST['model'])){
	$model = $_POST['model'];
}

if(!empty($_POST['verkoopprijs_particulier'])){
	$verkoopprijs = $_POST['verkoopprijs_particulier'];
}

if($_POST['afbeeldingen']){
	$fotos = explode(',', $_POST['afbeeldingen']);
	$bestanden = array();

	foreach($fotos as $foto_nr => $foto_url) {
		$bestandnaam = 'fotos/'. $_POST['voertuignr_hexon'] .'-'. $foto_nr .'.jpg';

		$bestanden[] = $bestandnaam;
	}
	$files = serialize($bestanden);
}

$body = serialize($_POST);

switch($actie) {
	case 'add':
		// Controles uitvoeren
		controleer_voertuig();

		$st = $db->prepare("INSERT INTO antwoorden_259(userid, vraag1, vraag2, vraag3, vraag4, vraag5, vraag6, vraag7, vraag8, vraag9, vraag10, vraag11, vraag12, vraag13, vraag14, vraag15, vraag16) VALUES(:userid, :hexon_nr, :kenteken, :merk, :soort, :kmstand, :registratiejaar, :versnellingsbak, :vermogen, :brandstof, :laadklep, :bouwjaar, :schadevoertuig, :image, :model, :data, :verkoopprijs)");
		$st->execute(array(
			':userid' => $userid,
			':hexon_nr' => $hexonnr,
			':kenteken' => $kenteken,
			':merk' => $merk,
			':soort' => $voertuigsoort,
			':kmstand' => $kmstand,
			':registratiejaar' => $registratiejaar,
			':versnellingsbak' => $transmissie,
			':vermogen' => $vermogen,
			':brandstof' => $brandstof,
			':laadklep' => $laadklep,
			':bouwjaar' => $bouwjaar,
			':schadevoertuig' => $schadevoertuig,
			':image' => $files,
			':model' => $model,
			':data' => $body,
			':verkoopprijs' => $verkoopprijs
		));
		
		// Foto's opslaan
		verwerk_fotos();

		// Alles is goed gegaan	
		print("1");			
		break;
		
	case 'change':
		// Controles uitvoeren
		controleer_voertuig();

		// Voertuig wijzigen in database
		$st = $db->prepare("UPDATE antwoorden_259 SET vraag2 = :kenteken, vraag3 = :merk, vraag4 = :soort, vraag5 = :kmstand, vraag6 = :registratiejaar, vraag7 = :versnellingsbak, vraag8 = :vermogen, vraag9 = :brandstof, vraag10 = :laadklep, vraag11 = :bouwjaar, vraag12 = :schadevoertuig, vraag13 = :image, vraag14 = :model, vraag15 = :data, vraag16 = :verkoopprijs WHERE vraag1 = :hexon_nr");
		$st->execute(array(
			':hexon_nr' => $hexonnr,
			':kenteken' => $kenteken,
			':merk' => $merk,
			':soort' => $voertuigsoort,
			':kmstand' => $kmstand,
			':registratiejaar' => $registratiejaar,
			':versnellingsbak' => $transmissie,
			':vermogen' => $vermogen,
			':brandstof' => $brandstof,
			':laadklep' => $laadklep,
			':bouwjaar' => $bouwjaar,
			':schadevoertuig' => $schadevoertuig,
			':image' => $files,
			':model' => $model,
			':data' => $body,
			':verkoopprijs' => $verkoopprijs
		));
		
		// Foto's opnieuw ophalen
		verwerk_fotos();			
		break;
		
	case 'delete':
		// Voertuig verwijderen uit database
		$st = $db->prepare("DELETE FROM antwoorden_259 WHERE vraag1 = :hexon_nr");
		$st->bindValue(':hexon_nr', $hexonnr);
		$st->execute();
		break;
}

function controleer_voertuig() {
	if(empty($_POST['afbeeldingen'])) {
		// Foutmelding teruggeven aan server van Hexon
		print("Op de eigen website zijn alleen voertuigen met foto toegestaan");

	}
}

function verwerk_fotos() {
	$fotos = explode(',', $_POST['afbeeldingen']);
	foreach($fotos as $foto_nr => $foto_url) {
		$bestandsnaam = 'fotos/'. $_POST['voertuignr_hexon'] .'-'. $foto_nr .'.jpg';
		
		$imgdata = file_get_contents($foto_url);
		file_put_contents($bestandsnaam, $imgdata);
	}
}
?>