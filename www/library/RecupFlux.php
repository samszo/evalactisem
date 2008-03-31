<?php
   require('Acti.php');
   require('SaveFlux.php');
   require('php-delicious/php-delicious.inc.php');
   require('../param/Constantes.php');
   require_once ("../param/ParamPage.php");

   define('DELICIOUS_USER', "amelmaster");
   define('DELICIOUS_PASS', "lema1983");
   //define('DELICIOUS_USER', "luckysemiosisr");
   //define('DELICIOUS_PASS', "Samszo0");

   
   $requette= $_GET["requette"];
   $requete_g=$_GET["req"];
   $tag=$_GET["tag"];
   $count=$_GET["count"];
   $url=$_GET["url"];
   $date=$_GET["date"];
  
   $Activite= new Acti();
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);
    $oSaveFlux= new SauvFlux(); 
   
   $AllTag=explode("*",$oSaveFlux->aGetAllTags($objSite,$oDelicious))  ;
  
   
   if($requette=="GetAllBundles" ){
    $oSaveFlux->aGetAllTags($objSite,$oDelicious);
    $result_F=$oSaveFlux->aGetAllBundles($objSite,$oDelicious);
    Donneegraph($result_F,$AllTag,$requete_g);
    $codeActi='GetAB';
	$descActi='Recupperation de tous les bundles';
   }
   
  if($requette=="GetAllTags"){
 
    $result_F="<nom ieml='n.u.-'><![CDATA[$AllTag[0]]]></nom><nombre ieml=\"t.u.-t.u.-'\"><![CDATA[$AllTag[1]]]></nombre>";
    Donneegraph($result_F,$AllTag,$requete_g);
    
  	$codeActi='GetAT';
	$descActi='Recupperation de tous les tags';
  }
  
  if($requette=="GetAllPosts"){
  	if ($aPosts = $oDelicious->GetAllPosts()){
  		$result_F=$oSaveFlux->aGetPosts($aPosts);
	  	Donneegraph($result_F,$AllTag,$requete_g);
  	 }else {
	        echo $oDelicious->LastErrorString();
	 }
     $codeActi='GetAP';
	 $descActi='Recupperation de tous les Posts';
	 
	 $Activite->AddActi($codeActi,$descActi);
	 
  }
  
 
  if($requette=="GetPosts"){
  	
  	if ($aPosts = $oDelicious->GetPosts($tag,$url,$date)){
  	 	$result_F=$oSaveFlux->aGetPosts($aPosts);
	  	Donneegraph($result_F,$AllTag,$requete_g);
	 
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
	  Donneegraph($result_F,$AllTag,$requete_g);
	 
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
function Donneegraph($result_F,$AllTag,$requete_g){
	global $oSaveFlux;
	global $objSite;
	if($requete_g=="tagsFbundles"){
    	$result_G=$oSaveFlux->GraphTagBund($objSite);
    	echo $result="<marque ieml='t.u.-'>".$result_F.$result_G."</marque>";
    }else{
    	$result_G="<noms ieml=\"n.u.-'\"><![CDATA[$AllTag[0]]]></noms><donnees ieml=\"n.u.-'\"><![CDATA[$AllTag[1]]]></donnees>";
    	echo $result="<marque ieml='t.u.-'>".$result_F.$result_G."</marque>";
    }
}


    

 ?>