
 <!-- Footer block --> 
          <footer>
              <div class="container">
                  <div class="footer_in">
                      <h6>Kuipers Group B.V.</h6>
                      <p>Minosstraat 8<br>5048 CK Tilburg<br><a href=#>routebeschrijving</a></p>
                      <a href="mailto:info@kuiperstrading.nl" class="mailto">info@kuiperstrading.nl</a>
                      <p class="phone"><em><i class="fa fa-phone"></i></em><a href="tel:310135711024" class="tel_num">+31 (0) 13 571 10 24</a><span class="clear"></span></p>
                  </div>
                  <div class="footer_in footer_in_txt">
                      <h6>Sitemap</h6>
                      <ul>
                          <li><a href="#">Over ons</a></li>
                          <li><a href="#">Support</a></li>
                          <li><a href="#">Lease</a></li>
                          <li><a href="#">Verhuur</a></li>
                          <li><a href="#">Verkoop</a></li>
                          <li><a href="#">Totale voorraad</a></li>
                          <li><a href="#">Werkplaats</a></li>
                          <li><a href="#">Contact</a></li>
                          <li><a href="#">Nieuws</a></li>
                      </ul>
                  </div>
                  <div class="footer_in auto">
                      <h6>Vrachtwagens</h6>
                      <ul>
                          <li><a href="inner.php?soort=VRACHTWAGEN&merk=DAF">DAF vrachtwagen</a></li>
                          <li><a href="inner.php?soort=VRACHTWAGEN&merk=RENAULT">Renault vrachtwagen</a></li>
                          <li><a href="inner.php?soort=VRACHTWAGEN&merk=IVECO">Iveco vrachtwagen</a></li>
                          <li><a href="inner.php?soort=VRACHTWAGEN&merk=MAN">Mercedes-Benz vrachtwagen</a></li>
                          <div class="footer_in trekker">
                          <h6>Aanhangers </h6>
                          <li><a href="#">DAF aanhanger</a></li>
                          <li><a href="#">Renault aanhanger</a></li>
                          <li><a href="#">Iveco aanhanger</a></li>
                          <li><a href="#">Mercedes-Benz aanhanger</a></li>
                          </div>
                      </ul>
                  </div>
                  <div class="footer_in auto opleg">
                      <h6>Trekker</h6>
                      <ul>
                          <li><a href="inner.php?soort=TREKKER&merk=DAF">DAF trekker</a></li>
                          <li><a href="inner.php?soort=TREKKER&merk=Renault">Renault trekker</a></li>
                          <li><a href="inner.php?soort=TREKKER&merk=Iveco">Iveco trekker</a></li>
                          <li><a href="inner.php?soort=TREKKER&merk=MAN">Mercedes-Benz trekker</a></li>
                      </ul>
                      <div class="footer_in trekker">
                          <h6>Auto’s</h6>
                          <ul>
                          <li><a href="inner.php?soort=AUTO&merk=Renault">Renault auto</a></li>
                          <li><a href="inner.php?soort=AUTO&merk=BMW">BMW auto</a></li>
                          </ul>
                          </div>
                  </div>
                  <div class="footer_in auto oppler">
                      <h6>Opleggers</h6>
                      <ul>
                          <li><a href="inner.php?soort=OPLEGGER&merk=DAF">DAF oplegger</a></li>
                          <li><a href="inner.php?soort=OPLEGGER&merk=Renault">Renault oplegger</a></li>
                          <li><a href="inner.php?soort=OPLEGGER&merk=Iveco">Iveco oplegger</a></li>
                          <li><a href="inner.php?soort=OPLEGGER&merk=MAN">Mercedes-Benz oplegger</a></li>
                      </ul>
                      <div class="footer_in trekker">
                          <h6>Bedrijfswagens </h6>
                          <ul>
                          <li><a href="inner.php?soort=BEDRIJF&merk=DAF">DAF bedrijfswagen</a></li>
                          <li><a href="inner.php?soort=BEDRIJF&merk=Renault">Renault bedrijfswagen</a></li>
                          <li><a href="inner.php?soort=BEDRIJF&merk=Iveco">Iveco bedrijfswagen</a></li>
                          <li><a href="inner.php?soort=BEDRIJF&merk=BMW">Mercedes-Benz bedrijfswagen</a></li>
                          </ul>
                          </div> 
                  </div>
                  <div class="clear"></div>
              </div>
              <div class="copy_right">
                  <div class="container">
                     <div class="copy_right_lt">
                        <ul>
                            <li><a href="#">algemene voorwaarden</a></li> 
                            <li><a href="#">disclaimer</a></li> 
                            <li><a href="#">linkpartners</a></li>
                        </ul>
                         <a href="#">©  Kuipers Group B.V. 2017</a>
                     </div>
                     <div class="copy_right_rt">
                        <p>powered by:<a href="#">Unique Webdesign</a></p> 
                     </div>
                  </div>
                  <div class="clear"></div>
              </div>
          </footer>
      </main>
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/slick.js"></script>
<script src="js/customInput.jquery.js"></script>
<script type="text/javascript" src="js/jquery.dd.js"></script>
 <script>
 	$(document).ready(function(e) {
		
		$('.header_top_nav > ul').clone().appendTo('.side_bar');
        $('nav .navigation > ul').clone().appendTo('.side_bar');
		$('#nav-icon').click(function(){
		$('body').toggleClass('open');
	});
	$('.side_bar ul li ul').parent().find('> a').prepend("<span><span></span></span>");
	$('.side_bar ul li a').click(function(e) {
			$(this).parent().find('ul').slideToggle();
			$(this).parent().siblings().find('ul').slideUp();
			$(this).toggleClass("active1");
			$(this).parent().siblings().find('a').removeClass("active1");
		});
		
     //   $('input').customInput();
        $('.resultant_lt ul li h6').click(function(){			
		$(this).parent().find('.soort').slideToggle();
		$(this).toggleClass('current');
       	})
		
		
		
   //     $("select").msDropdown();
		

   //    $("select").msDropdown();
    });
     
 </script>
</body>
</html>