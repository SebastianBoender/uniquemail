<div class="sidebar col-md-1 navbar-inverse sidebar_custom">
	<div class="container-fluid">
<?php
$id = $_GET['id'];
?>
	 <ul class="nav navbar-nav sidebar_ul">
      <li><a href="inbox?id=<?=$id?>&refresh=1">Inbox</a></li>
      <li><a href="outbox?id=<?=$id?>&refresh=1">Verzonden</a></li>
	  <li><a href="spam?id=<?=$id?>&refresh=1">Spam</a></li>
      <li><a href="concepten?id=<?=$id?>&refresh=1">Concepten</a></li>
      <li><a href="trash?id=<?=$id?>&refresh=1">Prullenbak</a></li>
     </ul>
	</div>
</div>

