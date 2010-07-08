<?php
  //
  // Fichier contenant les definitions de constantes
  //define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/evalactisem");
  //pour le d�bubbage
  define ("PathRoot","C:/wamp/www/evalactisem");

  // *** chemin de toutes les bases et les spip en service ***
  define("TT_CLASS_BASE", PathRoot."/library/php/");
	// *** Define the path to the SVG class dir. ***
	define("SVG_CLASS_BASE", PathRoot."/library/svg/");
  // Include the class files.
  require_once(TT_CLASS_BASE."AllClass.php");

	define ("TRACE", false);
	define ("DEFSITE", "local");
  //define ("DEFSITE", "mundi");

  //define ("CACHETIME", 100000000);
  define ("CACHETIME", 86400); //une journée
  define ("FORCE_CALCUL", true); //pour forcer les calculs et la mise à jour
  
  // folder to store cache files
   define('CACHE_PATH', PathRoot.'/tmp/');   
  
  $dbOptions = array (
		'ERROR_DISPLAY' => true
		);
  
  define ("MaxMarker", 300);
  define ("DELIM",'*');
  define ("DELIM_P",';');
  define ("DELIM_FLUX","array_delim");
  define ("jsPathRoot",PathRoot."/library/js/");
  
  define ("gmKey", "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ");

  define('EOL', "\r\n");
  define("XmlFlux",$_SESSION['loginSess']."_Flux");
  define('XmlGraphIeml' ,$_SESSION['loginSess']."_GraphIeml");
  define('PATH_FILE_FLUX','../tmpFlux/');
  define('PATH_STAR_PARSER','http://starparser.ieml.org/cgi-bin/star2xml.cgi?iemlExpression=');
  
  
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
	"UTI_TRAD_AUTO" => 1,
  	"gmKey" => gmKey,
  	"XML_Param"=>PathRoot."/param/ParamXul.xml", 
  	"LiveMetalDico"=>PathRoot."/param/LiveMetalDico.xml", 
  	"PATH_LiveMetal"=>"http://evalactisem.ieml.org" 
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
		"UTI_TRAD_AUTO" => 1,
		"gmKey" => gmKey,
	  	"XML_Param"=>PathRoot."/param/ParamXul.xml", 
	  	"LiveMetalDico"=>PathRoot."/param/LiveMetalDico.xml", 
	  	"PATH_LiveMetal"=>"http://evalactisem.ieml.org" 
	); 
	
	
  $SITES = array(
	"local" => $Site,
  	"mundi" => $SiteMundi
  );

  define ("PathWeb",$SITES[DEFSITE]["PATH_WEB"]);
  define ("jsPathWeb",PathWeb."library/js/");
  define ("ajaxPathWeb",PathWeb."library/php/ExeAjax.php");
  
  
?>