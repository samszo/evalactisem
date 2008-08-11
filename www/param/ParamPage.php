<?php
session_start();
//set_time_limit(3000);

	if($_SESSION['loginSess']){
		$_SESSION['loginSess']="samszo";
		$_SESSION['iduti']="2";
	}

	require_once ("Constantes.php");
	
	if($_SESSION['Delicious']){
	   $oDelicious=$_SESSION['Delicious'];
   	}else{
   		//pour le debuggage
       	$oDelicious = new PhpDelicious("samszo", "Lucky71");
		$_SESSION['Delicious']=$oDelicious;
   }




// vérification du site en cours
if(isset($_GET['site'])){
	$site = $_GET['site'];
}else{
	if(!session_is_registered("Site"))
		$site = DEFSITE;
}

if(isset($_GET['type']))
	$type = $_GET['type'];
else
	$type = 'ieml';
	
if(isset($_GET['FicXml']))
	$FicXml = $_GET['FicXml'];
else
	$FicXml = XML_CreaRdf;

if(isset($_GET['ParamNom']))
	$ParamNom = $_GET['ParamNom'];
else
	$ParamNom = "GetOntoTree";

if(isset($_GET['box']))
	$box = $_GET['box'];
else
	$box = "singlebox";

if(isset($_GET['UrlNom']))
	$UrlNom = $_GET['UrlNom'];
else
	$UrlNom = "Traduction";
	

$scope = array(
		"site" => $site
		,"type" => $type
		,"FicXml" => $FicXml
		,"ParamNom" => $ParamNom
		,"box" => $box
		,"UrlNom" => $UrlNom
		);	

$objSite = new Site($SITES, $site, $scope, false);
$objXul= new Xul($objSite);

?>