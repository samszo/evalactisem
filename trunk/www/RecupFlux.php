<?php
   require('library/php-delicious/php-delicious.inc.php');
   require('param/Constantes.php');
   
   define('DELICIOUS_USER', $_GET["login"]);
   define('DELICIOUS_PASS', $_GET["pwd"]);
   
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);

	if ($aPosts = $oDelicious->GetDates($_GET["tag"])) { 
			//echo "<p>".count($aPosts)." posts in this account. Results cached for 10 seconds (by default).</p>";
   		 /*
		 echo "<br/>aPosts=<br/>";
		 print_r($aPosts);
		 echo "<br/>";
		 */

			foreach ($aPosts as $aPost) { 
	            $dates .= "".$aPost['date'].";";
	            $counts .= "".$aPost['count'].";";
	        } 
	    } else {
	        echo $oDelicious->LastErrorString();
	    }
//echo $dates;
//echo $counts;
echo $dates.DELIM.$counts;
?>