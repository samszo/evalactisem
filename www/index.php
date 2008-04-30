<?php
require('library/php-delicious/php-delicious.inc.php');
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
	$lbl = "label='Connection to del.icio.us failed.' style='color:red;size=10'";
}elseif($con==3){
	$lbl = "label='Del.icio.us API access throttled.'" ;
}else{  
	$lbl = "label='traduction, semantique, ieml, delicious .....' style='color:blue;size:20px'"; 
}

header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
header ("title: Saisi des diagnosics d'accessibilité");
echo '<' . '?xml version="1.0" encoding="iso-8859-15" ?' . '>';
echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
echo ('<' . '?xml-stylesheet href="onada.css" type="text/css"?' . '>' . "\n");
?>
<window id="trad_flux" title="traduction Flux" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" >
	
	<script src="js/histogrammes.js"/>
	<script src="js/Interface.js"/>
	<script src="js/ajax.js"/>
	<script src="js/TradTagIeml.js"/>
	<script src="js/groupbox.js"/>
	<script type="text/javascript" > 
		var grpBox= new GroupBox('box1'); 
		var TradIeml= new Traduction(); 
		var Flux;
	</script>

		<popupset id="popupset">
		<popup id="iemlmenu" onpopupshowing="javascript:;">
			<menuitem label="Parser" oncommand="startInsert(event);"/>
		</popup>
	</popupset>
	<hbox >
		<label value="Utilisateur connecter : <?php echo $_SESSION['loginSess']; ?>"/>
		<label value="logout" onclick="window.location.replace('exit.php') ; " />
	</hbox>
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
							     <menuitem  label="Afficher tous les Tags "     value="GetAllTags"          oncommand=""/>
							     <menuitem  label="Afficher les Posts recents"  value="GetRecentPosts"      oncommand="grpBox.CreatGrpBox('box1');"/> 
							     <menuitem  label="Afficher le Posts"           value="GetPosts"            oncommand="grpBox.CreatGrpBox('box1');"/>
							     <menuitem  label="Afficher tous les Posts"     value="GetAllPosts"         oncommand=""/>
							     <menuitem  label="Afficher tous les Bundles"   value="GetAllBundles"       oncommand=""/>
							   </menupopup>
							</menulist>
							<box id="box1" ></box>		    
					</groupbox>
					
					<groupbox orient="vertical" >
						<caption label="Graphique"/>
						    <label value="Titre"/>
							<textbox persist="value" id="titre" value="Traduction du Flux"/>
							<label value="Type "/>
							<menulist id="type"  >
								<menupopup>
									<menuitem label="Nombre de Tags" value="GetAllTags"/>
									<menuitem label="Tags par Bundles" value="tagsFbundles"/>
								</menupopup>
							</menulist>
						    <button id="RecupFlux" label="Afficher le graphique"  onclick="RecupDeliciousFlux();"/>
					</groupbox>
					
					<groupbox orient="vertical" >
						<caption label="IEML"/>
						
			    			<button id="TypeGraphe" label="Traduire le flux" tooltiptext="Voir l'histogramme" onclick="SetDonnee();"/>
			    			<button id="TypeGraph" label="Affichage du graphique" tooltiptext="Voir l'histogramme" onclick="SetDonnee();"/>
			     			<button hidden="true" id="bt_10" label="Gérer les traductions"  onclick="Trad('webFrame','Traduction.xul');"/>

					</groupbox>
					
				</vbox>
				<splitter />
				
				<vbox flex="1">
					<groupbox flex="1" >
						<caption label="Visualisation des graphiques"/>
						<iframe id="webFrame" flex="1" src="library/CreaPapiDyna.php"  />
					</groupbox>
					<splitter />
					<groupbox orient="horizontal" id="traduction" >
						<caption label="Visualisation des données"/>
						<box hidden="true">
						    <label id="id-trad-ieml" hidden="true"/>
							<label value="code :"/><label id="code-trad-ieml"  />
							<label value="descriptif : "/><label id="lib-trad-ieml"  />
										<label id="trad-Sup-message" />			
										<label id="trad-message" />
										<label id="trad-Sup-message" />			
										<label id="trad-message" />
										<button label="Ajouter une traduction" oncommand="AddTrad();"/>	
										<button label="Supprimer une traduction" oncommand="SupTrad();"/>				
						</box>
						<iframe  id="treeReq" flex="1" />
					</groupbox>
				</vbox>
			</groupbox>
		</vbox> 
 </hbox>
 <script type="text/javascript">
 	//récupération des flux
 	
 	RecupDeliciousFlux();
 	
 </script>
</window>

