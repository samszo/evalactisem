<?php
   require('../php-delicious/php-delicious.inc.php');
   require_once ("../../param/ParamPage.php");
         
   $requette= $_GET["requette"];
   $requete_g=$_GET["req"];
  // $requette= $_GET["requette"];
   //$requete_g=$_GET["req"];
   $tag=$_GET["tag"];
   $count=$_GET["count"];
   $url=$_GET["url"];
   $date=$_GET["date"];
  
   $Activite= new Acti();
   $oSaveFlux= new SauvFlux(); 
   $iduti = $_SESSION['iduti'];
   
   $oSaveFlux->aGetAllTags($objSite,$oDelicious,$iduti);
   $Activite->AddActi('RAT',$iduti);
   
   /*
   if($requette=="GetAllBundles" ){
    $oSaveFlux->aGetAllTags($objSite,$oDelicious,$iduti);
    echo $result_F=$oSaveFlux->aGetAllBundles($objSite,$oDelicious,$iduti);
    $Activite->AddActi('RB',$iduti);
   }
   
  if($requette=="GetAllTags"){
  	
    $result_G=$oSaveFlux->aGetAllBundles($objSite,$oDelicious,$iduti);
   $oSaveFlux->aGetAllTags($objSite,$oDelicious,$iduti);
    $flux="<marque ieml='t.u.-'><tags> ".$AllTag.$result_G."</tags></marque>";
    SaveXmlFlux($flux);
    
    $Activite->AddActi('RAT',$iduti);
  	echo  $flux;
  }
  
  if($requette=="GetAllPosts"){
  	if ($aPosts = $oDelicious->GetAllPosts('',true)){
  		
  		echo $result_F=$oSaveFlux->aGetPosts($aPosts,'xml');
	  	SaveXmlFlux($result_F,"Posts");
  	 }else {
	        echo $oDelicious->LastErrorString();
	 }
    $Activite->AddActi("RAP",$iduti);
	  
  }
  
 
  if($requette=="GetPosts"){
  	
  	if ($aPosts = $oDelicious->GetPosts($tag,$url,$date)){
  	 	
  		echo $result_F=$oSaveFlux->aGetPosts($aPosts);
	  	
	 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }

  	 $Activite->AddActi("RP",$iduti);
	
}
  	
  
if($requette=="GetRecentPosts"){
  	if ($aPosts = $oDelicious->GetRecentPosts($tag,$count,true)){
  	 
  		echo $result_F=$oSaveFlux->aGetPosts($aPosts,'xml');
	 
	 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }

	    
	  	 $Activite->AddActi("RRP",$iduti);
	}

/*
function Donneegraph($result_F,$AllTag,$requete_g,$iduti){
	global $oSaveFlux;
	global $objSite;
	
	
	if($requete_g=="tagsFbundles"){
    	$result_G=$oSaveFlux->GraphTagBund($objSite,$iduti);
    	echo $resultB="<marque ieml='t.u.-'>".$result_F.$result_G."</marque>";
    	
    }else{
    	$result_G="<noms ieml=\"n.u.-'\"><![CDATA[$AllTag[0]]]></noms><donnees ieml=\"n.u.-'\"><![CDATA[$AllTag[1]]]></donnees>";
    	echo $resultT="<marque ieml='t.u.-'>".$result_F.$result_G."</marque>";
    	
    }
}*/

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
    
function Xpath($fonction){
	 $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='".$fonction."']";
	 return $Xpath; 
}

 ?>