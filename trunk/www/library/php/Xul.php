<?php
set_time_limit(300);

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
	        $this->trace = TRACE;
	
	
	    $this->site = $site;
	    $this->id = $id;
	        
	        
	        if($complet){
	        }
	
	
	        //echo "FIN new grille <br/>";
   }
        
   //Construction de la table des Tags qui n'ont pas de traduction dans le dictionnaire
   function GetTreeNoTradUti($idUti){
	
	$sem = new Sem($this->site,$this->site->scope["FicXml"],"");
	$type = "No_Trad";
	
	//construction de l'entête du tree
	$ihm .= '<tree                  
		enableColumnDrag="true"
        typesource="GetTreeNoTradUti"
        flex="1"        
        id="'.$type.'" 
        multiple="true"';
	$ihm .= ' onselect="Select_NoTrad(\''.$type.'\',\'treecol_Tagdel\');">'.EOL; 
	$ihm .= '<treecols >'.EOL;
	$ihm .= '<treecol hidden="false" flex="1" label="Traductions"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
	$ihm .= '<treecol id="treecol_Tagdel" flex="2"  primary="true" label="Tag delicious"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_descp" flex="2" label="Couche IEML"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_ieml" flex="2" label="'.utf8_encode('Libellé IEML').'"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_'.$type.'" flex="1"  label="IEML"  persist="width ordinal hidden" />'.EOL;
	$ihm .= '</treecols>'.EOL;
	$ihm .= '<treechildren >'.EOL;
    
    
	//récupère les tags non traduits
	$rs = $sem->RequeteSelect($this->site,'GetTreeNoTradUti','-idUti-','',$idUti,'');
	$i=0;
	//construction des tag non traduit de l'utilisateur 
    $ihmNo = '<treeitem id="NoTradUti_'.$idUti.'" container="true" open="true">'.EOL;
	$ihmNo .= '<treerow>'.EOL;
	$ihmNo .= '<treecell   label="'.utf8_encode("Non trouvé(s)").'"/>'.EOL ;
	$ihmNo .= '<treecell   label=""/>'.EOL ;
	$ihmNo .= '<treecell   label=""/>'.EOL ;
	$ihmNo .= '<treecell   label=""/>'.EOL ;
	$ihmNo .= '</treerow>'.EOL;
	$ihmNo .= '<treechildren>'.EOL;
	while($r = mysql_fetch_assoc($rs)){
        $ihmNo .= $this->AddTreeItemTrad($type.'_'.$r["onto_flux_id"].'_'.$idUti,"",array("",utf8_encode($r["onto_flux_code"]),"",""));
	}
	//termine le treedes non trouvé
	$ihmNo .= '</treechildren>'.EOL;
    $ihmNo .= '</treeitem>'.EOL;

	//récupère les tags non traduits
	$rs = $sem->RequeteSelect($this->site,'GetTreeTradAutoSupUti','-idUti-','-idUtiAuto-',$idUti,$this->site->infos["UTI_TRAD_AUTO"]);
	$i=0;
	//construction des tag non traduit de l'utilisateur 
    $ihmSup = '<treeitem id="NoTrad_Auto_'.$idUti.'" container="true" open="true">'.EOL;
	$ihmSup .= '<treerow>'.EOL;
	$ihmSup .= '<treecell label="'.utf8_encode("Trad. Automatique supprimé(s)").'"/>'.EOL ;
	$ihmSup .= '<treecell label=""/>'.EOL ;
	$ihmSup .= '<treecell label=""/>'.EOL ;
	$ihmSup .= '<treecell label=""/>'.EOL ;
	$ihmSup .= '</treerow>'.EOL;
	$ihmSup .= '<treechildren>'.EOL;
	while($r = mysql_fetch_assoc($rs)){
        $ihmSup .= $this->AddTreeItemTrad($type.'_'.$r["onto_flux_id"].'_'.$r["ieml_code"],"",array("",utf8_encode($r["onto_flux_code"]),$r["ieml_parent"],utf8_encode($r["ieml_lib"]),$r["ieml_code"]));
	}
	//termine le treedes non trouvé
	$ihmSup .= '</treechildren>'.EOL;
    $ihmSup .= '</treeitem>'.EOL;
    
    
	//termine le tree
	$ihm .= $ihmNo;
	$ihm .= $ihmSup;
	$ihm .= '</treechildren>'.EOL;
    $ihm .= '</tree>'.EOL;

    return $ihm;    
}
        
//Construction de la table des Traduction de le  utilisateur     
function GetTreeTradUtis($idUtis){
	
	$type = "Signl_Trad";
	
	//construction de l'entête du tree
	$ihm .= '<tree                  
		enableColumnDrag="true"
        typesource="GetTreeTradUtis"
        flex="1"        
        id="'.$type.'" 
        multiple="true"';
    $ihm .= ' onselect="Select_Trad(\''.$type.'\',1,2,3);">'.EOL;
    $ihm .= '<treecols >'.EOL;
	$ihm .= '<treecol hidden="false" flex="1" label="Traductions"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
	$ihm .= '<treecol id="treecol_Tagdel" flex="2"  primary="true" label="Tag delicious"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_descp" flex="2" label="'.utf8_encode('Libellé IEML').'"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_'.$type.'" flex="1"  label="IEML"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '</treecols>'.EOL;
    $ihm .= '<treechildren >'.EOL;

	foreach($idUtis as $idUti){
		$ihm .= $this->GetTreeItemTradUti($idUti,$type);	
	}
    
	//termine le tree
	$ihm .= '</treechildren>'.EOL;
    $ihm .= '</tree>'.EOL;

    return $ihm;    
	
        
}

function GetTreeItemTradUti($idUti,$type){

	$sem = new Sem($this->site,$this->site->scope["FicXml"],"");
	
	//récupère les traductions 
	if($idUti==$this->site->infos["UTI_TRAD_AUTO"]){
		//des traduction automatiques partagées par l'utilisateur 
		$rs = $sem->RequeteSelect($this->site,'GetTreeTradUtiAuto','-idUti-','-idUtiAuto-',$_SESSION['iduti'],$idUti);
	}else{
		//de l'utilisateur 
		$rs = $sem->RequeteSelect($this->site,'GetTreeTradUti','-idUti-','--',$idUti,"");
	}
	$i=0;
   	while($r = mysql_fetch_assoc($rs)){
       		
   		//pour gérer le changement de branche parente
       	if($r["uti_id"]!=$oIdUti){
       		$oFluxCode = -1;
       	}
   		if($r["onto_flux_code"]!=$oFluxCode){
   			$oIemlNiv = -1;
   		}
   		
       	//on crée les couches
       	if($r["ieml_niveau"]!=$oIemlNiv){
       		if($i>0){
	       		//on ferme le précédent tag
				$ihmNiv .= $ihmIeml;
	       		$ihmNiv .= '</treechildren>'.EOL;
				$ihmNiv .= '</treeitem>'.EOL;
				//on créé l'usl
				$usl .= $sem->StarParam["usl"];
				$usl .= "(";
				$usl .= $uslT;
				$usl = substr($usl,0,-1);
				$usl .= ")";
				$ihmNivs .= $ihmNiv;
       		}
       		//on ouvre le nouveau
       		$ihmNiv = '<treeitem id="onto_flux_id_'.$r["onto_flux_id"].'_'.$r["ieml_niveau"].'" container="true" open="true">'.EOL;
			$ihmNiv .= '<treerow>'.EOL;
			$ihmNiv .= '<treecell label=" "/>'.EOL ;
			$ihmNiv .= '<treecell label="'.$r["ieml_parent"].'"/>'.EOL ;
	        $ihmNiv .= '<treecell label=""/>'.EOL ;
            $ihmNiv .= '</treerow>'.EOL;
       		$ihmNiv .= '<treechildren>'.EOL;
	        $ihmIeml = "";
       		$uslT = "";
	        $oIemlNiv=$r["ieml_niveau"];       
       	}

       	//on vérifie si on change de tag
       	if($r["onto_flux_code"]!=$oFluxCode){
       		if($i>0){
	       		//on ferme le précédent tag
	            $ihmTag .= '<treecell label="'.$usl.'"/>'.EOL ;
	            $ihmTag .= '</treerow>'.EOL;
	       		$ihmTag .= '<treechildren>'.EOL;
       			$ihmTag .= $ihmNivs;
	       		$ihmTag .= '</treechildren>'.EOL;
				$ihmTag .= '</treeitem>'.EOL;
				$ihmTags .= $ihmTag;
       		}
       		//on ouvre le nouveau
			$usl = "";
			$ihmNivs = ""; 
       		$ihmTag = '<treeitem id="onto_flux_id_'.$r["onto_flux_id"].'" container="true" open="true">'.EOL;
			$ihmTag .= '<treerow>'.EOL;
			$ihmTag .= '<treecell label=" "/>'.EOL ;
			$ihmTag .= '<treecell label="'.utf8_encode($r["onto_flux_code"]).'"/>'.EOL ;
	        $ihmTag .= '<treecell label=""/>'.EOL ;
	        $oFluxCode=$r["onto_flux_code"];       
       	}
       	
		//vérifie si on change d'utilisateur
       	if($r["uti_id"]!=$oIdUti){
       		if($i>0){
	       		//on ferme le précédent utilisateur
				$ihm .= $ihmTags;
	       		$ihm .= '</treechildren>'.EOL;
				$ihm .= '</treeitem>'.EOL;
       		}
       		//on ouvre le nouveau
          	$ihm .= '<treeitem  properties="HelpUti" container="true" open="true">'.EOL;
          	$ihm .= '<treerow >'.EOL;
	        if($r["uti_id"]==$this->site->infos["UTI_TRAD_AUTO"])
    	    	$ihm .= '<treecell properties="HelpUti" label="Automatiques" />'.EOL ;
        	else
          		$ihm .= '<treecell properties="HelpUti" label="'.$r["uti_login"].'" />'.EOL ;
          	$ihm .= '</treerow>'.EOL;
          	$ihm .= '<treechildren>'.EOL;
			$oIdUti=$r["uti_id"];
       	}
        
       	//on crée les traductions
        $ihmIeml .= $this->AddTreeItemTrad($type.'_'.$r["onto_flux_id"].'_'.$r["ieml_id"],"",array("","",utf8_encode($r["ieml_lib"]),$r["ieml_code"]));
		//on crée l'usl
		$uslT .= $r["ieml_code"].$sem->StarParam["union"];
       	
        $i++;
   	}
    
   	//vérifie s'il existe des traductions
   	if($i==0)
   		return "";
   		
   	//on ferme le précédent tag
	$ihmNiv .= $ihmIeml;
    $ihmNiv .= '</treechildren>'.EOL;
	$ihmNiv .= '</treeitem>'.EOL;
	//on créé l'usl
	$usl .= $sem->StarParam["usl"];
	$usl .= "(";
	$usl .= $uslT;
	$usl = substr($usl,0,-1);
	$usl .= ")";
	$ihmNivs .= $ihmNiv;
	
    //on ferme le précédent tag
    $ihmTag .= '<treecell label="'.$usl.'"/>'.EOL ;
    $ihmTag .= '</treerow>'.EOL;
	$ihmTag .= '<treechildren>'.EOL;
	$ihmTag .= $ihmNivs;
    $ihmTag .= '</treechildren>'.EOL;
	$ihmTag .= '</treeitem>'.EOL;
	$ihmTags .= $ihmTag;
	
   	//termine le dernier utilisateur
   	$ihm .= $ihmTags;
	$ihm .= '</treechildren>'.EOL;
	$ihm .= '</treeitem>'.EOL;
	
	return $ihm;
}

	function AddTreeItemTrad($id, $style, $cells){
		
		$item = '<treeitem id="'.$id.'" >'.EOL;  
        $item .= '<treerow id="TreeRow_'.$id.'" '.$style.' >'.EOL;
        foreach($cells as $cell){
        	$item .= '<treecell label="'.$cell.'"  />'.EOL ;      	
        }
        $item .= '</treerow>'.EOL;
        $item .= '</treeitem>'.EOL;
		
        return($item);
		
	}
       // function pur la construction de dictionnaire IEML
       //<-----------------------------------------------------------------------------------
       function GetTreeIemlOnto($type){
                        
        //adresse de la datasource
                $label="Dictionnaire Ieml";
                $Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']";
                        $ds = $this->site->XmlParam->GetElements($Xpath);

                if($this->trace)
               		 echo "Xul:GetTree_ieml_onto:Cols".print_r($Cols)."<br/>";
                $tree='<vbox flex="1" style="background-color:yellow;" >'.EOL;
                $tree.='<label value="'.$label.'" style="font:arial;size:10;color:blue"  />'.EOL;
                $tree.='<box id="'.$this->site->scope["box"].'" flex="1"  class="editableTree" >'.EOL;
                 $tree.='<tree id="'.$type.'"
                      flex="1"
                      style="width:600; height:400"
                      onselect="Select_Dictio(\''.$type.'\',\'treecol_ieml\',\'treecol_descp\');"
                      typesource="'.$type.'"  
                      Treeid="'.$type.'">'.EOL;
                      //le conteneur doit avoir comme id id pour editableTree
                       $tree.= '<treecols >'.EOL;
                             $tree.= '<treecol id="treecol_Tagdel"  primary="true" label="Tag Delicious"  persist="width ordinal hidden" />'.EOL;
                             $tree.= '<splitter class="tree-splitter"/>'.EOL;
                             $tree.= '<treecol id="treecol_descp"  label="Description"  persist="width ordinal hidden" />'.EOL;
                             $tree.= '<splitter class="tree-splitter"/>'.EOL;
                             $tree.= '<treecol id="treecol_'.$type.'"  label="Traduction"  persist="width ordinal hidden" />'.EOL;
                       $tree.= '</treecols>'.EOL;  
                       $Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='get_hierarchie_Dictio']";
                       $Q = $this->site->XmlParam->GetElements($Xpath);
                       $sql = $Q[0]->select.$Q[0]->from;
                       $db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
                       $db->connect();
                       $req = $db->query($sql);
                       $db->close();
                       $tree .= '<treechildren>'.EOL;
                          $tree.= '<treeitem container="true" open="false">'.EOL;
                                $tree.= '<treerow>'.EOL;
                                         $tree.= '<treecell label="Dictionnaire ieml"/>' .EOL;
                                 $tree.= '</treerow>'.EOL;
                                 $tree .= '<treechildren>'.EOL;
                                   while($reponse=mysql_fetch_array($req)){
                                     $tree.= '<treeitem container="true" open="false">'.EOL;
                                           $tree.= '<treerow>'.EOL;
                                                 $tree.= '<treecell label="'.$reponse[0].'"/>' .EOL;
                                            $tree.= '</treerow>'.EOL;
                                            $tree.= $this->GetTreeChildrenDictio($reponse[0],$reponse[1]);
                                      $tree.= '</treeitem>'.EOL;


                                        }
                                   $tree .= '</treechildren>'.EOL;
                            $tree.= '</treeitem>'.EOL;
                      $tree .= '</treechildren>'.EOL;
                 $tree.='</tree>'.EOL;
              $tree.='</box>'.EOL;
            $tree.='</vbox>'.EOL;
            if($this->trace)
            echo "Xul:GetTree_ieml_onto:tree". $tree."<br/>";      
            return $tree;
       }
        
   	   function GetTreeChildrenDictio($parent,$id){
        $container="false";
                
        $Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='get_hierarchie_Dictio_children']";
                $Q = $this->site->XmlParam->GetElements($Xpath);
                $from = str_replace("-parent-", $id, $Q[0]->from);
                $sql = $Q[0]->select.$from;
                $db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $req = $db->query($sql);
                $db->close();
                 $tree .= '<treechildren>'.EOL;
                while($r = mysql_fetch_array($req))
                {  
                        $tree.= '<treeitem >'.EOL;
                                $tree.= '<treerow>'.EOL;
                                        $tree.= '<treecell label=""/>'.EOL ;    
                        $tree.= '<treecell  label="'.$r[0].'" />'.EOL ; 
                        $tree.= '<treecell  label="'.$r[1].'"/>'.EOL ;
                        $tree.= '</treerow>'.EOL;
                $tree.= '</treeitem>'.EOL;      
                                                                        
                }
                 $tree .= '</treechildren>'.EOL;
                if($this->trace)
             		echo "Xul:GetTreeChildren:tree". $tree."<br/>";  
                return $tree;
   	   }  
   	   //------------------------------------------------------------------>     
 }
?>
