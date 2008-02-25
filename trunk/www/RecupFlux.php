<?php
   require('library/php-delicious/php-delicious.inc.php');
   require('param/Constantes.php');
   
   define('DELICIOUS_USER', $_GET["login"]);
   define('DELICIOUS_PASS', $_GET["pwd"]);
   $requette=$_GET["req"];
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);
	
	if($requette="GetAllTags"){
		
		if ($aPosts = $oDelicious->GetAllTags()) { 
			/*echo "<p>".count($aPosts)." posts in this account. Results cached for 10 seconds (by default).</p>";
   		 
		 echo "<br/>aPosts=<br/>";
		 print_r($aPosts);
		 echo "<br/>";*/
			
			foreach ($aPosts as $aPost) { 
	            echo $aPost['tag']." ";
			} 
	    } else {
	        echo $oDelicious->LastErrorString();
	    }
	}
	
	if($requette=="GetRecentPosts"){
		if ($aPosts = $oDelicious->GetRecentPosts()) {
			foreach ($aPosts as $aPost) { 
	            echo $aPost['post']." ";
			} 
	    } else {
	        echo $oDelicious->LastErrorString();
	    }	
	}
	
	if($requette="GetAllBundeles"){
		if ($aPosts = $oDelicious->GetAllBundles()) {
			foreach ($aPosts as $aPost) { 
	           $tags=$aPost['tags']." ";
			   $name =$aPost['name']." ";
			} 
	    } else {
	        echo $oDelicious->LastErrorString();
		}
	echo $name.DELIM.$tags;
	}

?>