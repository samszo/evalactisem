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
	$iduti=$_SESSION['iduti'];
	    
	      
	header('Content-type: application/vnd.mozilla.xul+xml');
	echo '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
	echo '<' . 'xul-overlay href="overlay/popupset.php?f=1"' . '>' . "\n";
	echo '<' . 'xul-overlay href="overlay/tree.php?box=box1&ParamNom=GetOntoTree"' . '>' . "\n";
	echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
	echo ('<' . '?xml-stylesheet href="tree.css" type="text/css"?' . '>' . "\n");
	
	?>
<window id="ieml-global" title="IEML-10eF v0.1 - information economy meta language - Dixième Famille" orient="horizontal" left="0" top="0" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<script language="JavaScript" type="application/x-javascript" src="js/ajax.js"/>
    <script src="js/TradTagIeml.js"/>
    <script src="js/histogrammes.js"/>
    <popupset id="popupset">
	</popupset>
	<vbox id="trad" > 
		<hbox >
			<hbox xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"  style="background-color:blue;">
	      		<?php
					if(sizeof($sflux)>=1){
	   					$objXul->Tree_Trad($sFlux,$sTrad,$sDescp,"Signl_Trad","true",$T);  
					}
        
                   	if(sizeof($mFlux)>=1){
	    	 			$objXul->Tree_Trad($mFlux,$mTrad,$mDescp,"Multi_Trad","true",$T);
        		   	}
        		  	if(sizeof($nflux)>1){
	    				$objXul->Tree_Trad($nFlux,"","","No_Trad","false",$T);
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
	<box id="box1" flex="1"  ></box>
	</hbox>
</vbox>
		
</window>