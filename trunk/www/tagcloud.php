<?php
	require('param/ParamPage.php');

	$oTC = new TagCloud();
	//$tg->SauveBookmarkNetwork("luckysemiosis","Samszo0")
  	
	header("Content-type: image/svg+xml");
	if($TC=="post")
		$oTC->GetSvgPost($login,$ShowAll,$TempsVide,$DateDeb,$DateFin,$NbDeb,$NbFin);
	if($TC=="tag")
		$oTC->GetSvgTag($login,$ShowAll,$NbDeb,$NbFin);
	
    
    
   ?>