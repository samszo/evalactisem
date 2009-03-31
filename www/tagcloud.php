<?php
	require('param/ParamPage.php');

	$oTC = new TagCloud($oDelicious);
	//$oTC->SauveBookmarkNetwork("luckysemiosis","Samszo0");
  	
	header("Content-type: image/svg+xml");
	if($TC=="posts")
		$oTC->GetSvgPost($_GET['login'],$ShowAll,$TempsVide,$DateDeb,$DateFin,$NbDeb,$NbFin);
	if($TC=="tags")
		$oTC->GetSvgTag($_GET['login'],$ShowAll,$NbDeb,$NbFin);
	
?>