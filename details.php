<?php
include('assets/header.php');

//We zetten de valuta eenheid naar Euro
setlocale(LC_MONETARY, 'nl_NL');

//We kijken of het hexon nummer is meegegeven in het id, en vervolgens halen we het voertuig op uit de database
if(isset($_GET['id']) && !empty($_GET['id'])){
    $st = $db->prepare("SELECT * FROM antwoorden_259 WHERE vraag1 = :hexon");
    $st->bindValue(':hexon', $_GET['id']);
    $st->execute();

    $result = $st->fetchAll(); 
}

//We halen hier de previous url op voor de "vorige pagina" knop
$referer = $_SERVER['HTTP_REFERER'];

//De database geeft een value "H" of "A" terug voor de transmissie, dus we moeten zelf het woord afmaken, als het resultaat H is dan zet ik Handbak in de variable $bak, en als het A is dan word het Automaat
foreach($result as $data):
$bak =  "";

if($data['vraag7'] == "H") {
   $bak = "Handgeschakeld"; 
} else {
    $bak = "Automatisch";
}

?>

<!-- Banner block -->
		<div class="banner2" itemscope itemtype="http://schema.org/ImageGallery">
        	<div class="container">
            	<h4>Vertrouwd leasen, huren of het kopen van een truck of trailer</h4>
            </div>
        </div>       
<!-- Vehicles block -->
		<div class="vehicles" itemscope itemtype="http://schema.org/Vehicle">
        	<div class="container">
            	<div class="vehicles_in">
                    <ul class="h-card">
                    <li><a href="#" class="tonen u-url">Alles tonen</a></li>
                    <li><a href="#" class="u-url"><figure><img src="images/vrachtwagens.png" alt="Hover to reveal the location on the map" onmouseover="this.src='images/vrachtwagens_hvr.png';" onmouseout="this.src='images/vrachtwagens.png'";itemprop="image"></figure>achtwagens</a></li>
                    <li><a href="#" class="u-url"><figure><img src="images/trekker.png" alt="Hover to reveal the location on the map" onmouseover="this.src='images/trekker_hvr.png';" onmouseout="this.src='images/trekker.png';" itemprop="image"></figure>Trekker</a></li>
                    <li><a href="#" class="u-url"><figure><img src="images/opleggers.png" alt="Hover to reveal the location on the map" onmouseover="this.src='images/opleggers_hvr.png';" onmouseout="this.src='images/opleggers.png';" itemprop="image"></figure>Opleggers</a></li>
                	<li><a href="#" class="u-url"><figure><img src="images/aanhangers.png" alt="Hover to reveal the location on the map" onmouseover="this.src='images/aanhangers_hvr.png';" onmouseout="this.src='images/aanhangers.png';" itemprop="image"></figure>Aanhangers</a></li>
                    <li><a href="#" class="u-url"><figure><img src="images/auto.png" alt="Hover to reveal the location on the map" onmouseover="this.src='images/auto_hvr.png';" onmouseout="this.src='images/auto.png';" itemprop="image"></figure> Auto’s</a></li>
                    <li><a href="#" class="u-url"><figure><img src="images/bedrijfswagens.png" alt="Hover to reveal the location on the map" onmouseover="this.src='images/bedrijfswagens_hvr.png';" onmouseout="this.src='images/bedrijfswagens.png';" itemprop="image"></figure>Bedrijfswagens </a></li>
                    
                    <li><a href="#" class="u-url"><figure><img src="images/overig.png" alt="Hover to reveal the location on the map" onmouseover="this.src='images/overig_hvr.png';" onmouseout="this.src='images/overig.png';"></figure>Overig</a></li>                    
                </ul>

                    <div class="clear"></div>
                </div>
            </div>
        </div>      
<!-- Slider  block -->      

<?php
//De values van vraag13 en vraag15 zijn json geserializede arrays, als je de functie unserialize() gebruikt dan verander je hem weer terug van string naar PHP array
$pictures = unserialize($data['vraag13']);
$info_array = unserialize($data['vraag15']);

//Ik assign lege values aan de variabelen die we gaan gebruiken, zodat er geen errors kunnen optreden
$brandstof = "";
$verwacht = "";
$gereserveerd = "";
$apk = "";
$schadevoertuig = "";
$demovoertuig = "";
$autotrust_garantie = "";
$vakgarant_premium_occasion = "";
$vwe_occasion_garant_plan = "";
$verhuur = "";
$snelwisselsysteem = "";
$kraan = "";
$versnellingen = "";
$tellerstand_eenheid = "";

//Nu ga ik de values toevoegen aan de variablen, net als bij de transmissie kijk ik nu naar de uitkomst van de database en maak ik het woord af en zet ik het in de goede variable
if($info_array['brandstof'] == "D"){
    $brandstof = "Diesel";
} elseif($info_array['brandstof'] == "B"){
    $brandstof = "Benzine";
} else {
    $brandstof = "Onbekend";
}

if($info_array['verwacht'] == "n"){
    $verwacht = "Nee";
} elseif($info_array['verwacht'] == "j"){
    $verwacht = "Ja";
} else {
    $verwacht = "Onbekend";
}

if($info_array['apk_bij_aflevering'] == "n"){
    $apk = "Nee";
} elseif($info_array['apk_bij_aflevering'] == "j"){
    $apk = "Ja";
} else {
    $apk = "Onbekend";
}

if($info_array['gereserveerd'] == "n"){
    $gereserveerd = "Nee";
} elseif($info_array['verwacht'] == "j"){
    $gereserveerd = "Ja";
} else {
    $gereserveerd = "Onbekend";
}

if($info_array['schadevoertuig'] == "n"){
    $schadevoertuig = "Nee";
} elseif($info_array['schadevoertuig'] == "j"){
    $schadevoertuig = "Ja";
} else {
    $schadevoertuig = "Onbekend";
}

if($info_array['demovoertuig'] == "n"){
    $demovoertuig = "Nee";
} elseif($info_array['demovoertuig'] == "j"){
    $demovoertuig = "Ja";
} else {
    $demovoertuig = "Onbekend";
}

if($info_array['autotrust_garantie'] == "n"){
    $autotrust_garantie = "Nee";
} elseif($info_array['autotrust_garantie'] == "j"){
    $autotrust_garantie = "Ja";
} else {
    $autotrust_garantie = "Onbekend";
}

if($info_array['vakgarant_premium_occasion'] == "n"){
    $vakgarant_premium_occasion = "Nee";
} elseif($info_array['vakgarant_premium_occasion'] == "j"){
    $vakgarant_premium_occasion = "Ja";
} else {
    $vakgarant_premium_occasion = "Onbekend";
}

if($info_array['vwe_occasion_garant_plan'] == "n"){
    $vwe_occasion_garant_plan = "Nee";
} elseif($info_array['vwe_occasion_garant_plan'] == "j"){
    $vwe_occasion_garant_plan = "Ja";
} else {
    $vwe_occasion_garant_plan = "Onbekend";
}

if($info_array['verhuur'] == "n"){
    $verhuur = "Nee";
} elseif($info_array['verhuur'] == "j"){
    $verhuur = "Ja";
} else {
    $verhuur = "Onbekend";
}

if($info_array['snelwisselsysteem'] == "n"){
    $snelwisselsysteem = "Nee";
} elseif($info_array['snelwisselsysteem'] == "j"){
    $snelwisselsysteem = "Ja";
} else {
    $snelwisselsysteem = "Onbekend";
}

if($info_array['kraan'] == "n"){
    $kraan = "Nee";
} elseif($info_array['kraan'] == "j"){
    $kraan = "Ja";
} else {
    $kraan = "Onbekend";
}

if($info_array['tellerstand_eenheid'] == "K"){
    $tellerstand_eenheid = "Kilometer";
} elseif($info_array['kraan'] == "M"){
    $tellerstand_eenheid = "Mile";
} else {
    $tellerstand_eenheid = "Onbekend";
}

if($info_array['aantal_versnellingen'] == ""){
    $versnellingen = "Onbekend";
} 


$values_array_header = array(
  array('Referentienummer',$data["vraag1"]),
  array('Kenteken',$data['vraag2']),
  array('Merk',$data['vraag3']),
  array('Type',$info_array['type']),
  array('1e Registratie',$data['vraa6']),
  array('Vermogen',$info_array['vermogen_motor_pk']),
  array('Kilometerstand',number_format($data['vraag5'])),
  array('Tellerstand eenheid',$tellerstand_eenheid),
  array('Motor',$data['vraag8']),
  array('Basis kleur',$info_array['basiskleur']),
  array('Bouwjaar',$data['vraag11']),
  array('Datum deel 1',$info_array['Datum deel 1']),
  array('Datum deel 1a',$info_array['Datum deel 1a']),
  array('Datum deel 1b',$info_array['Datum deel 1b']),
  array('Motor',$data['vraag8']),
  array('Versnellingsbak',$bak),
  array('Aantal versnellingen',$versnellingen)
  );

?>

	    <div class="midlum bfr" itemscope itemtype="http://schema.org/ImageGallery">
        	<div class="container">
            	<div class="midlum_hd">
                	<div class="midlum_hd_lt">
                    	<a href="<?=$referer?>"><i class="fa fa-angle-left"></i> Terug</a>
                        <h5><em><?=$data['vraag3']?></em>| <?=$info_array['type']?></h5>
                    </div>
                    <div class="midlum_hd_rt">
                    	<ul>
                        	<li><a href="#"><i class="fa fa-print"></i></a></li>
                            <li><a href="#"><i class="fa fa-star-o"></i></a></li>
                            <li><a href="#"><i class="fa fa-envelope-open-o"></i></a></li>
                        </ul>
                    </div>
                	<div class="clear"></div>
                </div>
                <div class="midlum_in" itemscope itemtype="http://schema.org/thumb_slider">
                	<div class="midlum_in_lt thumb_slider">
                                <div class="slider slider-for">
                                <?php
                                foreach($pictures as $picture):
                                ?>
                					<div class="slider_img"><figure><img src="<?=$picture?>" alt="img" itemprop="image"></figure></div>
                        			
                                <?php
                                endforeach;
                                ?>
                    			</div>
                               
                                 <div class="slider slider-nav">
                               
                               <?php
                               //We gaan nu door de $pictures array loopen, zo kunnen we alle fotos in de array weergeven
                                foreach($pictures as $picture2):
                                ?>
                                        <div style="max-width: 100px;" class="thimbnails"><figure><img src="<?=$picture2?>" alt="img" itemprop="image"></figure></div>
            
                                 
                              <?php
                                endforeach;
                                ?>
                                 </div>
                                 <div class="services" itemscope itemtype="http://schema.org/services">
                                 	<h5>Service aanbiedingen</h5>
                                    <ul>
                                    	<li>
                                        	<div class="services_in">
                                            	<figure><img src="images/battery.jpg" alt="img" itemprop="image"></figure>
                                                <h5>2 nieuwe accu's</h5>
                                                <span>Vanaf: €190,-</span>
                                            </div>
                                        </li>
                                        <li>
                                        	<div class="services_in">
                                            	<figure><img src="images/battery.jpg" alt="img" itemprop="image"></figure>
                                                <h5>2 nieuwe accu's</h5>
                                                <span>Vanaf: €190,-</span>
                                            </div>
                                        </li>
                                        <li>
                                        	<div class="services_in">
                                            	<figure><img src="images/battery.jpg" alt="img"></figure>
                                                <h5>2 nieuwe accu's</h5>
                                                <span>Vanaf: €190,-</span>
                                            </div>
                                        </li>
                                    </ul>
                                 </div> 
                	</div>
                    <div class="midlum_in_rt renault_in_txt">
                			
                         <ul>
 <?php
 $o = 0;
 while($o < 18){
    echo '<li><label>'.$values_array_header[$o][0].'</label><span>'.$values_array_header[$o][1].'</span><strong class="clear"></strong>';
 
 if($o == 14){
    echo '      </ul>
                <ul>';
}

if($o == 15){
    echo '<li>&nbsp;<strong class="clear"></strong></li>
            <li><h6>Versnellingsbak</h6><strong class="clear"></strong></li>';
}

 $o++;
 


 }

 ?> 
                            
                        </ul>
                        <div class="clear"></div>
                    	<h4><?=money_format('%.2n', $data['vraag16'])?> <em><?=$info_array['verkoopprijs_particulier_btw']?>. btw</em></h4>
                        <div class="steleen">
                        	<ul>
                            	<li><a href="#" class="verder">Stel een vraag<i class="fa fa-angle-right"></i></a></li>
                                <li><span>Bel direct: </span><p class="phone"><i class="fa fa-phone"></i><a href="tel:310135711024" class="tel_num">+31 (0) 13 571 10 24</a><span class="clear"></span></p></li>
                            </ul>
                        </div>
                        <p><span>Bezoekadres:</span> Minosstraat 8, 5048 CK Tilburg, Bedrijvenpark Vossenberg <a href="#">bekijk op kaart</a></p>
                        <div class="technical">
                        	<h5>Technische details</h5>
                            <p><?=$info_array['standaardopmerkingen']?></p>
                        </div>
                	</div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

<!-- Footer nav  block -->
		<div class="btm_nav">
        	<div class="btm_nav_tp">
            	<div class="container">
                <ul class="h-card">
                	<li><a href="#" class="u-url">Identificatie</a></li>
                    <li><a href="#" class="u-url">Status</a></li>
                    <li><a href="#" class="u-url">Specificaties</a></li>
                    <li><a href="#" class="u-url">Gebruik</a></li>
                    <li><a href="#" class="u-url">Staat</a></li>
                    <li><a href="#" class="u-url">Technisch</a></li>
                    <li><a href="#" class="u-url">Inhoud</a></li>
                    <li><a href="#" class="u-url">Gewicht</a></li>
                    <li><a href="#" class="u-url">Prijs/Kosten</a></li>
                    <li><a href="#" class="u-url">Overig</a></li>
                    <li><a href="#" class="u-url">Lease</a></li>
                    <li><a href="#" class="u-url">Extra</a></li>
                    <li><a href="#" class="u-url">Accessoires</a></li>
                    <li><a href="#" class="u-url">Video's</a></li>
                </ul>
                </div>
            </div>
        	<div class="container">
<?=            	
$values_array_body = array(
  array('Referentienummer',$data["vraag1"]),
  array('1e Registratie',$data['vraag6']),
  array('Vermogen',$data['vraag8']),
  array('Kilometerstand',number_format($data['vraag5'])),
  array('Motor',$data['vraa6']),
  array('Vermogen',$info_array['vermogen_motor_pk']),
  array('Kilometerstand',number_format($data['vraag5'])),
  array('Motor',$data['vraag8']),
  array('Bouwjaar',$data['vraag11']),
  array('Datum deel 1',$info_array['datum_deel_1']),
  array('Datum deel 1a',$data['datum_deel_1a']),
  array('Datum deel 1b',$info_array['datum_deel_1b']),
  array('Carrosserie',$info_array['carrosserie']),
  array('Brandstof',$brandstof),
  array('Type',$info_array['type']),
  array('Verwacht',$verwacht),
  array('Gereserveerd',$gereserveerd),
  array('Basiskleur',$info_array['basiskleur']),
  array('Verkoopprijs particulier',$info_array['verkoopprijs_particulier']),
  array('BTW',$info_array['verkoopprijs_particulier_btw']),
  array('Apk tot',$info_array['apk_tot']),
  array('Apk bij aflevering',$apk),
  array('Massa',$info_array['massa']),
  array('Cilinderinhoud',$info_array['cilinderinhoud']),
  array('Aantal cilinders',$info_array['aantal_cilinders']),
  array('Vermogen motor KW',$info_array['vermogen_motor_kw']),
  array('Vermogen motor PK',$info_array['vermogen_motor_pk']),
  array('Aantal zitplaatsen',$info_array['aantal_zitplaatsen']),
  array('Interieurkleur',$info_array['interieurkleur']),
  array('Bekleding',$info_array['bekleding']),
  array('Aantal sleutels',$info_array['aantal_sleutels']),
  array('Kosten rijklaar',$info_array['kosten_rijklaar']),
  array('Schade voertuig',$schadevoertuig),
  array('Demo voertuig',$demovoertuig),
  array('Autotrust garantie',$autotrust_garantie),
  array('Vakgarant premium occasion',$vakgarant_premium_occasion),
  array('Vwe occasion garant plan',$vwe_occasion_garant_plan),
  array('Wielbasis',$info_array['wielbasis']),
  array('Laadvermogen',$info_array['laadvermogen']),
  array('Aantal assen',$info_array['aantal_assen']),
  array('Assen aangedreven',$info_array['assen_aangedreven']),
  array('Datum binnenkomst',$info_array['datum_binnenkomst']),
  array('Verhuur',$verhuur),
  array('Land',$info_array['land']),
  array('Emissieklasse',$info_array['emissieklasse']),
  array('verplaatsing',$info_array['verplaatsing']),
  array('Staat algemeen',$info_array['staat_algemeen']),
  array('Staat technisch',$info_array['staat_technisch']),
  array('Staat optisch',$info_array['staat_optisch']),
  array('Snelwisselsysteem',$info_array['snelwisselsysteem']),
  array('Laadklep soort',$info_array['laadklep_soort']),
  array('Laadklep capaciteit',$info_array['laadklep_capaciteit']),
  array('Kraan',$kraan),
  array('Versnellingsbak',$bak),
  array('Aantal versnellingen',$info_array['aantal_versnellingen']),
  array('Accessoires',str_replace(',', '<br />', $info_array['accessoires']))
  );
?>


                
                <div class="midlum_in_rt renault_in_txt">
                         <ul>
<?php
$j = 0;
while($j < 57){
    echo '<li><label>'.$values_array_body[$j][0].'</label><span>'.$values_array_body[$j][1].'</span><strong class="clear"></strong></li>';

    if($j == 4){
        echo '<li><h6>Motor</h6><strong class="clear"></strong></li>';
    }

    if($j == 24){
        echo '</ul><ul>';
    }

    if($j == 51){
        echo '<li>&nbsp;<strong class="clear"></strong></li>
                <li><h6>Motorisch</h6><strong class="clear"></strong></li>';
    }

    if($j == 52){
        echo '</span><strong class="clear"></strong></li>';
    }

    if($j == 53){
        echo '<li><h6>accessoires</h6><strong class="clear"></strong></li>';
    }
    $j++;
}
    ?>

       <!--                    <li><label>Referentienummer</label><span><?=$data['vraag1']?></span><strong class="clear"></strong></li>
                            <li><label>1e Registratie</label><span><?=$data['vraag6']?></span><strong class="clear"></strong></li>
                            <li><label>Vermogen</label><span><?=$data['vraag8']?> pk.</span><strong class="clear"></strong></li>
                            <li><label>Kilometerstand</label><span><?=number_format($data['vraag5'])?> km. </span><strong class="clear"></strong></li>
                            <li><h6>Motor</h6><strong class="clear"></strong></li>
                            <li><label>Motor</label><span><?=$data['vraag8']?> pk.</span><strong class="clear"></strong></li>
                            <li><label>Bouwjaar</label><span><?=$data['vraag11']?></span><strong class="clear"></strong></li>

                            <li><label>datum deel 1</label><span><?=$info_array['datum_deel_1']?></span><strong class="clear"></strong></li>

                            <li><label>datum deel 1a</label><span><?=$info_array['datum_deel_1a']?></span><strong class="clear"></strong></li>

                            <li><label>datum deel 1b</label><span><?=$info_array['datum_deel_1b']?></span><strong class="clear"></strong></li>

                            <li><label>carrosserie</label><span><?=$info_array['carrosserie']?></span><strong class="clear"></strong></li>                     

                            <li><label>brandstof</label><span><?=$brandstof?></span><strong class="clear"></strong></li>

                            <li><label>nieuw</label><span><?=$info_array['type']?></span><strong class="clear"></strong></li>

                            <li><label>verwacht</label><span><?=$verwacht?></span><strong class="clear"></strong></li>

                            <li><label>gereserveerd</label><span><?=$gereserveerd?></span><strong class="clear"></strong></li>

                            <li><label>basiskleur</label><span><?=$info_array['basiskleur']?></span><strong class="clear"></strong></li>


                            <li><label>verkoopprijs particulier</label><span><?=$info_array['verkoopprijs_particulier']?></span><strong class="clear"></strong></li>

                            <li><label>BTW</label><span><?=$info_array['verkoopprijs_particulier_btw']?> BTW</span><strong class="clear"></strong></li>

                            <li><label>apk tot</label><span><?=$info_array['apk_tot']?></span><strong class="clear"></strong></li>

                            <li><label>apk bij aflevering</label><span><?=$apk?></span><strong class="clear"></strong></li>

                            <li><label>massa</label><span><?=$info_array['massa']?></span><strong class="clear"></strong></li>

                            <li><label>cilinderinhoud</label><span><?=$info_array['cilinderinhoud']?></span><strong class="clear"></strong></li>

                            <li><label>aantal cilinders</label><span><?=$info_array['aantal_cilinders']?></span><strong class="clear"></strong></li>

                            <li><label>vermogen motor kw</label><span><?=$info_array['vermogen_motor_kw']?></span><strong class="clear"></strong></li>

                            <li><label>vermogen motor pk</label><span><?=$info_array['vermogen_motor_pk']?></span><strong class="clear"></strong></li>

                            <li><label>aantal zitplaatsen</label><span><?=$info_array['aantal_zitplaatsen']?></span><strong class="clear"></strong></li>
</ul>
<ul>
                            <li><label>interieurkleur</label><span><?=$info_array['interieurkleur']?></span><strong class="clear"></strong></li>

                            <li><label>bekleding</label><span><?=$info_array['bekleding']?></span><strong class="clear"></strong></li>

                            <li><label>aantal sleutels</label><span><?=$info_array['aantal_sleutels']?></span><strong class="clear"></strong></li>

                            <li><label>kosten rijklaar</label><span><?=$info_array['kosten_rijklaar']?></span><strong class="clear"></strong></li>

                            <li><label>schadevoertuig</label><span><?=$schadevoertuig?></span><strong class="clear"></strong></li>

                            <li><label>demovoertuig</label><span><?=$demovoertuig?></span><strong class="clear"></strong></li>

                            <li><label>autotrust garantie</label><span><?=$autotrust_garantie?></span><strong class="clear"></strong></li>

                            <li><label>vakgarant premium occasion</label><span><?=$vakgarant_premium_occasion?></span><strong class="clear"></strong></li>

                            <li><label>vwe occasion garantie plan</label><span><?=$vwe_occasion_garant_plan?></span><strong class="clear"></strong></li>

                            <li><label>wielbasis</label><span><?=$info_array['wielbasis']?></span><strong class="clear"></strong></li>

                            <li><label>laadvermogen</label><span><?=$info_array['laadvermogen']?></span><strong class="clear"></strong></li>

                            <li><label>aantal assen</label><span><?=$info_array['aantal_assen']?></span><strong class="clear"></strong></li>

                            <li><label>assen aangedreven</label><span><?=$info_array['assen_aangedreven']?></span><strong class="clear"></strong></li>

                            <li><label>datum binnenkomst</label><span><?=$info_array['datum_binnenkomst']?></span><strong class="clear"></strong></li>

                            <li><label>verhuur</label><span><?=$verhuur?></span><strong class="clear"></strong></li>

                            <li><label>land</label><span><?=$info_array['land']?></span><strong class="clear"></strong></li>

                            <li><label>emissieklasse</label><span><?=$info_array['emissieklasse']?></span><strong class="clear"></strong></li>

                            <li><label>verplaatsing</label><span><?=$info_array['verplaatsing']?></span><strong class="clear"></strong></li>

                            <li><label>staat algemeen</label><span><?=$info_array['staat_algemeen']?></span><strong class="clear"></strong></li>

                            <li><label>staat technisch</label><span><?=$info_array['staat_technisch']?></span><strong class="clear"></strong></li>

                            <li><label>staat optisch</label><span><?=$info_array['staat_optisch']?></span><strong class="clear"></strong></li>

                            <li><label>aandrijving</label><span><?=$info_array['aandrijving']?></span><strong class="clear"></strong></li>

                            <li><label>snelwisselsysteem</label><span><?=$snelwisselsysteem?></span><strong class="clear"></strong></li>

                            <li><label>laadklep soort</label><span><?=$info_array['laadklep_soort']?></span><strong class="clear"></strong></li>

                            <li><label>laadklep capaciteit</label><span><?=$info_array['laadklep_capaciteit']?></span><strong class="clear"></strong></li>

                            <li><label>kraan</label><span><?=$kraan?></span><strong class="clear"></strong></li>
                                
                            <li>&nbsp;<strong class="clear"></strong></li>
                            <li><h6>Motorisch</h6><strong class="clear"></strong></li>

                            <li><label>Versnellingsbak</label><span><?=$bak?>
                            </span><strong class="clear"></strong></li>
                            <li><label>Aantal versnellingen</label><span><?=$info_array['aantal_versnellingen'];?> versnellingen</span><strong class="clear"></strong></li>

                            <li><h6>accessoires</h6><strong class="clear"></strong></li>
                            <li><label>accessoires</label><span><?=str_replace(',', '<br />', $info_array['accessoires'])?></span><strong class="clear"></strong></li>
                            -->
                        </ul>



                                    <div class="clear"></div>
                	</div>
                    <div class="clear"></div>
            </div>
        </div>
        <?php
        endforeach;
        ?>
 <!-- Map block -->
          <div class="map">
            <div id="map" style="height:440px; width: 100%;"></div>
          </div>

          <script>
$(document).ready(function() {
        $('.slider-for').slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
          aut0oplay: false,
          asNavFor: '.slider-nav',
          centerPadding: '2px',
          centerMode:true
             
        });

            $('.slider-nav').slick({
          slidesToShow: 6,
          slidesToScroll: 1,
          asNavFor: '.slider-for',
          dots: false,
          //centerMode: true,
          arrows: false,
          autoplay: false,
          focusOnSelect: true,
          //centerPadding: '2px',
         
        });
});
</script>
 
<?php
include('assets/footer.php');
?>