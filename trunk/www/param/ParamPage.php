<?php
session_start();
set_time_limit(3000);

	if(!$_SESSION['loginSess']){
		$_SESSION['loginSess']="samszo";
		$_SESSION['mdpSess']="Lucky71";
		$_SESSION['iduti']="2";
		$_SESSION['loginSess']="plevy4";
		$_SESSION['mdpSess']="1plotin";
		$_SESSION['iduti']="3";
		//$oDelicious = new PhpDelicious("", "1plotin");
	}

	require_once ("Constantes.php");
	
	if(TRACE)
		echo "ParamPage:_SESSION['loginSess']=".$_SESSION['loginSess']."<br/>";
		
   	$oDelicious = new PhpDelicious($_SESSION['loginSess'], $_SESSION['mdpSess']);
   	$_SESSION['Delicious']=$oDelicious;
	if(TRACE)
		echo "ParamPage:Debug:oDelicious:login=".$oDelicious->sUsername." mdp=".$oDelicious->sPassword."<br/>";
   
//Vérification du mode connecté
if(isset($_GET['NoConnect'])){
	//$oDelicious->iCacheTime = 1000000000;
}
   


// vérification du site en cours
if(isset($_GET['site'])){
	$site = $_GET['site'];
}else{
	if(!session_is_registered("site"))
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