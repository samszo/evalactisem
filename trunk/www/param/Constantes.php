<?php

  //
  // Fichier contenant les definitions de constantes
  define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/evalactisem");
  
	// *** chemin de toutes les bases et les spip en service ***
  define("TT_CLASS_BASE", PathRoot."/library/");
	// Include the class files.
	require_once(TT_CLASS_BASE."AllClass.php");

  define ("DEFSITE", "local");

  $DB_OPTIONS = array (
		'ERROR_DISPLAY' => true
		);
  
  define ("MaxMarker", 300);
  define ("DELIM",'*');
  define ("jsPathRoot",PathRoot."/js/");

  define ("gmKey", "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ");

  define ("XML_CreaRdf",PathRoot."/param/CreaRdf.xml");
  define('EOL', "\r\n");

$Site = array(
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "ieml_evalactisem",
	"NOM" => "EvalActiSem",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => gmKey
	); 
$SiteThyp = array(
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "thyp2006", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "evalactisem",
	"NOM" => "EvalActiSem",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => gmKey
	); 
	
  $SITES = array(
	"local" => $Site,
	"thyp" => $SiteThyp
  );

?>