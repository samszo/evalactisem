<?php
require_once ("Constantes.php");

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
	
if(isset($_GET['So']))
	$So = $_GET['So'];
else
	$So = "l.o.-t.o.-we.b.-'";

if(isset($_GET['Trace']))
	$Trace = $_GET['Trace'];
else
	$Trace = false;

$scope = array(
		"site" => $site
		,"type" => $type
		,"FicXml" => $FicXml
		,"ParamNom" => $ParamNom
		,"box" => $box
		,"UrlNom" => $UrlNom
		,"So" => $So
		,"Trace" => $Trace
		);	
//print_r($scope);

$objSite = new Site($SITES, $site, $scope, false);

?>