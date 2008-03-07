<?php
   require('Acti.php');
   require('SaveFlux.php');
   require('php-delicious/php-delicious.inc.php');
   require('../param/Constantes.php');
   require_once ("../param/ParamPage.php");

   define('DELICIOUS_USER', "amelmaster");
   define('DELICIOUS_PASS', "lema1983");
   
   $requette= $_GET["requette"];
  
  
   $Activite= new Acti();
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);
   $oSaveFlux= new SauvFlux(); 
   
   if($requette==GetAllBundles ){
   	
    $oSaveFlux->aGetAllTags();
    echo $oSaveFlux->aGetAllBundles();
    $codeActi='GetAB';
	$descActi='Recupperation de tous les bundles';
   }
   
  if($requette==GetAllTags){
  	
  	$oSaveFlux->aGetAllBundles();
    echo $oSaveFlux->aGetAllTags();
  	$codeActi='GetAT';
	$descActi='Recupperation de tous les tags';
  }
  
  if($requette==GetAllPosts){
  	if ($aPosts = $oDelicious->GetAllPosts($aTag)){
  		$result=$oSaveFlux->aGetPosts($aPosts);
  	    
  	 }else {
	        echo $oDelicious->LastErrorString();
	 }
     $codeActi='GetAP';
	 $descActi='Recupperation de tous les Posts';
	 
	 $Activite->AddActi($codeActi,$descActi);
	 echo$result;
  }
  
 
  if($requette==GetPosts){
  	
  	if ($aPosts = $oDelicious->GetPosts()){
  	 	$result=$oSaveFlux->aGetPosts($aPosts);
	 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }

     $codeActi='GetP';
	 $descActi='Recupperation de  Posts';
  	 $Activite->AddActi($codeActi,$descActi);
	 echo $result;
}
  	
  
if($requette==GetRecentPosts){
  	if ($aPosts = $oDelicious->GetRecentPosts($aTag,$iCount)){
  	 $result=$oSaveFlux->aGetPosts($aPosts);
	 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }

     $codeActi='GetP';
	 $descActi='Recupperation de  Posts';
  	 $Activite->AddActi($codeActi,$descActi);
	 echo $result;

	 echo $tag.DELIM.$aDesc;
  }
  	
 

function Xpath($fonction){
	 $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='".$fonction."']";
	 return $Xpath; 
}
     echo $sTag.DELIM.$aDesc.DELIM.$aNote.DELIM.$aUdate;
    

 ?>