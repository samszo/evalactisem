<?php
   require('../php-delicious/php-delicious.inc.php');
   require_once ("../../param/ParamPage.php");
  
   $Activite= new Acti();
   $oSaveFlux= new SauvFlux(); 
   $iduti = $_SESSION['iduti'];
   
   $oSaveFlux->aGetAllTags($objSite,$oDelicious,$iduti);
   $Activite->AddActi('RAT',$iduti);
   

// creation de fichier XML du resultat

function SaveXmlFlux($Xml,$file=""){
	
//Creation de fichier loginFlux.xml 
	$name_file=md5(XmlFlux.$file).".xml";
	if(file_exists(Flux_PATH.$name_file)){
		unlink(Flux_PATH.$name_file);
	}
		
	$fichier = fopen(Flux_PATH.$name_file,"w");
	fwrite($fichier,$Xml);
	fclose($fichier);
}    

 ?>