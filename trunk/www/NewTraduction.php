<?php	
require_once ("param/ParamPage.php");   
$FluxM=$_GET["FluxM"];
	$MultiTrad=$_GET["MultiTrad"];
	$FluxS=$_GET["FluxS"];
	$SignlTrad=$_GET["SignlTrad"];
	$FluxN=$_GET["FluxN"];
	$DescpM=$_GET["descpM"];
	$DescpS=$_GET["descpS"];
	$mFlux=explode(";",$FluxM);
	$mTrad=explode("*",$MultiTrad);
	$sFlux=explode(";",$FluxS);
	$sTrad=explode(";",$SignlTrad);
	$nFlux=explode(";",$FluxN);
	
	$sDescp=explode(";",$DescpS);
    $mDescp=explode("*",$DescpM);
    header('Content-type: application/vnd.mozilla.xul+xml');
    //print_r($nFlux);
	?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<?xul-overlay href="overlay/popupset.php?f=1"?>
<?xul-overlay href="overlay/tree.php?box=box1&ParamNom=GetOntoTree"?>
<?xml-stylesheet rel="stylesheet" href="xbl/editableTree/demo.css" type="text/css" title="css"?>

<window id="ieml-global" title="IEML-10eF v0.1 - information economy meta language - Dixième Famille" orient="horizontal" left="0" top="0" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<script language="JavaScript" type="application/x-javascript" src="js/ajax.js"/>
    <script src="js/TradTagIeml.js"/>
    <script src="js/histogrammes.js"/>
    <popupset id="popupset">
	</popupset>
	<vbox id="traduction"  style="height:600px;width:1000px;"> 
		<hbox >
			<hbox xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"  style="background-color:blue;">
	      		<?php
		       		if(sizeof($sFlux)>1){
				     	$objXul->Tree($sFlux,$sTrad,$sDescp,"Singl_Trad","true");
			         }
			         if(sizeof($mFlux)>1){
				    	 $objXul->Tree($mFlux,$mTrad,$mDescp,"Multi_Trad","true");
			         }
			         if(sizeof($nFlux)>1){
				    	$objXul->Tree($nFlux,"","","No_Trad","false");
					}
		        ?>
			</hbox>	
	        <splitter collapse="before" resizeafter="farthest">
				<grippy/>
			</splitter>
	        <vbox id="box3">
			<hbox >
				<vbox >
					<vbox >
						<label value="Langage : ieml"/>
					    <label id="id-trad-ieml" hidden="true"/>
						<label value="code :"/><label id="code-trad-ieml"  />
						<label value="descriptif : "/><label id="lib-trad-ieml"  />
					</vbox>
					</vbox>
					<vbox >
						<vbox>
							<label id="trad-Sup-message" />			
							<label id="trad-message" />
						</vbox>
						<vbox  >
							<button label="Ajouter une traduction" oncommand="AddTrad();"/>	
							<button label="Supprimer une traduction" oncommand="SupTrad();"/>				
							
						</vbox>
					</vbox>
				<vbox >
				<vbox flex="1">
					<label value="Langage : flux"/>
				    <label id="id-trad-flux" hidden="true"/>
					<label value="code :"/><label id="code-trad-flux"  />
				    <label value="descriptif : "/><label id="lib-trad-flux"  />
				</vbox>
			</vbox>
		</hbox>
	 </vbox>
	<splitter collapse="before" resizeafter="farthest">
			<grippy/>
	</splitter>
	<box id="box1" style="height:400px;width:300px;" ></box>
	</hbox>
</vbox>
		
</window>