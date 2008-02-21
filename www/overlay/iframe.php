<?php
	require_once ("../param/ParamPage.php");

	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/menu/urlDesc[@nom='".$objSite->scope['UrlNom']."']";
	$Desc = $objSite->XmlParam->GetElements($Xpath);
	//print_r($Desc);

    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<overlay id="iframe"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

	<box id="<?php echo $objSite->scope["box"]; ?>" >
		<iframe src="<?php echo $Desc[0]["dst"]; ?>" flex="1" style="border:0px" id="BrowerGlobal"/>
	</box>


</overlay>