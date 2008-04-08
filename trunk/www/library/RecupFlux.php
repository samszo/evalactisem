<?php
   require('Acti.php');
   require('SaveFlux.php');
   require('php-delicious/php-delicious.inc.php');
   require('../param/Constantes.php');
   require_once ("../param/ParamPage.php");
   session_start();
  
   $oDelicious=$_SESSION['Delicious'];
   
   $login=$_SESSION['loginSess'];
   $requette= $_GET["requette"];
   $requete_g=$_GET["req"];
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
    $result_F=$oSaveFlux->aGetAllBundles($objSite,$oDelicious,$iduti);
    Donneegraph($result_F,$AllTag,$requete_g);
    $codeActi='GetAB';
	$descActi='Recuperation de tous les bundles';
   }
   
  if($requette=="GetAllTags"){
    $oSaveFlux->aGetAllBundles($objSite,$oDelicious,$iduti);
    $oSaveFlux->aGetAllTags($objSite,$oDelicious,$iduti);
    $result_F="<nom ieml='n.u.-'><![CDATA[$AllTag[0]]]></nom><nombre ieml=\"t.u.-t.u.-'\"><![CDATA[$AllTag[1]]]></nombre>";
    Donneegraph($result_F,$AllTag,$requete_g,$iduti);
    
  	$codeActi='GetAT';
	$descActi='Recuperation de tous les tags';
  }
  
  if($requette=="GetAllPosts"){
  	if ($aPosts = $oDelicious->GetAllPosts()){
  		$result_F=$oSaveFlux->aGetPosts($aPosts);
	  	Donneegraph($result_F,$AllTag,$requete_g,$iduti);
  	 }else {
	        echo $oDelicious->LastErrorString();
	 }
     $codeActi='GetAP';
	 $descActi='Recuperation de tous les Posts';
	 
	 $Activite->AddActi($codeActi,$descActi);
	 
  }
  
 
  if($requette=="GetPosts"){
  	
  	if ($aPosts = $oDelicious->GetPosts($tag,$url,$date)){
  	 	$result_F=$oSaveFlux->aGetPosts($aPosts);
	  	Donneegraph($result_F,$AllTag,$requete_g,$iduti);
	 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }

     $codeActi='GetP';
	 $descActi='Recupperation de  Posts';
  	 $Activite->AddActi($codeActi,$descActi);
	
}
  	
  
if($requette=="GetRecentPosts"){
  	if ($aPosts = $oDelicious->GetRecentPosts($tag,$count)){
  	 $result_F=$oSaveFlux->aGetPosts($aPosts);
	  Donneegraph($result_F,$AllTag,$requete_g,$iduti);
	 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }

	     $codeActi='GetP';
		 $descActi='Recupperation de  Posts';
	  	 $Activite->AddActi($codeActi,$descActi);
	}
  
 

	
function Xpath($fonction){
	 $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='".$fonction."']";
	 return $Xpath; 
}
function Donneegraph($result_F,$AllTag,$requete_g,$iduti){
	global $oSaveFlux;
	global $objSite;
	if($requete_g=="tagsFbundles"){
    	$result_G=$oSaveFlux->GraphTagBund($objSite,$iduti);
    	echo $result="<marque ieml='t.u.-'>".$result_F.$result_G."</marque>";
    }else{
    	$result_G="<noms ieml=\"n.u.-'\"><![CDATA[$AllTag[0]]]></noms><donnees ieml=\"n.u.-'\"><![CDATA[$AllTag[1]]]></donnees>";
    	echo $result="<marque ieml='t.u.-'>".$result_F.$result_G."</marque>";
    }
}


    

 ?>