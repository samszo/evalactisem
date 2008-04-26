<?php
    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<overlay id="toverlay"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

		<popupset id="popupset">
		<tooltip id="tipBadValue" onclick="this.hidePopup( );">
			<vbox>
				<label value="Valeur incorrecte !!"/>
			</vbox>
		</tooltip>
		<popup id="clipmenu" onpopupshowing="javascript:;">
			<menuitem label="parser" oncommand="Parser();"/>
			
		</popup>
	</popupset>

</overlay>