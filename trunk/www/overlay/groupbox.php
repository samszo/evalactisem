<?php
require_once ("../param/ParamPage.php");

$query=$_GET["requette"];
	if($query=="GetPosts"){
	$Xpath = "/XmlParams/XmlParam[@nom='ParamQuery']/Querys/Query[@fonction='GetPosts']/params/param";
	$Params=$objSite->XmlParam->GetElements($Xpath);
	//print_r($Params);
	}
	
	header('Content-type: application/vnd.mozilla.xul+xml');

?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<overlay id="ParamQuery"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
         <box id="box1">
	         <groupbox>
	         	<caption label="Parmatres"/>
	            <?php
				   foreach($Params as $Param){
				  	 echo"<label value='".$Param['nom']."'/>";
				  	 echo"<textbox id='".$Param['nom']."' value=''/>";
				   }
				 ?>
			</groupbox>
		</box>
</overlay>