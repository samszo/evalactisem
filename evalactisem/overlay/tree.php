<?php

require_once ("../param/ParamPage.php");

	//adresse de la datasource
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']";
	$ds = $objSite->XmlParam->GetElements($Xpath);
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/Cols/col";
	$Cols = $objSite->XmlParam->GetElements($Xpath);	
		echo '<tree id="tree'.$type.'"
			  context="iemlmenu"			
			   enableColumnDrag="true"
			   flex="1"
			   typesource="'.$type.'"	
			   idTree="tree'.$type.'">';
				echo '<treecols>';
				
					//le conteneur doit avoir comme id id pour editableTree
					echo('<treecol id="id" label="branche" primary="true"  cycler="true"/>');
					echo('<splitter class="tree-splitter"/>');
					
					foreach($Cols as $Col)
					{
						//la première colonne est le bouton pour déplier
							echo('<treecol id="treecol_'.$Col["tag"].'" flex="1" label="'.$Col["tag"].'"  hidden="'.$Col["hidden"].'"/>');
							echo('<splitter class="tree-splitter"/>');
						
						
					}
				
				echo '</treecols>';
			
					
					echo $objXul->GetTreeChildren($type, $Cols);
			
			echo '</tree>';

	

  ?>
