<?php
   require('library/php-delicious/php-delicious.inc.php');
   require('param/Constantes.php');
   
   define('DELICIOUS_USER', $_GET["login"]);
   define('DELICIOUS_PASS', $_GET["pwd"]);
  
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);
	
	
		if ($aPosts = $oDelicious->GetAllBundles()) {
			foreach ($aPosts as $aPost) { 
	           $tags=$aPost['tags']." ";
			   $name =$aPost['name']." ";
			} 
	    } else {
	        echo $oDelicious->LastErrorString();
		}
	echo $name.DELIM.$tags;
	

?>