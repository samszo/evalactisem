<?php
   require('library/php-delicious/php-delicious.inc.php');
   
   define('DELICIOUS_USER', 'amelbou');
   define('DELICIOUS_PASS', 'lemabou');
   
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);

	if ($aPosts = $oDelicious->GetDates("")) { 
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
$lien='http://localhost/mundilogiweb/ieml/library/histogrammes/stats.php?large=400';
$lien.='&haut=300';
$lien.='&titre='.urlencode('Posts de '.DELICIOUS_USER.' par date');
$lien.='&donnees='.$counts;
$lien.='&noms='.$dates;
$lien.='&type=pie';
$lien.='&col1=yellow';
$lien.='&col2=red';
$lien.='&col3=blue';
$lien.='&col4=black';
//echo $lien;

            $oCurl = curl_init($lien);
            // set options
           // curl_setopt($oCurl, CURLOPT_HEADER, true);
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			//echo $sCmd."<br/>";
			//$arrInfos = curl_getinfo($ch);
			//print_r($arrInfos);
			//echo "sResult=<br/>";
			//print_r($sResult);
			//echo "<br/>";
			//fin ajout samszo

            // request URL
            $sResult = curl_exec($oCurl);
            // close session
            curl_close($oCurl);
header("Content-type: image/svg+xml");
echo $sResult;
?>