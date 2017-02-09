<?php
	$mb = imap_open("{mail.mijndomein.nl:993/ssl}","test@youmad.nl", "Test1234" );

	$messageCount = imap_num_msg($mb);
	for( $MID = 1; $MID <= $messageCount; $MID++ )
	{
	   $EmailHeaders = imap_headerinfo( $mb, $MID );
	   $Body = imap_fetchbody( $mb, $MID, 1 );

	   echo '<pre>', print_r($EmailHeaders), '<pre>';
	}

var_dump($messageCount);

$array = json_decode(json_encode($EmailHeaders), true);


echo $EmailHeaders->date;

echo $Body;

foreach($EmailHeaders->to as $to ){
	echo $to->personal;
}

?>