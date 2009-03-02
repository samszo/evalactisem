<?php
session_start();
	require_once ("Constantes.php");
	
	$oDelicious = new PhpDelicious($_SESSION['loginSess'],$_SESSION['mdpSess'],10);
	$_SESSION['Delicious']= $oDelicious;
	

	// vérification du site en cours
	if(isset($_GET['site'])){
		$site = $_GET['site'];
	}else{
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
	
	if(isset($_GET['TempsVide']))
		$TempsVide = $_GET['TempsVide'];
	else
		$TempsVide = false;
	
	if(isset($_GET['ShowAll']))
		$ShowAll = $_GET['ShowAll'];
	else
		$ShowAll = false;
	
	if(isset($_GET['DateDeb']))
		$DateDeb = $_GET['DateDeb'];
	else
		$DateDeb = false;
		
	if(isset($_GET['DateFin']))
		$DateFin = $_GET['DateFin'];
	else
		$DateFin = false;

	if(isset($_GET['NbDeb']))
		$NbDeb = $_GET['NbDeb'];
	else
		$NbDeb = 0;
		
	if(isset($_GET['NbFin']))
		$NbFin = $_GET['NbFin'];
	else
		$NbFin = 1000000000000;
	
	if(isset($_GET['TC']))
		$TC = $_GET['TC'];
	else
		$TC = "posts";
		
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
	
	$_SESSION['iduti']=$objSite->utilisateur($_SESSION['loginSess']);


?>
