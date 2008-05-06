<?php
require_once ("../param/ParamPage.php");
	session_start();
	$idacteur=$_SESSION['iduti'];
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']";
	$ds = $objSite->XmlParam->GetElements($Xpath);
	
	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdfDesc";
	$Desc = $objSite->XmlParam->GetElements($Xpath);
	
	//param des lignes rdf
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdf";
	$Rdfs = $objSite->XmlParam->GetElements($Xpath);	
	
	
    header('Content-type: application/vnd.mozilla.xul+xml');
	echo '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
?>
<overlay id="tabletrad" >
	<box id="<?php echo $objSite->scope["box"]; ?>" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
		<listbox id="boxlist" onselect="startSelectTab();" >
			
			<listhead >
				<?php
					$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdf";
					$Rdfs = $objSite->XmlParam->GetElements($Xpath);
					foreach($Rdfs as $Rdf){
						echo'<listheader label="'.$Rdf["tag"].'"></listheader>';
					}
			    ?>
			</listhead>
			<listcols>
				<listcol flex="1"></listcol>
				<listcol flex="1"></listcol>
				<listcol flex="1"></listcol>
				<listcol flex="1"></listcol>
			</listcols>
		
		<?php
			$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='ieml-flux']";
			$Q = $objSite->XmlParam->GetElements($Xpath);
			$where=str_replace("-idacteur-",$idacteur,$Q[0]->where);
			$sql = $Q[0]->select.$Q[0]->from.$where;
		    //echo $sql."<br/>"; 

			$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$req = $db->query($sql);
			$db->close();
			$nb = mysql_num_rows($req);

			while($r = mysql_fetch_assoc($req))
			{
				echo('<listitem>');
				echo('<listcell label="'.$r["ieml_id"].'"/>');
				echo('<listcell label="'.$r["onto_flux_id"].'"/>');
				echo('<listcell label="'.$r["ieml_code"].'"/>');
				echo('<listcell label="'.$r["ieml_lib"].'"/>');
				echo('</listitem>');
			}		    			    
		
		
		?>
		</listbox>
	</box>
</overlay>