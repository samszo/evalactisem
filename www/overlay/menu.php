<?php
	require_once ("../param/ParamPage.php");

	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/menu";
	$Menus = $objSite->XmlParam->GetElements($Xpath);
	//print_r($Desc);

    header('Content-type: application/vnd.mozilla.xul+xml');
	echo '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
?>
<overlay id="menu"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

	<box id='<?php echo $objSite->scope['box']; ?>' >

	<menu id="help-menu" label="Niveau 1">

		<menupopup id="help-popup">
	    <menuitem id="help-contents" label="RR" onmouseover="document.getElementById('m1_1').open = true;">

			<menu id="m1_1" label="Niveau 1.1">
				<menupopup>
					<menuitem label="Printer"/>
					<menuitem label="Mouse"/>
					<menuitem label="Keyboard"/>
				</menupopup>
			</menu>
		
		</menuitem>
	    <menuitem id="help-index" label="SS" />
	    <menuitem id="help-about" label="TT"/>
	  </menupopup>
	</menu>

	</box>
</overlay>