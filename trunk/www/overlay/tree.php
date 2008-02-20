<?php
require_once ("../param/ParamPage.php");
	//adresse de la datasource
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']";
	$ds = $objSite->XmlParam->GetElements($Xpath);
	//echo $ds[0]["datasource"];
	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdfDesc";
	$Desc = $objSite->XmlParam->GetElements($Xpath);
	//print_r($Desc);
	//param des lignes rdf
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdf";
	$Rdfs = $objSite->XmlParam->GetElements($Xpath);	
	//print_r($Rdfs);

    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>

<overlay id="oTree"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

    <popupset>
        <tooltip id="tipBadValue" onclick="this.hidePopup( );">
            <vbox>
                <label value="Valeur incorrecte !!"/>
            </vbox>
        </tooltip>
    </popupset>
	<box id="<?php echo $objSite->scope["box"]; ?>"  class="editableTree" >
		<tree id="treeRing"
				width="600" height="600"
				context="clipmenu"			
				enableColumnDrag="true"
				fctStart="startEditable"
				fctSave="saveEditable"
				fctInsert="startInsert"
				fctDelete="startDelete"
				fctSelect="startSelect"
			typesource="<?php echo $type;?>"	
			ref="urn:roots" 
			datasources="<?php echo $ds[0]["datasource"]."?type=".$type;?>"
		 >
				<treecols>
				<?php
					//le conteneur doit avoir comme id id pour editableTree
					echo('<treecol id="id" label="branche" primary="true" flex="1" cycler="true" sort="rdf:http://'.$Desc[0]["urn"].'/rdf#'.$Desc[0]["tag"].'"/>');
					echo('<splitter class="tree-splitter"/>');
					foreach($Rdfs as $Rdf)
					{
						echo('<treecol id="treecol_'.$Rdf["tag"].'" label="'.$Rdf["tag"].'"> <col tag="id" parse="Integer" hidden="true"/>
			<col tag="lib " hidden="false"/>
			<col tag="niveau" hidden="false"/>
			<col tag="type" hidden="true"/>'.$Rdf["hidden"].'"/>');
						echo('<splitter class="tree-splitter"/>');
					}
				?>
				</treecols>
				<template>
					<rule>
					<?php
						echo('<treechildren  id="rdf:http://'.$Desc[0]["urn"].'/rdf#'.$Desc[0]["tag"].'">');
						echo('<treeitem uri="rdf:*">');
						echo('<treerow id="rdf:http://'.$Desc[0]["urn"].'/rdf#'.$Desc[0]["tag"].'">');
						//ajout d'une cellule pour la branche
						echo('<treecell label="rdf:http://'.$Desc[0]["urn"].'/rdf#'.$Desc[0]["tag"].'"/>');
						foreach($Rdfs as $Rdf)
						{
							echo('<treecell label="rdf:http://'.$Desc[0]["urn"].'/rdf#'.$Rdf["tag"].'"/>');
						}
						echo('</treerow>');
						echo('</treeitem>');
						echo('</treechildren>');
					?>
					</rule>
				</template>
			</tree>
	</box>
</overlay>