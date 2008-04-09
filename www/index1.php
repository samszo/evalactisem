<?php
header('Content-type: application/vnd.mozilla.xul+xml');
echo "<?xml version='1.0' encoding='ISO-8859-1' ?>";
echo "<?xul-overlay href='overlay/menubar.php?ParamNom=MenuUrl'?>";
?>
<window id="ieml-global" title="IEML-Flux v0.1 - information economy meta language - Dixième Famille" orient="horizontal" left="0" top="0" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<script language="JavaScript" type="application/x-javascript" src="js/Interface.js"/>
	<box>
		 <vbox id="menubar" ></vbox>
		 <vbox id="singlebox" style="height:600px;width:1200px;"/>
	
	</box>
	
		
	
</window>
