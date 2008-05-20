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
	$lbl = "label='Connection to del.icio.us failed.' style='color:red;size=10'";
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

	<script src="js/Interface.js"/>
	<script src="js/ajax.js"/>
	<script src="js/TradTagIeml.js"/>
	<script src="js/groupbox.js"/>
	<script src="js/tree.js"/>

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
					</groupbox>
					
					<groupbox orient="vertical" >
						<caption label="Graphique"/>
						    <label value="Titre"/>
							<textbox persist="value" id="titre" value="Tags en fonction de count"/>
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
			    			<button hidden="false" id="PostTo" label="Mettre à jour del.icio.us" tooltiptext="Met à jour le bookmark collaboratif IEML" onclick="MajFlux();"/>
			     			<button hidden="true" id="bt_10" label="Gérer les traductions"  onclick="Trad('webFrame','Traduction.xul');"/>

					</groupbox>
				</vbox>
				
				<vbox flex="1">
					<groupbox flex="3" orient="horizontal" >
						<caption label="Visualisation des graphiques"/>
						<groupbox flex="1" >
							<caption label="del.icio.us"/>
							<iframe id="webFrame" flex="1" src="library/CreaPapiDyna.php"  />
						</groupbox>
						<groupbox flex="1" >
							<caption label="IEML"/>
							<iframe id="iemlFrame" flex="1" src="library/CreaPapiDyna.php"  />
						</groupbox>
					</groupbox>
			        <splitter collapse="after" resizeafter="farthest">
						<grippy/>
					</splitter>
					<groupbox flex="1" orient="horizontal" >
						<caption label="Visualisation des données"/>
						<iframe  id="treeReq" flex="1" />
						<vbox id="infosTrad" hidden="true"  >
							<groupbox  >
								<caption label="Langage du flux"/>
							    <label id="id-trad-flux" hidden="true"/>
								<label value="code :"/><label id="code-trad-flux" style="background-color:yellow" />
							    <label value="descriptif : "/><label id="lib-trad-flux" style="background-color:yellow" />
							</groupbox>
							<groupbox >
								<caption label="Langage IEML"/>
							    <label id="id-trad-ieml" hidden="true"/>
								<label value="code :"/><label id="code-trad-ieml" style="background-color:yellow" />
								<label value="descriptif : "/><label id="lib-trad-ieml" style="background-color:yellow" />
							</groupbox>
							<groupbox >
								<caption label="Actions de traduction"/>
								<button label="Ajouter" oncommand="AddTrad();"/>	
								<button label="Supprimer" oncommand="SupTrad();"/>
								<label id="trad-Sup-message" />			
								<label id="trad-message" />
								<label id="trad-Sup-message" />			
								<label id="trad-message" />
							</groupbox>				
						</vbox>
						<box id="contDonnee" flex="1" hidden="true" >
						<tabbox flex="1" >
						    <tabs >
						        <tab label="Tags traduits" />
						        <tab label="Tags avec plusieurs traduction" />
						        <tab label="Tags sans traduction" />
						    </tabs>
						    <tabpanels flex="1">
						        <tabpanel >
									<box id="tpSingleTrad" flex="1" />
						         </tabpanel>
						        <tabpanel>
									<box id="tpMultiTrad" flex="1" />
						         </tabpanel>
						        <tabpanel>
									<box id="tpNoTrad" flex="1" />
						         </tabpanel>
						    </tabpanels>
						</tabbox>
						</box>
						<vbox id="treeDicoIeml" flex="1" hidden="true" />					
					</groupbox>
				</vbox>
			</groupbox>
		</vbox> 
 </hbox>
 <script type="text/javascript">
 	//récupération des flux
    RecupDeliciousFlux();
 	
 </script>
 
	<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	var pageTracker = _gat._getTracker("UA-3573757-2");
	pageTracker._initData();
	pageTracker._trackPageview();
	</script>
 
</window>

