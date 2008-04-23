<?php
session_start();
class Xul{
  public $id;
  public $XmlParam;
  public $trace;
  private $site;
 
    function __tostring() {
    return "Cette classe permet de définir et manipuler des grilles.<br/>";
    }

    function __construct($site, $id=-1, $complet=true) {
	//echo "new Site $sites, $id, $scope<br/>";
	print_r($this->Xul);
  	$this->trace = false;

    $this->site = $site;
    $this->id = $id;
	
	
	if($complet){
	}

	//echo "FIN new grille <br/>";
		
    }
	
	function GetTab($src, $id, $dst="Rub", $recur = false){


		//chaque ligne est un onglet
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabForm".$dst."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "GetXulTab ".$dst." ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();

		//initialisation de la tabbox
		$tabbox = '<tabbox  id="tabbox_'.$src.'_'.$dst.'_'.$id.'">';
		$tabbox .= '<tabs>';
		$i=0;
		while ($r =  $db->fetch_assoc($result)) {
			$tabbox .= '<tab id="tab'.$r["id"].'" label="'.$r["titre"].'" />';
			if($Q[0]->dst=='Form')
				$tabpanels .= $this->GetXulTabPanels($r["idArt"], $r["id"],'Form',$recur);
			else
				$tabpanels .= $this->GetXulTabPanels($src, $r["id"],$Q[0]->dst,$recur);
			$i++;
		}
		
		if($i!=0){
			$tabbox .= '</tabs>';
			$tabbox .= '<tabpanels>';
			$tabbox .= $tabpanels;
			$tabbox .= '</tabpanels>';
			$tabbox .= '</tabbox>';
		}else
			$tabbox = "";
			
		return $tabbox;
		
	}


	function GetTabPanels($src, $id, $dst="Rub", $recur = false){

		//récupère les articles de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabPanels".$dst."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$where = str_replace("-src-", $src, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetXulTabPanels ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();

		//initialisation du panel
		$tabpanel = '<tabpanel id="tabpanel_'.$src.'_'.$dst.'_'.$id.'">';	
		
		//ajoute les onglets des sous rubriques
		if($recur)
			$tabpanel .= $this->GetXulTab($src, $id, $dst, $recur);
		
		//ajoute les groupbox pour chaque article
		while($r = $db->fetch_assoc($req)) {
			//$tabpanel .= '<groupbox >';	
			//$tabpanel .= '<caption label="'.$r["titre"].'"/>';
			if($Q[0]->dst=='Form')
				//ajoute les données de chaque article
				$tabpanel .= $this->GetXulForm($r["id"], $id);
			else
				//ajoute la tabbox de destination
				$tabpanel .= $this->GetXulTab($src, $r["id"], $Q[0]->dst, $recur);	
		}
		$tabpanel .= '</tabpanel>';

		return $tabpanel;
	}

			
    function GetForm($idDon, $idGrille) {
  
  
		//requête pour récupérer les données de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-idGrille-", $idGrille, $where);
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetXulForm ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		//ajoute les controls pour chaque grille
		$form = '<hbox flex="1">';	
		$form .= '<groupbox >';	
		$form .= '<caption label="Donnée : '.$idDon.'"/>';
		while($r = $db->fetch_assoc($req)) {
			$idDoc = 'val'.DELIM.$r["id_donnee"].DELIM.$r["champ"];
			switch ($idGrille) {
				case $this->site->infos["GRILLE_REG_LEG"]:
					//construstion de la règle législative
					$form .= $this->GetXulRegLeg($idDoc, $r);
					break;
				default:
					$form .= $this->GetXulControl($idDoc, $r);
			}
		}
		$form .= '</groupbox>';
		if($idGrille == $this->site->infos["GRILLE_GEO"]){
			$form .= '<groupbox >';	
			$form .= '<caption label="Cartographie"/>';
			//ajoute la carte
			$form .= $this->GetXulCarto($idDon);
			$form .= '</groupbox>';
		}
		
		$form .= '</hbox>';	

		return $form;
	
	}
	
	function GetCarto($idDon)
	{
	
		return	"<iframe height='550px' width='450px' src='http://www.mundilogiweb.com/onadabase/design/BlocCarte.php?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
	
	
	}

	function GetRegLeg($id, $row)
	{
		
		/*résultat de row
		champ 	rang 	titre 	type 	obligatoire 	extra_info 	
		ligne_1 	6 	valeur étalon 	ligne 	  	  	  	  	  	 
		ligne_2 	7 	valeur étalon 2 	ligne 	  	  	  	  	  	 
		ligne_3 	4 	Nom de la valeur 	ligne 	  	  	  	  	  	 
		mot_1 	5 	opérateur 		mot 	18 	  	  	  	 
		mot_2 	8 	Unités 		mot 	19 	  	  	  	 
		select_1 	9 	règle respectée 	select radio		
		*/
		
		switch ($row['champ']) {
			case 'ligne_1':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_2':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value="" width="200"/>';
				break;
			case 'ligne_3':
				//construction du control
				$control = '<label value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				break;
			case 'mot_1':
				$mot = New MotClef($row["valeur"], $this->site);
				$control = '<label value="'.$this->site->XmlParam->XML_entities($mot->titre).'"/>';			
				break;
			case 'mot_2':
				$mot = New MotClef($row["valeur"], $this->site);
				$control = '<label value="'.$this->site->XmlParam->XML_entities($mot->titre).'"/>';			
				break;
			case 'select_1':
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<groupbox>';
				$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '<label id="trace'.$id.'" value=""/>';
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				break;
		}

		return $control;
	
	}
	
	function GetControl($id, $row)
	{
		$control = '';
		switch ($row['type']) {
			case 'select':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<groupbox>';
				$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				break;
			case 'mot':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<groupbox>';
				$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				break;
			default:
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				$control .= '<label value="'.$this->site->XmlParam->XML_entities($row["titre"]).'"/>';			
				$control .= '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				
		}
		
		$control .= '<label id="trace'.$id.'" value=""/>';

		return $control;

	}

	function GetChoixVal($row)
	{
		//requête pour récupérer les données de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChoix".$row['type']."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $row['grille'], $Q[0]->where);
		$where = str_replace("-champ-", $row['champ'], $where);
		$where = str_replace("-extra_info-", $row['extra_info'], $where);
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetChoixVal ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();

		$control = "";
		while($r = $db->fetch_assoc($req)) {
			$select = 'false';
			if($row['valeur']==$r['choix'])
				$select = 'true';
			if($this->trace)
				echo "select ".$select." ".$row['valeur']."==".$r['choix']."<br/>";
			$control .= "<radio id='".$r['choix']."' selected='".$select."' label='".$this->site->XmlParam->XML_entities($r["titre"])."'/>";
		}
		
		return $control;

	}
	
	
	function GetTreeChildren($type, $Cols=-2, $id=-2){

		
		if($Cols==-2){
			$Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/Cols/col";
			$Cols = $this->site->XmlParam->GetElements($Xpath);
			//print_r($Cols);
		}
		
		$Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		if($id==-2){
			
			//récupère la valeur par defaut
			$Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/from";
			$attrs =$this->site->XmlParam->GetElements($Xpath);
			
			
			if($attrs[0]["niv"])
				$id = $attrs[0]["niv"];
			
		}
		
		$from = str_replace("-parent-", $id, $Q[0]->from);
		if($type=="flux"){
			$from = str_replace("-iduti-", $_SESSION['iduti'], $from);
		}
		$sql = $Q[0]->select.$from;
		
		
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$nb = mysql_num_rows($req);

		$hierEnfant = "";
		$tree = '<treechildren >'.EOL;
		while($r = mysql_fetch_row($req))
		{
			$tree .= '<treeitem id="'.$type.'_'.$r[0].'" container="true" empty="false" >'.EOL;
			$tree .= '<treerow>'.EOL;
			$i= -1;
			foreach($Cols as $Col)
			{
				$tree .= '<treecell label="'.$r[$i].'"/>'.EOL;
				$i ++;
			}
			$tree .= '</treerow>'.EOL;
			$tree .= $this->GetTreeChildren($type, $Cols, $r[0]);
			$tree .= '</treeitem>'.EOL;
		}

		if($nb>0){
		 	
			$tree .= '</treechildren>'.EOL;
		}	
		else{
			
			$tree='';
		   
		}
			
		
		return $tree;

	}
    
    function Tree($flux,$trad,$descp,$type,$primary,$bdd){
    	echo'<vbox  style="background-color:blue;" align="center">';	
    	echo'<label value="'.$type.'" style="font:arial;size:10;color:yellow" />';
    	echo'<box style="height:400px;width:100px;">';
    	echo'<tree context="clipmenu"			
			enableColumnDrag="true"
			fctStart="startEditable"
			fctSave="saveEditable"
			fctInsert="startInsert"
			fctDelete="startDelete"
			fctSelect="startSelect"
			typesource="'.$type.'"	
			id="'.$type.'" ';
			if($type=="No_Trad"){
				echo ' onselect="Select_NoTrad(\''.$type.'\');">'; 
			}else{
				echo ' onselect="Select_Trad(\''.$type.'\');">';
			}
			
    		if($type=="No_Trad"){
    			echo'<treecols >';
	  			 echo'<treecol id="treecol_Tagdel"  primary="'.$primary.'" label="Tag Delicious"  width="120" />';
	  		     echo'<splitter class="tree-splitter"/>';
                 echo'</treecols>';
	  		   echo'<treechildren>';  
	  		   for($i=0;$i<sizeof($flux);$i++){
                    echo'<treeitem >';
                        echo'<treerow>';
                        echo'<treecell label="'.$flux[$i].'"/>' ;
                        echo'</treerow>';
	  		        echo'</treeitem>'; 
	  		   }
	  		   echo'</treechildren>';
	  		   echo'</tree>';
            }else{
	  		echo'<treecols >';
	  			 echo'<treecol id="treecol_Tagdel"  primary="'.$primary.'" label="Tag Delicious"  width="120" />';
	  		     echo'<splitter class="tree-splitter"/>';
	  			 echo'<treecol id="treecol_descp"  label="Description"  width="120" />';
	  			 echo'<splitter class="tree-splitter"/>';
	  			 echo'<treecol id="treecol_'.$type.'"  label="Traduction" width="120" />';
	  		    echo'</treecols>';
	  		echo'<treechildren>';    
                    for($i=0;$i<sizeof($flux);$i++){
                    	echo'<treeitem container="true" open="true">';
                        	 	 echo'<treerow>';
                        	 	 echo'<treecell label="'.$flux[$i].'"/>' ;
                        	  	 echo'</treerow>';
                        	  	 if($type=="Signl_Trad"){
                        	  		//echo $type;
                        	  		echo'<treechildren>';
                        	  			echo'<treeitem >';	
                        	  			  if(in_array($trad[$i],$bdd)){
                        	  			  	$prop="utilisateur";
                        	  			  }else{
                        	  			  	$prop="dictio";
                        	  			  }
                        	  			  echo'<treerow properties="'.$prop.'">';
                        	  					echo'<treecell label=""  />' ;	
                        	  				    echo'<treecell label="'.$descp[$i].'"/>' ;	
                        	  				  	echo'<treecell label="'.$trad[$i].'"/>' ;
                        	  				  echo'</treerow>';
                        	  			echo'</treeitem>';
                        	  		echo'</treechildren>';
                        	  	}
                        	  	if($type=="Multi_Trad"){
                        	  		echo'<treechildren>';
                        	  		for($j=0;$j<sizeof($trad[$i]);$j++){
                        	  			echo'<treeitem >';	
                        	  				echo'<treerow>';
                        	 		 			echo'<treecell label=""/>' ;	
                        	  				    echo'<treecell  label="'.$descp[$i][$j].'" />' ;	
                        	  					echo'<treecell  label="'.$trad[$i][$j].'"/>' ;
                        	  			    echo'</treerow>';
                        	  			echo'</treeitem>';
                        	  		
                        	  		}
                        	  		echo'</treechildren>';
                        	  	}
                        	  	echo'</treeitem>';
                        }
	  					
	  			echo'</treechildren>';
	  		echo'</tree>';
            }
	    echo'</box>';
	  	echo'</vbox>';
      
    }
	
    
    
    function VerifExist_onto_trad($iduti){
    	global $objSite;	
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		        $db->connect();   
                	// requête pour vérifier l'existence de la traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='Tree_dynamique']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from = str_replace("-iduti-", $iduti, $Q[0]->from);
                $sql = $Q[0]->select.$from;
               
                $result = $db->query($sql);
                $db->close();
    			while($reponse=mysql_fetch_array($result)){
    				$Trad.=$reponse[1].";";
    				$Desc.=$reponse[0].";";
    				
    			}
    			
    			return $Trad."*".$Desc;
               
     }
	
	
	
  }
?>