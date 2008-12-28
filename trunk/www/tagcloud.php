<?php
	/*
 	define ("PathRoot","C:/wamp/www/evalactisem");
		// *** Define the path to the SVG class dir. ***
	define("SVG_CLASS_BASE", PathRoot."/library/svg/");
   	define ("jsPathRoot",PathRoot."/library/js/");
	
	require_once ("library/php/TagCloud.php");
	require_once ("library/svg/Svg.php");
	require_once ('library/delicious/library/php-delicious.inc.php');
	*/
	require('param/ParamPage.php');

	$tg = new TagCloud();
	//$tg->SauveBookmarkNetwork("luckysemiosis","Samszo0")
  	
	header("Content-type: image/svg+xml");
	$tg->GetSvg($login);

    
    
   ?>