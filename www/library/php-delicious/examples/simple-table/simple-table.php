<?php
   require('../../php-delicious.inc.php');
   
   define('DELICIOUS_USER', 'andreagrp');
   define('DELICIOUS_PASS', 'book1211');
   
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Simple Table of All Posts</title>
	<style type="text/css">
	   body { font-family: Arial, sans-serif; }
	   h1 { font-size: 121%; }
	   table { font-size: 92%; }
	   th { background: #ccc; padding: 7px; text-align: left; }
	   td { border-bottom: 1px solid #ccc; padding: 7px; }
	</style>
</head>

<body>
   <h1>Simple Table of All Posts</h1>
	<?php 
		if ($aPosts = $oDelicious->GetAllPosts("master")) { 
			echo "<p>".count($aPosts)." posts in this account. Results cached for 10 seconds (by default).</p>";
   		 /*
		 echo "<br/>aPosts=<br/>";
		 print_r($aPosts);
		 echo "<br/>";
		 */

		    echo "<table>";
		    echo "<tr>";
		    echo "<th>Description</th>";
		    echo "<th>Notes</th>";
		    echo "<th>Last Updated</th>";
		    echo "</tr>";
			foreach ($aPosts as $aPost) { 
	            echo "<tr>";
	            echo "<td><a href='".$aPost['url']."'>".$aPost['desc']."</a></td>";
	            echo "<td>".$aPost['notes']."</td>";
	            echo "<td>".$aPost['updated']."</td>";
	            echo "</tr>";
	        } 
			echo "</table>";
	    } else {
	        echo $oDelicious->LastErrorString();
	    }
   ?>
</body>

</html>