<?php
	define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/evalactisem");
	// *** Define the path to the SVG class dir. ***
	define("SVG_CLASS_BASE", PathRoot."/library/svg/");

	require_once ("library/php/TagCloud.php");
	require_once ("library/svg/Svg.php");

	$tg = new TagCloud();
    $tg->GetSvg();

   ?>