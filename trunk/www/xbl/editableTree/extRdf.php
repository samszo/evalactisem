<?xml version="1.0" encoding="UTF-8" ?>
<?xml-stylesheet rel="stylesheet" href="xbl/editableTree/demo.css" type="text/css" title="css"?>

<!--
  * (c) Edutice 2007 http://www.edutice.fr
  * Author : vbe <v.bataille@novatice.com>
   * Version : 00.00.01
   * Description : demo of editable tree
   * Note : Edutice is a brand of Novatice Technologies SAS
 -->

<window id="demoTree" onload="init();"
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"
        xmlns:html="http://www.w3.org/1999/xhtml">
    
    <script type="text/javascript" src="xbl/editableTree/functions.js" />

    <popupset>
        <tooltip id="tipBadValue" onclick="this.hidePopup( );">
            <vbox>
                <label value="Valeur incorrecte !!"/>
            </vbox>
        </tooltip>
    </popupset>

    <spacer height="10"/>
    <description>
        L'arbre suivant dispose de toutes les fonctionnalités proposées par <html:a href="editableTree.xml">ce binding</html:a>.
    </description>
    <spacer height="10"/>
    
    <hbox pack="center">
        <vbox class="editableTree">
            <tree id="treeRing"
                    width="600" height="180"
                    enableColumnDrag="true"
                    datasources="xbl/editableTree/ring.rdf"
                    ref="urn:data:ring"
                    fctStart="startEditable"
                    fctSave="saveEditable"
                    fctInsert="startInsert"
                    fctDelete="startDelete"
											>
    
                <treecols>
                    <treecol id="id" hidden="true" ignoreincolumnpicker="true"/>
    
                    <treecol id="per_name" label="Nom" flex="1"
                                sortDirection="ascending"
                                ondblclick="sortOnColumn(event, 'http://ring/rdf#per_name');"/>
                    <splitter class="tree-splitter" />
    
                    <treecol id="per_firstname" label="Prénom" flex="1" />
                    <splitter class="tree-splitter" />
    
                    <treecol id="per_race" label="Race" flex="1" />
                    <splitter class="tree-splitter" />
    
                    <treecol id="per_community" label="Communauté" cycler="true"
                                width="65"
                                ondblclick="sortOnColumn(event, 'http://ring/rdf#per_community');"/>
                    <splitter class="tree-splitter" />
    
                    <treecol id="per_ring" label="Porteur"
                                width="65" cycler="true" fixed="true"/>
                    <splitter class="tree-splitter" />
    
                    <treecol id="per_actor_name" label="Acteur" flex="1"
                            ondblclick="sortOnColumn(event, 'http://ring/rdf#per_actor_name');"/>
                    <splitter class="tree-splitter" />
    
                    <treecol id="per_actor_birth" label="Naissance" width="80" fixed="true"/>
                </treecols>
    
                <template>
                    <treechildren>
                        <treeitem uri="rdf:*"><treerow>
                                <treecell label="rdf:http://ring/rdf#per_id"/>
                                <treecell label="rdf:http://ring/rdf#per_name"/>
                                <treecell label="rdf:http://ring/rdf#per_firstname"/>
                                <treecell label="rdf:http://ring/rdf#per_race"/>
                                <treecell properties="rdf:http://ring/rdf#per_community"/>
                                <treecell properties="rdf:http://ring/rdf#per_ring"/>
                                <treecell label="rdf:http://ring/rdf#per_actor_name"/>
                                <treecell label="rdf:http://ring/rdf#per_actor_birth"/>
                        </treerow></treeitem>
                    </treechildren>
                </template>
            </tree>
        </vbox>
        <vbox>
			<button label="Delete" id="supprimer" oncommand="ModifRdf();"/>
	</vbox>
    </hbox>
    
    <spacer height="20"/>
    <description>
        <label value="Vous pouvez :" style="color: #ff8a00; font-weight: bold;"/><html:br/>
        ¤ sélectionner une cellule à la souris (simple clic) ou avec les flèches de direction.<html:br/>
        ¤ éditer une cellule avec la souris (double clic) ou avec la touche 'entrée'.<html:br/>
        ¤ la touche 'echap' permet d'annuler l'édition (pour les listes et les champs texte).<html:br/>
        ¤ trier les données en double cliquant sur l'entête de colonne (pour les colonnes Nom, Communauté et Acteur). <html:br/><html:br/>
        
        <label value="Sont disponibles :" style="color: #ff8a00; font-weight: bold;"/><html:br/>
        ¤ l'édition de la colonne 'Race' par une liste déroulante.<html:br/>
        ¤ l'édition de la colonne 'Communauté' par une case à cocher (la touche shift permet de modifier toute la colonne).<html:br/>
        ¤ l'édition de la colonne 'Porteur' par un bouton radio (une seule ligne peut être sélectionnée).<html:br/>
        ¤ l'édition de la colonne 'Acteur' par une liste alimentée par du rdf (à titre d'exemple de code uniquement, la liste n'est pas effectivement alimentée).<html:br/>
        ¤ l'édition de la colonne 'Naissance' par un champ texte n'acceptant que les années (4 chiffres ou rien).<html:br/><html:br/>
        
        ¤ le redimensionnement, l'affichage et le déplacement des colonnes.<html:br/><html:br/>

        <label value="Les sources :" style="color: #ff8a00; font-weight: bold;"/><html:br/>
        <html:a href="editableTree.xml">Binding d'un arbre éditable</html:a> <spacer width="40"/>
        <html:a href="demoEditableTree.xul">Fichier XUL</html:a> <spacer width="10"/> <html:a href="demo.css">Fichier CSS</html:a><spacer width="10"/> 
        <html:a href="js/functions.js">Fichier javascript</html:a> <spacer width="10"/> <html:a href="ring.rdf">Fichier RDF</html:a>
    </description>
    

</window>
