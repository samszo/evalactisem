<?php
   require('Acti.php');
   require('SaveFlux.php');
   require('php-delicious/php-delicious.inc.php');
   require('../param/Constantes.php');
   require_once ("../param/ParamPage.php");

   //define('DELICIOUS_USER', "amelmaster");
   //define('DELICIOUS_PASS', "lema1983");
   define('DELICIOUS_USER', "luckysemiosisr");
   define('DELICIOUS_PASS', "Samszo0");

   
   $requette= $_GET["requette"];
   $tag=$_GET["tag"];
   $count=$_GET["count"];
   $url=$_GET["url"];
   $date=$_GET["date"];
  
   $Activite= new Acti();
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);
   $oSaveFlux= new SauvFlux(); 
   
   if($requette=="GetAllBundles" ){
   	
    $oSaveFlux->aGetAllTags($objSite,$oDelicious);
    $result=$oSaveFlux->aGetAllBundles($objSite,$oDelicious);
    echo $result;
    $codeActi='GetAB';
	$descActi='Recupperation de tous les bundles';
   }
   
  if($requette=="GetAllTags"){
  	
  	$oSaveFlux->aGetAllBundles($objSite,$oDelicious);
    echo $oSaveFlux->aGetAllTags($objSite,$oDelicious);
  	$codeActi='GetAT';
	$descActi='Recupperation de tous les tags';
  }
  
  if($requette=="GetAllPosts"){
  	if ($aPosts = $oDelicious->GetAllPosts()){
  		$result=$oSaveFlux->aGetPosts($aPosts);
  	    
  	 }else {
	        echo $oDelicious->LastErrorString();
	 }
     $codeActi='GetAP';
	 $descActi='Recupperation de tous les Posts';
	 
	 $Activite->AddActi($codeActi,$descActi);
	 echo$result;
  }
  
 
  if($requette=="GetPosts"){
  	
  	if ($aPosts = $oDelicious->GetPosts($tag,$url,$date)){
  	 	$result=$oSaveFlux->aGetPosts($aPosts);
	 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }

     $codeActi='GetP';
	 $descActi='Recupperation de  Posts';
  	 $Activite->AddActi($codeActi,$descActi);
	 echo $result;
}
  	
  
if($requette=="GetRecentPosts"){
  	if ($aPosts = $oDelicious->GetRecentPosts($tag,$count)){
  	 $result=$oSaveFlux->aGetPosts($aPosts);
	 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }

	     $codeActi='GetP';
		 $descActi='Recupperation de  Posts';
	  	 $Activite->AddActi($codeActi,$descActi);
		 echo $result;

	 
  }
  if($requette=="tagsFbundles"){
  	$result=$oSaveFlux->GraphTagBund($objSite);
  	echo $result;
  }
 

function Xpath($fonction){
	 $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='".$fonction."']";
	 return $Xpath; 
}



    

 ?>