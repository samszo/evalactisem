<?php
	require_once ("../param/ParamPage.php");

	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/menu";
	$Menus = $objSite->XmlParam->GetElements($Xpath);
	//print_r($Desc);

	$sem = New Sem($objSite
		, PathRoot."/param/EvalActiSem.xml"
		, $objSite->scope['So']
		,""
		,""
		,$objSite->scope['Trace']
		);
	$menu = $sem->GetChoixNavig($objSite->scope['So']); 		

    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="UTF-8" ?>
<overlay id="menu"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

<script type="text/javascript" src="../js/processus.js" />

	<toolbox flex="1"  id='<?php echo $objSite->scope['box']; ?>' >
	
		<menubar id="sample-menubar">
		<?php echo $menu["liste"]; ?>		
		</menubar>
		
	</toolbox>
<script>
<?php echo $menu["js"]; ?>		
</script>

</overlay>