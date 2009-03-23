<?php

require('param/ParamPage.php');


function ChercheAbo ()
	{
		// connexion a delicious
		global $con;
		
		$login=$_POST['login_uti'];
		$mdp=$_POST['mdp_uti'];
		if(TRACE)
			echo "index:ChercheAbo:login:".$_POST['login_uti']." mdp=".$_POST['mdp_uti']."<br/>";
   	   	
		if(($login!="")&&($mdp!="")){
			$oDelicious = new PhpDelicious($login, $mdp);
			$_SESSION['loginSess']=$login;
			$_SESSION['mdpSess']=$mdp;
			$_SESSION['Delicious']=$oDelicious;
			if(TRACE)
				echo "ParamPage:Debug:oDelicious=".$oDelicious->sUsername."<br/>";
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

header ("Content-type: application/vnd.mozilla.xul+xml; charset=utf-8");
header ("title: Saisi des diagnosics d'accessibilit�");
echo '<' . '?xml version="1.0" encoding="utf-8" ?' . '>';
echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
echo ('<' . '?xml-stylesheet href="CSS/tree.css" type="text/css"?' . '>' . "\n");
echo ('<' . '?xml-stylesheet href="CSS/iemlCycle.css" type="text/css"?' . '>' . "\n");
echo '<'.'?xul-overlay href="overlay/treeDicoIeml.xul"?'.'>';
?>
<window flex="1" id="trad_flux" title="traduction Flux" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" onload=" ">
    <script src="library/js/tree.js"/>
	<script src="library/js/Interface.js"/>
	<script src="library/js/ajax.js"/>
 	<script src="library/js/utf8.js"/>
	

	<script type="text/javascript" > 
		var Flux; var f;
		var urlExeAjax = "<?php echo ajaxPathWeb; ?>";
		var urlAjax = "<?php echo PathWeb; ?>";
		var urlSpreadsheet="http://spreadsheets.google.com/pub?key=" ;
		var Items;
		var type;
		var lang='fr';
	</script>

	<popupset id="popupset">
		<popup id="clipmenu" onpopupshowing="javascript:;">
			<menuitem label="Voir les primitives" oncommand="ParserIemlExp('|','Primitive');"/>
			<menuitem label="Voir les �v�nements" oncommand="ParserIemlExp('|','Event');"/>
		</popup>
	</popupset>

	<hbox id="InOut" >
		<label id='uti_login' value="Utilisateur connecter : <?php echo $_SESSION['loginSess']; ?>"/>
		<label value="logout" onclick="window.location.replace('exit.php') ; " />
	</hbox>
	<vbox id='Maj' hidden='true' >
	   <label id='label_Maj' value='Patienter' style='font-style:normal;color: green'/>
	   <progressmeter id="progmeter" mode="undetermined"  />
   </vbox>

	<vbox id="main" flex="1">
		 <groupbox orient="horizontal" flex="1" >
			<caption <?php echo $lbl;?> />
			<vbox id="infosTrad" flex="1" >
			    <hbox style='margin-left:800px;margin-top:0px;'>
				<button label="Explorer le Nuage de Tag" tooltiptext="Explorer les tags" onclick="window.open('http://localhost/evalactisem/tagcloud.xul','Tag Cloud',1500,1500);"/>
				<button label="Mettre à jour le bookmark IEML" tooltiptext="Met à jour le bookmark collaboratif IEML" onclick="BookMark_AddPostIemlDelicious();"/>
			</hbox>
				<label id="trad-message" hidden="false" style="color:blue;" />
				<hbox flex="1" >
					<groupbox orient="horizontal" >
						<caption label="Tag delicious"/>
					    <label id="id-trad-flux" hidden="true"/>
						<stack style='height:150px;' flex='1'>
							<textbox id="code-trad-flux" style="color:red;font-size:150%" onkeyup="lancer(event);" autocomplete="off" />
							<listbox id="calque_tag" style='margin-top:50px;' hidden='true' onselect='getSelectItemRech("code-trad-flux");'></listbox>
					    </stack>

					    <label hidden="true" id="lib-trad-flux" style="color:red;font-size:150%" />
					</groupbox>
					<groupbox orient="horizontal" flex="1" >
							<caption label="Expression IEML"/>
						    <label id="id-trad-ieml" hidden="true"/>
						    
							<stack style='height:150px;' flex='1'>
								<textbox id="lib-trad-ieml" multiline="true" style="color:red;font-size:150%;height:50px;border-top:0px" flex="1" onkeyup="lancer(event);" autocomplete="off"  />
								<listbox id="calque_lib" flex='1' style='margin-top:50px;' hidden='true' onselect='getSelectItemRech("lib-trad-ieml","code-trad-ieml");'></listbox>
							</stack>
							<label style="color:red;font-size:150%" value=" *" />
							<stack  flex='1'>
								<textbox id="code-trad-ieml" multiline="true" style="color:red;font-size:150%;" flex="1" onkeyup="lancer(event);" autocomplete="off" />
								<listbox id="calque_code"  style='margin-top:50px;' hidden='true' onselect='getSelectItemRech("code-trad-ieml","lib-trad-ieml");' ></listbox>
							</stack>
							<label style="color:red;font-size:150%" value="** " />									
					</groupbox>
				</hbox>
				<hbox>
						<button hidden="false" label="Ajouter" tooltiptext="Ajouter une Traduction" oncommand="Sem_AddTrad();"/>	
						<button hidden="false" label="Supprimer" tooltiptext="Supprimer une Traduction" oncommand="Sem_SupTrad();"/>		
						<label id="trad-message" hidden="false" style="color:blue;font-size:150%" />

				</hbox>
			</vbox>
		</groupbox>
		
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
									<hbox hidden="true" >
										<label  style='color:blue;' value="3. Cliquez ici" />
										<button label="Ajouter la traduction" oncommand="AddTrad();"/>	
									</hbox>
									<box id="tpNoTrad" flex="1" />
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
		</vbox>
 </vbox>
 <script type="text/javascript">
 	//r�cup�ration des flux
  GetFlux();

 </script>
 
 
</window>
