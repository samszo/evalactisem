<?php
set_time_limit(3000);
require('library/php-delicious/php-delicious.inc.php');
require('param/Constantes.php');
define('DELICIOUS_USER', $_POST['login_uti']);
define('DELICIOUS_PASS', $_POST['mdp_uti']);

session_start();
extract($_SESSION,EXTR_OVERWRITE);
extract($_POST,EXTR_OVERWRITE);

function ChercheAbo ()
	{
		// connexion a delicious
		global $con;
		
		$login=$_POST['login_uti'];
		$mdp=$_POST['mdp_uti'];
   	   	
		if(($login!="")&&($mdp!="")){
	    	$oDelicious = new PhpDelicious($login, $mdp);
			$_SESSION['loginSess']=$login;
			$_SESSION['Delicious']=$oDelicious;
			$oDelicious->DeliciousRequest('posts/delete', array('url' => $sUrl));
			$con=$oDelicious->LastError();
			if ($con==2)
			{
				echo "Incorrect del.icio.us username or password";
				include("login.php");
				exit;
			}
			}else{
				include("login.php");
				exit;
		}
}


ChercheAbo ();

if($con==1){
	$lbl = "label='Connection to del.icio.us failed.' style='color:red;size:10'";
}elseif($con==3){
	$lbl = "label='Del.icio.us API access throttled.'" ;
}else{  
	$lbl = "label='Traduction del.icio.us -> IEML ' style='color:blue;font-size:150%'"; 
}

header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
header ("title: Saisi des diagnosics d'accessibilité");
echo '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
echo ('<' . '?xml-stylesheet href="CSS/tree.css" type="text/css"?' . '>' . "\n");
echo ('<' . '?xml-stylesheet href="CSS/iemlCycle.css" type="text/css"?' . '>' . "\n");
echo '<'.'?xul-overlay href="overlay/treeDicoIeml.xul"?'.'>';
?>
<window flex="1" id="trad_flux" title="traduction Flux" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" onload=" ">

	<script src="library/js/Interface.js"/>
	<script src="library/js/ajax.js"/>
	<script src="library/js/TradTagIeml.js"/>
	<script src="library/js/groupbox.js"/>
	<script src="library/js/tree.js"/>
	<script src="library/js/iemlBoussole.js"/>
	<script src="http://www.google.com/jsapi" />
 	<script src="library/js/GoogleDoc.js"/>
 	<script src="library/js/utf8.js"/>
	

	<script type="text/javascript" > 
		var grpBox= new GroupBox('box1'); 
		var TradIeml= new Traduction(); 
		var Flux;
		var urlExeAjax = "<?php echo ajaxPathWeb; ?>";
		var urlAjax = "<?php echo PathWeb; ?>";
	</script>

		<popupset id="popupset">
			<popup id="clipmenu" onpopupshowing="javascript:;">
				<menuitem label="Voir les primitives" oncommand="Parser('|','Primitive');"/>
				<menuitem label="Voir les événements" oncommand="Parser('|','Event');"/>
			</popup>
		</popupset>
	<hbox >
	   
		<label value="Utilisateur connecter : <?php echo $_SESSION['loginSess']; ?>"/>
		<label value="logout" onclick="window.location.replace('exit.php') ; " />
	</hbox>
	<vbox id='Maj' hidden='true' >
	   <label id='label_Maj' value='Patienter' style='font-style:normal;color: green'/>
	   <progressmeter id="progmeter" mode="undetermined"  />
   </vbox>
	<label id="tradu" hidden="true" value=""/>
	<hbox id="histogramme" flex="1">
	   <vbox flex="1" >
		 <groupbox orient="horizontal" flex="1" >
			<caption <?php echo $lbl;?> />
				<vbox>
					<groupbox orient="vertical" hidden="true" >
						<caption label="Flux del.icio.us"/>
							<label id="selctreq" value="" hidden="true"/>
							<label value="requête" />
							<menulist id="requette" oncommand="">
							   <menupopup >
							     <menuitem  label="Afficher tous les Tags "     value="GetAllTags"          oncommand="grpBox.CreatGrpBox('box1');"/>
							     <menuitem  label="Afficher les Posts recents"  value="GetRecentPosts"      oncommand="grpBox.CreatGrpBox('box1');"/> 
							     <menuitem  label="Afficher le Posts"           value="GetPosts"            oncommand="grpBox.CreatGrpBox('box1');"/>
							     <menuitem  label="Afficher tous les Posts"     value="GetAllPosts"         oncommand="grpBox.CreatGrpBox('box1');"/>
							     <menuitem  label="Afficher tous les Bundles"   value="GetAllBundles"       oncommand="grpBox.CreatGrpBox('box1');"/>
							   </menupopup>
							</menulist>
							<box id="box1" ></box>		    
						    <button id="ShowFlux" label="Afficher les données"  onclick="RecupDeliciousFlux();"/>
					</groupbox>
										
					<groupbox orient="vertical" hidden="true" >
						<caption label="IEML"/>
						
			    			<button id="TypeGraphe" label="Traduire le flux" tooltiptext="Voir l'histogramme" onclick="SetDonnee();"/>
			    			<button hidden="false" id="PostTo" label="Mettre à jour delicious" tooltiptext="Met à jour le bookmark collaboratif IEML" onclick="AddPostIemlDelicios();"/>
			     			
					</groupbox>
					
					<groupbox orient="vertical" hidden="true" >
						<caption label="Graphique"/>
						    <label value="Titre" hidden="true"/>
							<textbox persist="value" hidden="true" id="titre" value=""/>
							<label value="Type "/>
							<menulist id="type"  >
								<menupopup>
									<menuitem label="Nombre de Tags" value="GetAllTags"/>
									<menuitem label="Tags par Bundles" value="tagsFbundles"/>
								</menupopup>
							</menulist>
						    <button id="RecupFlux" label="Afficher le graphique"  onclick="RecupDeliciousFlux();"/>
					</groupbox>
					
					<groupbox orient="vertical" hidden="true">
						<caption label="Administration" />
						
			    			<button id="AdminDelicious" label="Suppimer mon compte" tooltiptext="Voir l'histogramme" onclick="SupprimerCompteDelicious();"/>

					</groupbox>
				</vbox>

			        <splitter collapse="before" resizeafter="farthest" hidden="true">
						<grippy/>
					</splitter>
				
				<hbox flex="1">
						
						<vbox id='TableFlux'  flex="1"></vbox>
					   
						
							
						<vbox id="infosTrad" hidden="true" flex="1" >
								<hbox >
									<groupbox orient="horizontal" >
										<caption label="Tag delicious"/>
									    <label id="id-trad-flux" hidden="true"/>
										<label hidden="true" value="code :"/><label id="code-trad-flux" style="color:red;font-size:150%" />
									    <label hidden="true" value="descriptif : "/><label hidden="true" id="lib-trad-flux" style="color:red;font-size:150%" />
									</groupbox>
									<groupbox orient="horizontal" >
										<caption label="Expression IEML"/>
									    <label id="id-trad-ieml" hidden="true"/>
										<label value="descriptif : " hidden="true"/><label id="lib-trad-ieml" style="color:red;font-size:150%" />
										<label value="code :" hidden="true" />
										<label style="color:red;font-size:150%" value=" *" />
										<label id="code-trad-ieml" style="color:red;font-size:150%" />
										<label style="color:red;font-size:150%" value="** " />
									</groupbox>
								</hbox>
								<hbox>
										<button hidden="true" label="Ajouter" oncommand="AddTrad();"/>	
										<button hidden="true" label="Supprimer" oncommand="SupTrad();"/>
										<button hidden="true" label="Modifier" oncommand="ModifTrad();"/>
										<label id="trad-Sup-message" hidden="true" />			
										<label id="trad-message" hidden="false" style="color:blue;font-size:150%" />
								</hbox>
							<vbox id="contDonnee" flex="1" hidden="true" >
								<tabbox flex="1" >
								    <tabs >
								        <tab label="Tags traduits" />
								        <tab label="Tags à traduire" />
								    </tabs>
								    <tabpanels flex="1"  >
								        <tabpanel >
											<vbox flex="1" >
												<hbox>
													<button label="Supprimer la traduction" oncommand="SupTrad();"/>
													<button label="Voir les primitives" oncommand="Parser('|','Primitive')"/>	
													<button label="Voir les événements" oncommand="Parser('|','Event');"/>
												</hbox>
												<box id="tpSingleTrad" flex="1" context="clipmenu" />
											</vbox >
								         </tabpanel>
								        <tabpanel>
								        	<vbox flex="1">
												<label  style='color:blue;' value="1. Choisissez un tag dans les onglets 'Tag(s) trouvé(s)' ou 'Tag(s) non trouvé(s)'" />
												<label  style='color:blue;' value="2. Choisissez une expression IEML dans l'onglet 'Dictionnaire et Cycles'" />
												<hbox>
													<label  style='color:blue;' value="3. Cliquez ici" />
													<button label="Ajouter la traduction" oncommand="AddTrad();"/>	
												</hbox>
												<tabbox flex="1" >
												    <tabs >
												        <tab label="Tag(s) trouvé(s)" />
												        <tab label="Tag(s) non trouvé(s)" />
												        <tab label="Dictionnaire et Cycles" />
												    </tabs>
												    <tabpanels flex="1"  >
												        <tabpanel >
															<vbox flex="1" >
																<box id="tpMultiTrad" flex="1" />
															</vbox >
												         </tabpanel>
												        <tabpanel>
															<vbox flex="1" >
																<box id="tpNoTrad" flex="1" />
															</vbox >
												         </tabpanel>
												         <tabpanel>
												         	<vbox flex="1">
																<label id="keyGrid" hidden="true" />
																<tabbox flex="1" orient="horizontal" >
																    <tabs orient="vertical" >
																        <tab label="Dictionnaire" />
																        <tab label="Behavior" onclick="ChargeCycle('p8PAs8y8e1x3J43Fu2t0bDg');" syle="color:green"/>
																        <tab label="axial orientation" onclick="ChargeCycle('p8PAs8y8e1x2YTS7Zgag7Nw');" />
																    </tabs>
																    <tabpanels flex="1"  >
																        <tabpanel >
																			<vbox id="treeDicoIeml" flex="1"  />
																         </tabpanel>
																        <tabpanel>
																			<box  style="overflow:auto"  id="iemlCycle_p8PAs8y8e1x3J43Fu2t0bDg" flex='1' />
																         </tabpanel>
																         <tabpanel>
																			<box  style="overflow:auto"  id="iemlCycle_p8PAs8y8e1x2YTS7Zgag7Nw" flex='1'  />
																        </tabpanel>
																    </tabpanels>
																</tabbox>
															</vbox>			
												        </tabpanel>
												    </tabpanels>
												</tabbox>
											</vbox>
								         </tabpanel>
								    </tabpanels>
								</tabbox>
							</vbox>
						</vbox>
			        <splitter collapse="before" >
						<grippy/>
					</splitter>
					<vbox flex="1">
						<tabbox id="tbIframe" flex="1" >
						    <tabs >
						        <tab label="Bookmark de <?php echo $_SESSION['loginSess']; ?>" />
						        <tab label="Bookmark IEML" />
						        <tab label="Boussole IEML" />
						        <tab id="tabStatIeml" label="Statistiques IEML" />
						    </tabs>
						    <tabpanels flex="1"  >
						        <tabpanel >
						        	<!-- 
						          	-->
									<iframe flex="1" src="http://del.icio.us/<?php echo $_SESSION['loginSess']; ?>"  />
						         </tabpanel>
						        <tabpanel>
									<vbox flex="1" >
						    			<button label="Mettre à jour le bookmark IEML" tooltiptext="Met à jour le bookmark collaboratif IEML" onclick="AddPostIemlDelicios();"/>
										<iframe flex="1" src="http://del.icio.us/ieml"  />
						        	<!-- 
						          	-->
									</vbox >
						         </tabpanel>
						        <tabpanel>
										<iframe id="ifBoussole" style="min-width: 150px;" flex="1"  src="library/svg/iemlBoussole.svg"  />
						         </tabpanel>
						        <tabpanel>
						        		<box id="bIemlStat" flex="1">
											<iframe id="fIemlStat" flex="1"  src=""  />
										</box>
						         </tabpanel>
						    </tabpanels>
						</tabbox>







					</vbox>
				</hbox>
			</groupbox>
		</vbox> 
 </hbox>
 <script type="text/javascript">
 	//récupération des flux
  GetFlux();
   
 </script>
 
 
</window>
