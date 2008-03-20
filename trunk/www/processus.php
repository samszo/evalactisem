<?php  
    require_once ("param/ParamPage.php");
    
	$sem = New Sem($objSite
		, PathRoot."/param/EvalActiSem.xml"
		, $objSite->scope['So']
		,""
		,$objSite->scope['Trace']
		);
	$menu = $sem->GetChoixNavig($objSite->scope['So']); 		
    print_r("menu=".$objSite->scope['site']);
	
   header('Content-type: application/vnd.mozilla.xul+xml');

?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<window id="ieml-global" title="IEML-10eF v0.1 - information economy meta language - Dixième Famille" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<script language="JavaScript" type="application/x-javascript" src="js/interface.js"/>
	<script language="JavaScript" type="application/x-javascript" src="js/processus.js"/>
	<script language="JavaScript" type="application/x-javascript" src="js/ajax.js"/>
	<script language="JavaScript" type="application/x-javascript" src="js/histogrammes.js"/>


				<hbox>
	<vbox >
		<hbox >
			<groupbox flex="1">
				<caption label="Explorer les processus"/>
				<menubar id="sample-menubar">
				<?php echo $menu["liste"]; ?>		
				</menubar>
			</groupbox>
		</hbox>
		<hbox id="box2" >
			<vbox >

			<groupbox flex="1">
			<caption label="Processus en cours" />
				<label id="proc-id" hidden="true"/>
				<hbox>
					<label value="code :"/>
					<textbox id="proc-code" style="width:500px;" value=""/>
				</hbox>
				<hbox>
					<label value="descriptif : "/>
					<textbox id="proc-desc" style="width:500px;" value=""/>
				</hbox>
				<hbox>
					<label value="traduction : "/>
					<textbox id="proc-trad" style="width:500px;" value=""/>
				</hbox>
				<hbox>
					<label value="trace : "/>
					<textbox id="proc-trace" multiline="true" rows="12" style="width:500px;" value=""/>
				</hbox>
			</groupbox>
					<groupbox flex="1">
					<caption label="Fonctions" />
						<hbox>
							<button label="Modifier" oncommand="SetProc();"/>				
							<button label="Parser"  oncommand="Parse('proc-code','proc-trace')" />
							<button label="StatSvg"  oncommand="GetGraph('proc-code','proc-svg')" />
						</hbox>
					</groupbox>
				<hbox>
					<label id="proc-message" />	
				</hbox>
			</vbox>
		</hbox>
	</vbox>
	<vbox flex="1" >
		<groupbox flex="1">
		<caption label="SVG Zone" />
			<groupbox orient="vertical" hidden="true">
				<caption label="Données"/>
				<label value="Données (séparées par des ;)"/>
				<textbox id="donnees" value=""/>
				<label value="Nom des données (séparés par des ;)"/>
				<textbox id="noms" value=""/>
			</groupbox>
			<iframe src="http://localhost/luckysemiosis/ChaoticumPapillonae/CreaPapiDyna.php" flex="1" style="border:0px" id="FrameSvg"/>
		</groupbox>
	</vbox>
</hbox>

	<script>
<?php echo $menu["js"]; ?>		
</script>

</window>

