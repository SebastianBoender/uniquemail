<?php
function safeGet($val){
// safe from most dangerous chars 
$val = str_replace(array('&', '"', '<', '>'),array('&amp;', '&quot;',
'&lt;', '&gt;'),$val);
  
return $val;
} 

function makesafe($val)
{
//Trim the inputs, to prevent XSS attacks
  $var = trim($val);
  if(get_magic_quotes_gpc())
  {
  $var = stripslashes($var);
  }
  
  //if(function_exists('mysql_real_escape_string'))
  //{
  // $var = mysql_real_escape_string($var);
  //}else
  //{
     $var = addslashes($var);
  //}
    
    return $var;
}

