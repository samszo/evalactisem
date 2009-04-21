<?php
session_start();
	require_once ("Constantes.php");
	
	if(isset($_SESSION['loginSess'])){
		$login = $_SESSION['loginSess'];
		$mdp = $_SESSION['mdpSess'];
	}else{
		/*
		$login = "luckysemiosis";
		$mdp = "Samszo0";
		*/
		$login = "samszo";
		$mdp = "Lucky71";
		$_SESSION['loginSess']=$login;
		$_SESSION['mdpSess']=$mdp;
	}
	
	$oDelicious = new PhpDelicious($_SESSION['loginSess'],$_SESSION['mdpSess'],CACHETIME);
	$_SESSION['Delicious']= $oDelicious;
		

	if(!isset($_SESSION['lang'])){
		$_SESSION['lang']='fr';
	}
	
	
	// v�rification du site en cours
	if(isset($_GET['site'])){
		$site = $_GET['site'];
	}else{
		$site = DEFSITE;
	}
	
	if(isset($_GET['type']))
		$type = $_GET['type'];
	else
		$type = 'ieml';
			
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
			,"ParamNom" => $ParamNom
			,"box" => $box
			,"UrlNom" => $UrlNom
			);	
	
	$objSite = new Site($SITES, $site, $scope, false);
	$objXul= new Xul($objSite);
	$objUti = new Uti($objSite,$_SESSION['loginSess']);
	$objUtiTradAuto = new Uti($objSite,false,$objSite->infos["UTI_TRAD_AUTO"]);
	
	$_SESSION['iduti']=$objUti->id;
	

//function pour le cache
	function cParse($code){
		global $objSite;
		$s = new Sem($objSite,$objSite->XmlParam,"");
		return $s->parse($code);
	}
	
	
	
?>
