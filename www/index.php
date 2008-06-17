<?php
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
	$lbl = "label='traduction, semantique, ieml, delicious .....' style='color:blue;size:20px'"; 
}

header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
header ("title: Saisi des diagnosics d'accessibilité");
echo '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
echo ('<' . '?xml-stylesheet href="tree.css" type="text/css"?' . '>' . "\n");
//echo ('<' . '?xml-stylesheet rel="stylesheet" href="xbl/editableTree/demo.css" type="text/css" title="css"?' . '>' . "\n");
echo '<'.'?xul-overlay href="overlay/treeDicoIeml.xul"?'.'>';
?>
<window flex="1" id="trad_flux" title="traduction Flux" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" >

	<script src="library/js/Interface.js"/>
	<script src="library/js/ajax.js"/>
	<script src="library/js/TradTagIeml.js"/>
	<script src="library/js/groupbox.js"/>
	<script src="library/js/tree.js"/>
	<script type="text/javascript"  src="http://www.google.com/jsapi"></script>
 	<script src="library/js/GoogleDoc.js"/>
	

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
	   <label id='label_Maj' value='Veuillez patienter la mise a jour est en cours...' style='font-style:normal;color: green'/>
	   <progressmeter id="progmeter" value="50%"  />
   </vbox>
	<label id="tradu" hidden="true" value=""/>
	<hbox id="histogramme" flex="1">
	   <vbox flex="1" >
		 <groupbox orient="horizontal" flex="1" >
			<caption <?php echo $lbl;?> />
				<vbox>
					<groupbox orient="vertical" >
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
										
					<groupbox orient="vertical" >
						<caption label="IEML"/>
						
			    			<button id="TypeGraphe" label="Traduire le flux" tooltiptext="Voir l'histogramme" onclick="SetDonnee();"/>
			    			<button hidden="false" id="PostTo" label="Mettre à jour del.icio.us" tooltiptext="Met à jour le bookmark collaboratif IEML" onclick="AddPostIemlDelicios();"/>
			     			<button hidden="true" id="bt_10" label="Gérer les traductions"  onclick="Trad('webFrame','Traduction.xul');"/>

					</groupbox>
					
					<groupbox orient="vertical" >
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
						    <button id="RecupFlux" label="Afficher le graphique"  onclick="RecupDeliciousFlux();GetCycleIeml();"/>
					</groupbox>
					
					<groupbox orient="vertical" >
						<caption label="Administration"/>
						
			    			<button id="AdminDelicious" label="Suppimer mon compte" tooltiptext="Voir l'histogramme" onclick="SupprimerCompteDelicious();"/>

					</groupbox>
				</vbox>
				
				<hbox flex="1">
					<groupbox flex="1" orient="horizontal" >
						<caption label="Visualisation des données"/>
						
						<vbox id='TableFlux'  flex="1"></vbox>
					   
						
							
						<vbox id="infosTrad" hidden="true" flex="1" >
							<groupbox >
								<caption label="Actions de traduction"/>
								<hbox >
									<groupbox orient="horizontal" >
										<caption label="Langage du flux"/>
									    <label id="id-trad-flux" hidden="true"/>
										<label value="code :"/><label id="code-trad-flux" style="background-color:yellow" />
									    <label value="descriptif : "/><label id="lib-trad-flux" style="background-color:yellow" />
									</groupbox>
									<groupbox orient="horizontal" >
										<caption label="Langage IEML"/>
									    <label id="id-trad-ieml" hidden="true"/>
										<label value="code :"/><label id="code-trad-ieml" style="background-color:yellow" />
										<label value="descriptif : "/><label id="lib-trad-ieml" style="background-color:yellow" />
									</groupbox>
								</hbox>
								<hbox>
										<button label="Ajouter" oncommand="AddTrad();"/>	
										<button label="Supprimer" oncommand="SupTrad();"/>
										<button label="Modifier" oncommand="ModifTrad();"/>
										<button label="Créer un noeud" oncommand="CreaNoeud();"/>
										<label id="trad-Sup-message" hidden="true" />			
										<label id="trad-message" hidden="true" />
										<label id="trad-Sup-message" hidden="true" />			
										<label id="trad-message" hidden="true" />
								</hbox>
							</groupbox>				
							<box id="contDonnee" flex="1" hidden="true" >
								<tabbox flex="1" >
								    <tabs >
								        <tab label="Tags traduits" />
								        <tab label="Tags avec plusieurs traduction" />
								        <tab label="Tags sans traduction" />
								        <tab label="Cycle de axial orientation / where" />
								    </tabs>
								    <tabpanels flex="1"  >
								        <tabpanel >
											<box id="tpSingleTrad" flex="1" context="clipmenu" />
								         </tabpanel>
								        <tabpanel>
											<box id="tpMultiTrad" flex="1" />
								         </tabpanel>
								        <tabpanel>
											<box id="tpNoTrad" flex="1" />
											<vbox id="treeDicoIeml" flex="1" hidden="true" />
								         </tabpanel>
								         <tabpanel>
											
											<iframe id="iemlCycle" flex='1' hidden="false" />
								        </tabpanel>
								    </tabpanels>
								</tabbox>
							</box>
						</vbox>
						
									
					</groupbox>
			        <splitter collapse="before" resizeafter="farthest">
						<grippy/>
					</splitter>
					<groupbox style="min-width: 350px;" orient="horizontal" >
						<caption label="Visualisation des graphiques"/>
						<iframe id="webFrame" style="min-width: 350px;" flex="1" src="http://del.icio.us/<?php echo $_SESSION['loginSess']; ?>"  />
					</groupbox>
				</hbox>
			</groupbox>
		</vbox> 
 </hbox>
 <script type="text/javascript">
 	//récupération des flux
   GetFlux();
 	
 </script>
 
 
</window>
