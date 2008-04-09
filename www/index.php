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
		var xmlFlux;
	</script>
    <label value="<?php if($con==1){
							echo 'Connection to del.icio.us failed.';
                         }elseif($con==3){
		             		
		             			echo 'Del.icio.us API access throttled.' ;
		             	 }else{  
			         	 	echo'traduction, semantique, ieml, delicious .....'; 
			            }
			           ?>"/>

	<label value="logout" onclick="window.location.replace('exit.php') ; " style=" margin-left:1200px"/>
	<hbox id="histogramme" flex="1">
		<vbox hidden="true">
		   <groupbox>
				<caption label="del.icio.us"/>
				<groupbox orient="vertical">
					<caption label="Login"/>
					<label value="login"/>
					<textbox id="login" value=""/>
					<label value=""/>
					<textbox id="pwd" value="" type="password"/>
				</groupbox>
		   </groupbox>
		  
	   </vbox>
	   <vbox flex="1" >

		 <groupbox orient="horizontal">
			<caption label="del.icio.us"/>
				<groupbox orient="horizontal" >
					<caption label="Graphique"/>
						<vbox>
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
					    <label value="Titre"/>
						<textbox persist="value" id="titre" value="Traduction du Flux"/>
						<label value="Type "/>
						<menulist id="type"  >
							<menupopup>
								<menuitem label="Tags en fonction des bundles" value="tagsFbundles"/>
								<menuitem label="Tags en fonction de count" value="GetAllTags"/>
							</menupopup>
						</menulist>
					    <button id="RecupFlux" label="Affichage du graphique"  onclick="RecupDeliciousFlux()"/>
						</vbox>
				</groupbox>
				<iframe id="webFrame" flex="1" src="http://www.google.fr "  />
				<iframe id="treeReq" flex="1" src="http://www.google.fr"/>
		</groupbox>
		<splitter collapse="before" resizeafter="farthest">
							<grippy/>
		</splitter>
		<groupbox orient="vertical" flex="1">
			<caption label="IEML"/>
			<hbox>
     			<button id="bt_10" label="Traduction de TAG"  onclick="Trad('traduction','traduction.xul');"/>
    			<button id="TypeGraphe" label="Affichage du graphique" tooltiptext="Voir l'histogramme" onclick="Trad_Pars_Ieml();"/>
			</hbox>
			<hbox flex="1">
				<iframe id="iemlhisto" flex="1" src="http://www.ieml.org"  />
				<splitter collapse="before" resizeafter="farthest">
							<grippy/>
				</splitter>
				<hbox id="traduction" flex="1"/>
			</hbox>
		</groupbox>

	</vbox>
     
 </hbox>
 <script type="text/javascript">
 	//récupération des flux
 	SetDonnee();
 </script>
</window>

