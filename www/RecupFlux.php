<?php
   
   require('library/php-delicious/php-delicious.inc.php');
   require('param/Constantes.php');
   require_once ("param/ParamPage.php");
   define('DELICIOUS_USER', "amelmaster");
   define('DELICIOUS_PASS', "lema1983");
   
   $requette= $_GET["requette"];
  
  
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);
	
   if($requette== $_GET["requette"] ){
	$descFlux_Band="bundels";
	$niveauFlux_Band=1;
	$parentsFlux_Band="";
	$descFlux="tag";
	$niveauFlux=1;
	$Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='Ieml_Onto_Flux']";
	$Q = $objSite->XmlParam->GetElements($Xpath);	
   	
	$values = str_replace("-descFlux-", $descFlux_Band, $Q[0]->values);
	$values = str_replace("-niveauFlux-", $niveauFlux_Band, $values);
	$values = str_replace("-parentsFlux-", $parentsFlux_Band, $values);
	
	if ($aPosts = $oDelicious->GetAllBundles()) {
			foreach ($aPosts as $aPost) { 
	           $Q = $objSite->XmlParam->GetElements($Xpath);
			   
	           $tags=$aPost['tags']." ";
			   $name =$aPost['name']." ";
			   
			   $values = str_replace("-descFlux-", $descFlux_Band, $Q[0]->values);
			   $values = str_replace("-niveauFlux-", $niveauFlux_Band, $values);
	           $values = str_replace("-parentsFlux-", $parentsFlux_Band, $values);
			   $values = str_replace("-codeFlux-",$aPost['name'],$values );

			   $sql = $Q[0]->insert.$values;
			  
          
			//instertion de bundles
			$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$req = $db->query($sql);
			
			
			$enfant=explode(" ",$tags);
			
			for($i=0;$i<sizeof($enfant)-1;$i++){
				
				$Q = $objSite->XmlParam->GetElements($Xpath);
				
				 
				$value = str_replace("-descFlux-",$descFlux,$Q[0]->values);
				$value = str_replace("-niveauFlux-",$niveauFlux,$value );
				$value = str_replace("-parentsFlux-", $name.";",$value );
				$value = str_replace("-codeFlux-",$enfant[$i],$value);
				
				$sqltag = $Q[0]->insert.$value; 
				
				echo"enfant=".($sqltag)."</br>";
				$req = $db->query($sqltag);
			
			}
			} 
			
	        	$db->close();
	        } else {
	        echo $oDelicious->LastErrorString();
		}
	echo $name.DELIM.$tags;
   }else{
   	echo "erreur </br>";
   echo "requtte=".$requette;
   }

?>