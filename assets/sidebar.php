<div class="sidebar col-md-1 navbar-inverse sidebar_custom">
	<div class="container-fluid">
<?php
$id = $_GET['id'];
?>
	 <ul class="nav navbar-nav sidebar_ul">
      <li><a href="inbox?id=<?=$id?>">Inbox</a></li>
      <li><a href="outbox?id=<?=$id?>">Verzonden</a></li>
	  <li><a href="spam?id=<?=$id?>">Spam</a></li>
      <li><a href="concepten?id=<?=$id?>">Concepten</a></li>
      <li><a href="trash?id=<?=$id?>">Prullenbak</a></li>
     </ul>
	</div>
</div>

