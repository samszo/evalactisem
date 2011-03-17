<?php

require('param/ParamPage.php');

ChercheAbo ();

if($con==1){
	$lbl = "label='Connection to del.icio.us failed.' style='color:red;size:10'";
}elseif($con==3){
	$lbl = "label='Del.icio.us API access throttled.'" ;
}else{  
	$lbl = "label='Traduction Tag -> IEML ' style='color:blue;font-size:150%'"; 
}

header ("Content-type: application/vnd.mozilla.xul+xml; charset=utf-8");
header ("title: Saisi des diagnosics d'accessibilité");
echo '<' . '?xml version="1.0" encoding="utf-8" ?' . '>';
echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
echo ('<' . '?xml-stylesheet href="CSS/tree.css" type="text/css"?' . '>' . "\n");
echo ('<' . '?xml-stylesheet href="CSS/iemlCycle.css" type="text/css"?' . '>' . "\n");
echo '<'.'?xul-overlay href="overlay/treeDicoIeml.xul"?'.'>';
?>
<window flex="1" id="trad_flux" title="traduction Flux" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" onload=" ">
	<script type="text/javascript" > 
		var Flux; var f;
		var urlExeAjax = "<?php echo ajaxPathWeb; ?>";
		var urlAjax = "<?php echo PathWeb; ?>";
		var urlSpreadsheet="http://spreadsheets.google.com/pub?key=" ;
		var Items;
		var type;
		var lang='fr';
		var selectCalque;
		var m=0;
	</script>

    <script src="library/js/tree.js"/>
	<script src="library/js/Interface.js"/>
	<script src="library/js/ajax.js"/>
 	<script src="library/js/utf8.js"/>
	<script src="library/js/tagcloud.js"/>
	
	<popupset id="popupset">
		<popup id="clipmenu" onpopupshowing="javascript:;">
			<menuitem label="Voir les primitives" oncommand="ParserIemlExp('|','Primitive');"/>
			<menuitem label="Voir les événements" oncommand="ParserIemlExp('|','Event');"/>
		</popup>
	</popupset>
	<toolbox >
	  <menubar id="evalactisem-menubar">
	  	<!--  
	    <menu id="flux" label="Flux">
	      <menupopup id="Flux-popup">
	        <menuitem label="Del.icio.us" onclick="GetFlux(true);"/>
	        <menuitem label="Twitter"/>
	      </menupopup>
	    </menu>
	    -->
	    <menu id="lang_flux" label="Langues" >
	      <menupopup id="Lang_flux-popup" >
	        <menuitem id='ar' label="Ar" type='radio' oncommand="lang=this.id;Xul_Ajax_ShowTreeTrad('');" />
	        <menuitem id='en' label="En" type='radio' oncommand="lang=this.id;Xul_Ajax_ShowTreeTrad('');"/>
	        <menuitem id='fr' label="Fr" type='radio' oncommand="lang=this.id;Xul_Ajax_ShowTreeTrad('');"/>
	        <menuitem id='zh' label="Zh" type='radio' oncommand="lang=this.id;Xul_Ajax_ShowTreeTrad('');"/>
	      </menupopup>
	    </menu>
	     <menu id="bookmark_ieml" label="Bookmark IEML">
	      <menupopup id="bookmark_ieml-popup">
	        <menuitem id='MAJ_bookmark_ieml' label="Mettre à jour" onclick="BookMark_AddPostIemlDelicious();" />
	      	<menuitem id='Consulter' label="Consulter" onclick="window.open('http://delicious.com/ieml')" />
	      </menupopup>
	    </menu>
	    <menu id="outils" label="Outils">
	      <menupopup id="Outils-popup">
	        <menuitem id='Dictio' label="Dictionnaire" onclick="window.open('http://www.ieml.org/french/elements.html')" />
	        <menuitem id='LiveMetal' label="Live Metal" onclick="window.open('http://evalactisem.ieml.org')" />
	      </menupopup>
	    </menu>
	    <menu id="logout" label="Logout" onclick="window.location.replace('exit.php') ;">
	     <menupopup id="Outils-popup">
	    </menupopup>
	    </menu>
	  </menubar>
	</toolbox>
	<hbox id="InOut" >
		<label id='uti_login' value="Utilisateur connecter : <?php echo $_SESSION['loginSess']; ?>"/>
	</hbox>
	<vbox id='Maj' hidden='true' >
	   <label id='label_Maj' value='Patienter' style='font-style:normal;color: green'/>
	   <progressmeter id="progmeter" mode="undetermined"  />
   </vbox>

	<vbox id="main" flex="1">

		<tabbox flex="1" >
		    <tabs >
		        <tab label="Traduction" />
		        <tab label="Exploration" />
		    </tabs>
		    <tabpanels flex="1"  >
		        <tabpanel >
					<vbox flex="1" >

	
		 <groupbox orient="horizontal" flex="1" >
			<vbox id="infosTrad" flex="1" >
				<label id="trad-message" hidden="false" style="color:blue;" />
				<hbox flex="1" >
					<groupbox orient="horizontal" >
						<caption label="Tags"/>
					    <label id="id-trad-flux" hidden="true"/>
						<stack style='height:150px;margin-top:20px;"' flex='1'>
							<textbox id="code-trad-flux" style="color:red;font-size:150%" onkeyup="lancer(event);" autocomplete="off" />
							<listbox id="calque_tag" style='margin-top:50px;' hidden='true' onselect='getSelectItemRech("code-trad-flux");'></listbox>
					    </stack>
					    <label hidden="true" id="lib-trad-flux" style="color:red;font-size:150%" />
					</groupbox>
					<groupbox orient="horizontal" flex="1" >
							<caption label="Expression IEML"/>
							<vbox>
								<box id='Lang'></box>
							    <label id="id-trad-ieml" hidden="true"/>
								<stack style='height:150px;' flex='1'>
									<textbox id="lib-trad-ieml" multiline="true" style="color:red;font-size:150%;height:50px;border-top:0px" flex="1" onkeyup="lancer(event);" autocomplete="off"  />
									<listbox id="calque_lib" flex='1' style='margin-top:50px;' hidden='true' onselect='getSelectItemRech("lib-trad-ieml","code-trad-ieml","lib");'></listbox>
								</stack>
							</vbox>
							<label style="color:red;font-size:150%;margin-top:20px;" value=" *" />
							<stack  flex='1' style="margin-top:20px;">
								<textbox id="code-trad-ieml" multiline="true" style="color:red;font-size:150%;" flex="1" onkeyup="lancer(event);" autocomplete="off" />
								<listbox id="calque_code"  style='margin-top:50px;' hidden='true' onselect='getSelectItemRech("code-trad-ieml","lib-trad-ieml","code");' ></listbox>
							</stack>
							<label style="color:red;font-size:150%;margin-top:20px;" value="** " />	
														
					</groupbox>
				</hbox>
				<hbox>
						<button hidden="false" label="Ajouter" tooltiptext="Ajouter une Traduction" oncommand="Sem_AddTrad();"/>	
						<button hidden="false" label="Supprimer" tooltiptext="Supprimer une Traduction" oncommand="Sem_SupTrad();"/>		
						<label id="trad-message" hidden="false" style="color:blue;font-size:150%" />

				</hbox>
			</vbox>
		</groupbox>
	   	<splitter collapse="before" >
			<grippy/>
		</splitter>
		
		<vbox flex="6" >
			<hbox flex="6">
				<vbox id="contDonnee" flex="1" hidden="true" >
					<tabbox flex="1" >
					    <tabs >
					        <tab label="Tags traduits" />
					        <tab label="Tags à traduire" />
					    </tabs>
					    <tabpanels flex="1"  >
					        <tabpanel >
								<vbox flex="1" >
									
									<hbox hidden="true">												    
										<button label="Supprimer la traduction" oncommand="Sem_SupTrad();"/>
										<button label="Voir les primitives" oncommand="ParserIemlExp('|','Primitive')"/>	
										<button label="Voir les événements" oncommand="ParserIemlExp('|','Event');"/>
									</hbox>
									<box id="tpSingleTrad" flex="1" />
								</vbox >
					         </tabpanel>
					        <tabpanel>
					        	<vbox flex="1">
									<label hidden="true" style='color:blue;' value="1. Choisissez un tag" />
									<label hidden="true" style='color:blue;' value="2. Choisissez une expression IEML dans l'onglet 'Dictionnaire et Cycles'" />
									<button label="Traduire automatiquement" oncommand="Xul_GetTradAuto();"/>	
									<box id="GetTradAuto" flex="1" />
									<box id="tpNoTrad" flex="4" />
								</vbox>
					         </tabpanel>
					    </tabpanels>
					</tabbox>
				</vbox>
		        <splitter collapse="before" >
					<grippy/>
				</splitter>
				<vbox flex="1">
					<label id="keyGrid" hidden="true" />
					<tabbox flex="1" orient="horizontal" >
						<tabs orient="vertical" >
							<tab label="Dictionnaire" />
						</tabs>
						<tabpanels flex="1"  >
							<tabpanel >
								<vbox id="treeDicoIeml" flex="1"  />
							</tabpanel>
						</tabpanels>
					</tabbox>
				</vbox>			
			</hbox>
									<!--  
				
				<vbox flex="1" hidden="true">
					<tabbox id="tbIframe" flex="1" >
					    <tabs >
					        <tab label="Bookmark de <?php echo $_SESSION['loginSess']; ?>" />
					        <tab label="Bookmark IEML" />
					        <tab label="Boussole IEML" />
					        <tab id="tabStatIeml" label="Statistiques IEML" />
					    </tabs>
					    <tabpanels flex="1"  >
					        <tabpanel >
								<iframe flex="1" src="http://del.icio.us/<?php echo $_SESSION['loginSess']; ?>"  />
					         </tabpanel>
					        <tabpanel>
								<vbox flex="1" >
					    			<button label="Mettre � jour le bookmark IEML" tooltiptext="Met � jour le bookmark collaboratif IEML" onclick="BookMark_AddPostIemlDelicious();"/>
									<iframe flex="1" src="http://del.icio.us/ieml"  />
								</vbox >
					         </tabpanel>
					        <tabpanel>
								<vbox flex="1" >
									<label  style='color:blue;' value="Vous pouvez utiliser la boussole IEML pour filtrer les expressions des cycles" />
									<iframe id="ifBoussole" style="min-width: 150px;" flex="1"  src="library/svg/iemlBoussole.svg"  />
								</vbox>
					         </tabpanel>
					        <tabpanel>
					        		<box id="bIemlStat" flex="1">
										<iframe id="fIemlStat" flex="1"  src=""  />
									</box>
					         </tabpanel>
					    </tabpanels>
					</tabbox>
	-->

 <!--  fin dee l'onglet traduction -->
		</vbox>
								</vbox>
 					         </tabpanel>
					        <tabpanel>


<hbox id="main" flex="1" style="overflow:auto" >
	<vbox id="outils" flex="1" >
		<hbox >
			<groupbox orient="horizontal" >
				<caption label='Choix de la représentation' />
				 <menulist id="choixTC" >
				    <menupopup>
				      <menuitem label="Posts" value="posts"/>
				      <menuitem label="Tags" value="tags"/>
				      <menuitem label="Bulles" value="bulles"/>
				    </menupopup>
				  </menulist>
				 <menulist id="choixAjout" >
				    <menupopup>
				      <menuitem label="Remplace" value="-1"/>
				      <menuitem label="Ajoute" value="0"/>
				    </menupopup>
				  </menulist>
				 <menulist id="ShowAll" >
				    <menupopup>
				      <menuitem label="Taille réelle" value="false"/>
				      <menuitem label="Taille compressée" value="true"/>
				    </menupopup>
				  </menulist>
			</groupbox>
			<groupbox orient="horizontal" >
				<caption label='Choix de la langue' />
				 <menulist id="choixLangue" >
				    <menupopup>
				      <menuitem label="tag" value="tag"/>
				      <menuitem label="ieml" value="ieml"/>
				    </menupopup>
				  </menulist>
			</groupbox>
		</hbox>	
		<groupbox orient="vertical" >
			<caption label='Occurence des Tags' />
			<hbox>
				<label value="Min :" /><label id="lblTagIntMin" value="" />
			</hbox>
			<scrollbar
			    id="scrollTagIntMin" 
			    idLbl="lblTagIntMin"
			    orient="horizontal"
			    curpos="1"
			    maxpos="100"
			    increment="1"
			    pageincrement="10"
			    onmouseup="SelectNetwork('TreeDeliciousNetwork',1);"/>
			<hbox>
				<label value="Max :" /><label id="lblTagIntMax" value="" />
			</hbox>
			<scrollbar
			    id="scrollTagIntMax"
			    idLbl="lblTagIntMax"
			    orient="horizontal"
			    curpos="1"
			    maxpos="100"
			    increment="1"
			    pageincrement="10"
			    onmouseup="SelectNetwork('TreeDeliciousNetwork',1);"/>
		</groupbox>
		
		 <groupbox orient="vertical" >
			<caption label='Interval de temps' />
			<hbox>
				<label value="Début :" /><datepicker onchange="" id="dpTagDeb" type="popup" value="2007-03-26"/>
				<label value="Fin : " /><datepicker id="dpTagFin" type="popup" value="2007-03-26"/>
			</hbox>
			<checkbox id="TempsVide" label="Afficher les silences" checked="true"/>
		</groupbox>

		 <groupbox orient="horizontal" >
			<caption label='Afficher les traductions' />
			<checkbox id="TradUti" label="De <?php echo $_SESSION['loginSess']; ?>" oncommand="VoirTrad()" checked="true"/>
			<checkbox id="TradNetWork" label="De son réseau" oncommand="VoirTrad()" checked="true"/>
			<checkbox id="TradNo" label="pas faites" oncommand="VoirTrad()" checked="true"/>
		</groupbox>
		
		<groupbox orient="vertical" flex="1" >
			<caption label='Réseau social' />
			<hbox id="DeliciousNetwork" flex="1">
			</hbox>
		</groupbox>
	</vbox>
   	<splitter collapse="before" >
		<grippy/>
	</splitter>
	<vbox flex="1">
		<groupbox orient="vertical" flex="1" >
			<caption label='Représentation' />
			<hbox id="tagcloud" flex="1"  style="overflow:auto" />
		</groupbox>
	</vbox>
</hbox>

	<script>
		//création des listener
		document.getElementById("scrollTagIntMin").addEventListener("DOMAttrModified",
			function(event) {onScroll(event);}, false);	
		document.getElementById("scrollTagIntMax").addEventListener("DOMAttrModified",
			function(event) {onScroll(event);}, false);
		//récupération de l'arbre du réseau social
		GetTreeDeliciousNetwork();
	</script>
 
					         </tabpanel>
					    </tabpanels>
					</tabbox>
 
 <!--  fin du main -->
 </vbox>
 
 
 
 <script type="text/javascript">
 	//r�cup�ration des flux
  Sem_getLangLiveMetal(); 

  
 </script>
 
 
</window>
