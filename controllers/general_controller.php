<?php
class generalController{

	public function multiexplode ($delimiters,$string){
		//Deze functie splitst de mailserver string, zodat de mailserver, port en ssl apart kunnen worden ge-echo'd
	    global $launch;

	    $ready = str_replace($delimiters, $delimiters[0], $string);
	    $launch = explode($delimiters[0], $ready);
	    return  $launch;
	}

	public function makelinks($text)
	{
	    $text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
	    '<a target=_blank href="\\1">\\1</a>', $text);
	    $text = eregi_replace('(((f|ht){1}tps://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
	    '<a target=_blank href="\\1">\\1</a>', $text);
	    $text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
	    '\\1<a target=_blank href="[http://\\2"]http://\\2">\\2</a>', $text);
	    $text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})',
	    '<a href="mailto:\\1">\\1</a>', $text);
	   
	    return $text;
	}
}