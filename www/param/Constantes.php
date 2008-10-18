<?php
  //
  // Fichier contenant les definitions de constantes
  //define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/evalactisem");
  //pour le débubbage
  define ("PathRoot","C:/wamp/www/evalactisem");

  // *** chemin de toutes les bases et les spip en service ***
  define("TT_CLASS_BASE", PathRoot."/library/php/");
	// *** Define the path to the SVG class dir. ***
	define("SVG_CLASS_BASE", PathRoot."/library/svg/");
  // Include the class files.
  require_once(TT_CLASS_BASE."AllClass.php");
  define ("PointV", ';');
  define ("Diaz", '#');
  define ("Virgule", ',');
  define ("Etoil", '*');
  define ("TRACE", false);
  define ("DEFSITE", "local");

  $dbOptions = array (
		'ERROR_DISPLAY' => true
		);
  
  define ("MaxMarker", 300);
  define ("DELIM",'*');
  define ("DELIM_P",';');
  define ("DELIM_FLUX","array_delim");
  define ("jsPathRoot",PathRoot."/library/js/");
  
  define ("gmKey", "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ");

  define ("XML_CreaRdf",PathRoot."/param/ParamXul.xml");
  define('EOL', "\r\n");
  define("XmlFlux",$_SESSION['loginSess']."_Flux");
  define('XmlGraphIeml' ,$_SESSION['loginSess']."_GraphIeml");
  define('Flux_PATH', '../tmpFlux/');
  define('PATH_FILE_FLUX','../tmpFlux/');
  define('LOGIN_IEML',"ieml");
  define('MDP_IEML','Paragraphe08');
  $Site = array(
	"PATH_WEB" => "http://localhost/evalactisem/", 
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
	"UTI_TRAD_AUTO" => 1,
  	"gmKey" => gmKey
	); 
$SiteThyp = array(
	"PATH_WEB" => "http://localhost/evalactisem/", 
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
	"UTI_TRAD_AUTO" => 4,
	"gmKey" => gmKey
	); 
$SiteMundi = array(
	"PATH_WEB" => "http://www.mundilogiweb.com/evalactisem/", 
	"SQL_LOGIN" => "mundilogieml", 
	"SQL_PWD" => "uLkm3WuW", 
	"SQL_HOST" => "mysql5-12",
	"SQL_DB" => "mundilogieml",
	"NOM" => "EvalActiSem",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"UTI_TRAD_AUTO" => 4,
	"gmKey" => gmKey
	); 
	
	
  $SITES = array(
	"local" => $Site,
	"thyp" => $SiteThyp,
  	"mundi" => $SiteMundi
  );

  define ("PathWeb",$SITES[DEFSITE]["PATH_WEB"]);
  define ("jsPathWeb",PathWeb."library/js/");
  define ("ajaxPathWeb",PathWeb."library/php/ExeAjax.php");
  
  
?>