<?php
	require('param/ParamPage.php');

	$oTC = new TagCloud($oDelicious);
	//$oTC->SauveBookmarkNetwork("luckysemiosis","Samszo0");
  	
	header("Content-type: image/svg+xml");
	if($TC=="posts")
		$oTC->GetSvgPost($login,$ShowAll,$TempsVide,$DateDeb,$DateFin,$NbDeb,$NbFin);
	if($TC=="tags")
		$oTC->GetSvgTag($login,$ShowAll,$NbDeb,$NbFin);
	
    
    
   ?>