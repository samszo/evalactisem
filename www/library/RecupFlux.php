<?php
   require('php-delicious/php-delicious.inc.php');
   require('../param/Constantes.php');
   require_once ("../param/ParamPage.php");
   session_start();
  
   $oDelicious=$_SESSION['Delicious'];
   
   $requette= $_GET["requette"];
   $requete_g=$_GET["req"];
   
   $login=$_SESSION['loginSess'];
  // $requette= $_GET["requette"];
   //$requete_g=$_GET["req"];
   $tag=$_GET["tag"];
   $count=$_GET["count"];
   $url=$_GET["url"];
   $date=$_GET["date"];
  
   $Activite= new Acti();
   $oSaveFlux= new SauvFlux(); 

   $iduti=$oSaveFlux->utilisateur($objSite,$login);
   $_SESSION['iduti']=$iduti;
   $AllTag=explode("*",$oSaveFlux->aGetAllTags($objSite,$oDelicious,$iduti))  ;
  
   
   
   if($requette=="GetAllBundles" ){
    $oSaveFlux->aGetAllTags($objSite,$oDelicious,$iduti);
    echo $result_F=$oSaveFlux->aGetAllBundles($objSite,$oDelicious,$iduti);
    $Activite->AddActi('RB',$iduti);
   }
   
  if($requette=="GetAllTags"){
    $oSaveFlux->aGetAllBundles($objSite,$oDelicious,$iduti);
    $oSaveFlux->aGetAllTags($objSite,$oDelicious,$iduti);
    $result_F="<Tags> $AllTag[0] </Tags><Count> $AllTag[1] </Count>";
    Donneegraph($result_F,$AllTag,$requete_g,$iduti);
    $Activite->AddActi('RAT',$iduti);
  	
  }
  
  if($requette=="GetAllPosts"){
  	if ($aPosts = $oDelicious->GetAllPosts('',true)){
  		echo $result_F=$oSaveFlux->aGetPosts($aPosts,'xml');
	  	//Donneegraph($result_F,$AllTag,$requete_g,$iduti);
  	 }else {
	        echo $oDelicious->LastErrorString();
	 }
    $Activite->AddActi("RAP",$iduti);
	 
  }
  
 
  if($requette=="GetPosts"){
  	
  	if ($aPosts = $oDelicious->GetPosts($tag,$url,$date,true)){
  	 	
  		echo $result_F=$oSaveFlux->aGetPosts($aPosts,'xml');
	  	//Donneegraph($result_F,$AllTag,$requete_g,$iduti);
	 
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
  
 

	
function Xpath($fonction){
	 $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='".$fonction."']";
	 return $Xpath; 
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

function Donneegraph($result_F,$AllTag,$requete_g,$iduti){
	global $oSaveFlux;
	global $objSite;
	
    	$result_G=$oSaveFlux->GraphTagBund($objSite,$iduti);
    	
    	echo $result_G="<marque ieml='t.u.-'> <tags> $AllTag[0] </tags><count> $AllTag[1] </count> ".$result_G." </marque>";
		
    	//Creation de fichier loginFlux.xml 
    	
    	$name_file=XmlFlux;
			if(file_exists(Flux_PATH."/".$name_file)){
				unlink(Flux_PATH."/".$name_file);
				
			}
		
    	$fichier = fopen(Flux_PATH."/".$name_file,"w");
	    fwrite($fichier,$result_G);
	    fclose($fichier);
    	
   
}



    

 ?>