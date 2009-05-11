<?php
class Xul{
  public $id;
  public $XmlParam;
  public $trace;
  private $site;
 
    function __tostring() {
    return "Cette classe permet de d�finir et manipuler des grilles.<br/>";
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
   function Get_Tree_NoTrad_Uti($idUti,$lang){
	
	$sem = new Sem($this->site,$this->site->infos["XML_Param"],"");
	$type = "No_Trad";
	
	//construction de l'ent�te du tree
	$ihm = '<tree                  
		enableColumnDrag="true"
        typesource="GetTreeNoTradUti"
        flex="1"        
        id="'.$type.'" 
        multiple="true"';
	$ihm .= ' onselect="SelectNoTrad(\''.$type.'\',\'treecol_Tagdel\');">'.EOL; 
	$ihm .= '<treecols >'.EOL;
	$ihm .= '<treecol hidden="false" flex="1" label="Traductions"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
	$ihm .= '<treecol id="treecol_Tagdel" flex="2"  primary="true" label="Tag delicious"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_descp" flex="2" label="Couche IEML"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_ieml" flex="2" label="'.'Libellé IEML'.'"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_'.$type.'" flex="1"  label="IEML"  persist="width ordinal hidden" />'.EOL;
	$ihm .= '</treecols>'.EOL;
	$ihm .= '<treechildren >'.EOL;
    
    
	//récupére les tags non traduits
	$rs = $this->site->RequeteSelect('GetTreeNoTradUti',array(array('-idUti-',$idUti)));
	$i=0;
	//construction des tag non traduit de l'utilisateur 
    $ihmNo = '<treeitem id="NoTradUti_'.$idUti.'" container="true" open="true">'.EOL;
	$ihmNo .= '<treerow>'.EOL;
	$ihmNo .= '<treecell   label="'."Non trouvé(s)".'"/>'.EOL ;
	$ihmNo .= '<treecell   label=""/>'.EOL ;
	$ihmNo .= '<treecell   label=""/>'.EOL ;
	$ihmNo .= '<treecell   label=""/>'.EOL ;
	$ihmNo .= '</treerow>'.EOL;
	$ihmNo .= '<treechildren>'.EOL;
	while($r = mysql_fetch_assoc($rs)){
        $ihmNo .= $this->AddTreeItemTrad($type.'_'.$r["onto_flux_id"].'_'.$idUti,"",array("",$r["onto_flux_code"],"",""));
	}
	//termine le treedes non trouvé
	$ihmNo .= '</treechildren>'.EOL;
    $ihmNo .= '</treeitem>'.EOL;

	//récupére les tags non traduits
	$rs = $this->site->RequeteSelect('GetTradAutoSup',array(array('-idUti-',$idUti),array('-idUtiAuto-',$this->site->infos["UTI_TRAD_AUTO"])));
	$i=0;
	//construction des tag non traduit de l'utilisateur 
    $ihmSup = '<treeitem id="NoTrad_Auto_'.$idUti.'" container="true" open="true">'.EOL;
	$ihmSup .= '<treerow>'.EOL;
	$ihmSup .= '<treecell label="'."Trad. Automatique supprimé(s)".'"/>'.EOL ;
	$ihmSup .= '<treecell label=""/>'.EOL ;
	$ihmSup .= '<treecell label=""/>'.EOL ;
	$ihmSup .= '<treecell label=""/>'.EOL ;
	$ihmSup .= '</treerow>'.EOL;
	$ihmSup .= '<treechildren>'.EOL;
	while($r = mysql_fetch_assoc($rs)){
		$oCacheXml = new Cache($this->sUsername."liveMetal", $iCacheTime=10);
		$xml=simplexml_load_string($oCacheXml->Get(true));
		$Xpath = "//entry[@id='".$r['ieml_id']."']";
		$Entrys=$xml->xpath($Xpath);
		$ihmSup .= $this->AddTreeItemTrad($type.'_'.$r["onto_flux_id"].'_'.$Entrys[0]->iemlCode,"",array("",$r["onto_flux_code"],$Entrys[0]->iemlParent,$Entrys[0]->iemlLib,$Entrys[0]->iemlCode));
	
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
function Get_Tree_Trad_Utis($idUtis, $lang){
	
	$type = "Signl_Trad";
	
	//construction de l'entete du tree
	$ihm = '<tree                  
		enableColumnDrag="true"
        typesource="GetTreeTradUtis"
        flex="1"        
        id="'.$type.'" 
        multiple="true"';
    $ihm .= ' onselect="SelectTrad(\''.$type.'\',1,2,3);">'.EOL;
    $ihm .= '<treecols >'.EOL;
	$ihm .= '<treecol hidden="false" flex="1" label="Traductions"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
	$ihm .= '<treecol id="treecol_Tagdel" flex="2"  primary="true" label="Tag delicious"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_descp" flex="2" label="Libellé IEML"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_'.$type.'" flex="1"  label="IEML"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '</treecols>'.EOL;
    $ihm .= '<treechildren >'.EOL;

	foreach($idUtis as $oUti){
		$ihm .= $this->GetTreeItemTradUti($oUti,$type,$lang);	
	}
    
	//termine le tree
	$ihm .= '</treechildren>'.EOL;
    $ihm .= '</tree>'.EOL;

    return $ihm;    
	
        
}

function GetTreeItemTradUti($oUti,$type,$lang){

	$sem = new Sem($this->site,$this->site->infos["XML_Param"],"");
	//charge le dictionaire
	$xml= simplexml_load_file($this->site->infos["LiveMetalDico"]);
	//récupére les traductions 
	if($oUti->id==$this->site->infos["UTI_TRAD_AUTO"]){
		//des traduction automatiques partagées par l'utilisateur 
		$rs = $this->site->RequeteSelect('GetTreeTradUtiAuto',array(array('-idUti-',$_SESSION['iduti']),array('-idUtiAuto-',$oUti->id)));
	}else{
		//de l'utilisateur 
		$rs = $this->site->RequeteSelect('GetTreeTradUti',array(array('-idUti-',$oUti->id)));
	}
	$i=0;
	$oIdUti = -1;
	$ihmTags = "";
	$ihmNivs = "";
	$ihmIeml = "";
	$ihm="";
	$Nivs = array("L0"=>"","L1"=>"","L2"=>"","L3"=>"","L4"=>"","L5"=>"");
	$usl="";
	while($r = mysql_fetch_assoc($rs)){
       	
   		//pour gérer le changement de branche parente
       	if($r["uti_id"]!=$oIdUti){
       		$oFluxCode = -1;
       	}
   		//cherche le code dans le dictionnaire
       	$Xpath = "/wikimetal/entry[id=".$r['ieml_id']."]";
   		$entry=$xml->xpath($Xpath);
   		$iemlNiv = $sem->GetIemlLevel($entry[0]->expression);
   		//on crée les couches
       	if($Nivs[$iemlNiv]==""){
       		//on crée un nouveau niveau
       		$Nivs[$iemlNiv] = '<treeitem id="onto_flux_id_'.$r["onto_flux_id"].'_'.$iemlNiv.'" container="true" open="true">'.EOL;
			$Nivs[$iemlNiv] .= '<treerow>'.EOL;
			$Nivs[$iemlNiv] .= '<treecell label=" "/>'.EOL ;
			$Nivs[$iemlNiv] .= '<treecell label="'.$iemlNiv.'"/>'.EOL ;
	        $Nivs[$iemlNiv] .= '<treecell label=""/>'.EOL ;
            $Nivs[$iemlNiv] .= '</treerow>'.EOL;
       		$Nivs[$iemlNiv] .= '<treechildren>'.EOL;
       	}

       	//on v�rifie si on change de tag
       	if($r["onto_flux_code"]!=$oFluxCode){
       		if($i>0){
	       		//on ferme le pr�c�dent tag
	            $ihmTag .= '<treecell label="'.$usl.'"/>'.EOL ;
	            $ihmTag .= '</treerow>'.EOL;
	       		$ihmTag .= '<treechildren>'.EOL;
	       		//on ferme les niveaux 
				$ihmNivs = "";
	       		foreach($Nivs as $niv=>$ihmNiv){
		       		//uniquement ceux qui sont remplies
					if($ihmNiv!=""){
			       		//on ferme le précédent tag
						$Nivs[$niv] .= '</treechildren>'.EOL;
						$Nivs[$niv] .= '</treeitem>'.EOL;
						$ihmNivs .= $Nivs[$niv];
		       		}
				}
	       		$Nivs = array("L0"=>"","L1"=>"","L2"=>"","L3"=>"","L4"=>"","L5"=>"");
		   		//on crée la couche en court pour l'expression IELM qui suit
		       	if($Nivs[$iemlNiv]==""){
		       		//on crée un nouveau niveau
		       		$Nivs[$iemlNiv] = '<treeitem id="onto_flux_id_'.$r["onto_flux_id"].'_'.$iemlNiv.'" container="true" open="true">'.EOL;
					$Nivs[$iemlNiv] .= '<treerow>'.EOL;
					$Nivs[$iemlNiv] .= '<treecell label=" "/>'.EOL ;
					$Nivs[$iemlNiv] .= '<treecell label="'.$iemlNiv.'"/>'.EOL ;
			        $Nivs[$iemlNiv] .= '<treecell label=""/>'.EOL ;
		            $Nivs[$iemlNiv] .= '</treerow>'.EOL;
		       		$Nivs[$iemlNiv] .= '<treechildren>'.EOL;
		       	}
	       		
       			$ihmTag .= $ihmNivs;
	       		$ihmTag .= '</treechildren>'.EOL;
				$ihmTag .= '</treeitem>'.EOL;
				$ihmTags .= $ihmTag;
       		}
       		//on ouvre le nouveau
			$usl = "";
			$ihmIeml = "";
			$ihmTag = '<treeitem id="onto_flux_id_'.$r["onto_flux_id"].'" container="true" open="true">'.EOL;
			$ihmTag .= '<treerow>'.EOL;
			$ihmTag .= '<treecell label=" "/>'.EOL ;
			$ihmTag .= '<treecell label="'.$r["onto_flux_code"].'"/>'.EOL ;
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
        
       	//on récupère la description de l'item
       	$iemlE = $sem->LiveMetalRequest($lang,$r['ieml_id'],'getEntry');
       	
       	//on crée la branche de traduction
        $Nivs[$iemlNiv] .= $this->AddTreeItemTrad($type.'_'.$r["onto_flux_id"].'_'.$entry[0]['id'],"",array("","",$iemlE->entry->expression.'',$entry[0]->expression.''));
		//on cr�e l'usl
		$usl .= $sem->StarParam["usl"]."(".$entry[0]->expression.")";
       	
        $i++;
   	}
    
   	//v�rifie s'il existe des traductions
   	if($i==0)
   		return "";
   		
    //on ferme les niveaux 
	$ihmTag .= '<treecell label="'.$usl.'"/>'.EOL ;
    $ihmTag .= '</treerow>'.EOL;
    $ihmTag .= '<treechildren>'.EOL;
    //on ferme les niveaux 
	$ihmNivs="";
    foreach($Nivs as $niv=>$ihmNiv){
       	//uniquement ceux qui sont remplies
		if($ihmNiv!=""){
       		//on ferme le précédent tag
			$Nivs[$niv] .= '</treechildren>'.EOL;
			$Nivs[$niv] .= '</treeitem>'.EOL;
			$ihmNivs .= $Nivs[$niv];
       	}
	}
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


function GetTreeTradAuto($tag,$lang){

	$sem = new Sem($this->site,$this->site->infos["XML_Param"],"");

	//on récupère les traductions disponibles
 	$Entrys=$sem->LiveMetalRequestAll($tag,'searchField');
	
	//construction de l'entete du tree
	$ihm = '<tree                  
		enableColumnDrag="true"
        id="GetTreeTradAuto"
        flex="1"        
        multiple="true"';
    $ihm .= ' onselect="SelectTrad(\'GetTreeTradAuto\',1,2,3);">'.EOL;
    $ihm .= '<treecols >'.EOL;
	$ihm .= '<treecol hidden="false" flex="1" label="Traductions"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
	$ihm .= '<treecol id="treecol_Tagdel" flex="2"  primary="true" label="Tag"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_descp" flex="2" label="Libellé IEML"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol id="treecol_'.$type.'" flex="1"  label="IEML"  persist="width ordinal hidden" />'.EOL;
    $ihm .= '</treecols>'.EOL;
    $ihm .= '<treechildren >'.EOL;
    /*on ouvre le tag
    $ihm .= '<treeitem id="ieml_niv_'.$iemlNiv.'" container="true" open="true">'.EOL;
    $ihm .= '<treerow>'.EOL;
    $ihm .= '<treecell label="Automatique"/>'.EOL ;
    $ihm .= '<treecell label="'.$tag.'"/>'.EOL ;
    $ihm .= '<treecell label=""/>'.EOL ;
    $ihm .= '</treerow>'.EOL;
    $ihm .= '<treechildren>'.EOL;
    */
 	
 	$i=0;
	$Nivs = array("L0"=>"","L1"=>"","L2"=>"","L3"=>"","L4"=>"","L5"=>"");
	//construction du tree des réponses
    foreach($Entrys->entry as $entry){

    	//récupère l'élément IEML
    	$iemlE = $sem->LiveMetalRequestAll($entry->id,"getEntryAll");
    	
   		$iemlCode = $iemlE->entry[0]->expression;
    	$iemlNiv = $sem->GetIemlLevel($iemlCode);
   		
    	//on crée les couches
       	if($Nivs[$iemlNiv]==""){
       		//on crée un nouveau niveau
       		$Nivs[$iemlNiv] = '<treeitem id="ieml_niv_'.$iemlNiv.'" container="true" open="true">'.EOL;
			$Nivs[$iemlNiv] .= '<treerow>'.EOL;
    		$Nivs[$iemlNiv] .= '<treecell label=" "/>'.EOL ;
			$Nivs[$iemlNiv] .= '<treecell label="'.$iemlNiv.'"/>'.EOL ;
	        $Nivs[$iemlNiv] .= '<treecell label=""/>'.EOL ;
            $Nivs[$iemlNiv] .= '</treerow>'.EOL;
       		$Nivs[$iemlNiv] .= '<treechildren>'.EOL;
       	}
       	
       	//on crée la branche de traduction
        $Nivs[$iemlNiv] .= $this->AddTreeItemTrad('ieml_id_'.$entry->id,"",array("",$tag,$entry->expression.'',$iemlCode.''));

       	
        $i++;
   	}
    
   	//v�rifie s'il existe des traductions
   	if($i==0)
   		return "Pas de traduction";
   		
    //on ferme les niveaux 
	$ihmNivs="";
    foreach($Nivs as $niv=>$ihmNiv){
       	//uniquement ceux qui sont remplies
		if($ihmNiv!=""){
       		//on ferme le précédent tag
			$Nivs[$niv] .= '</treechildren>'.EOL;
			$Nivs[$niv] .= '</treeitem>'.EOL;
			$ihmNivs .= $Nivs[$niv];
       	}
	}
	
    //on ajoute les niveaux
    $ihm .= $ihmNivs;	
    
	/*on ferme le tag
	$ihm .= '</treechildren>'.EOL;
    $ihm .= '</treeitem>'.EOL;
    */
    
    //termine le tree
	$ihm .= '</treechildren>'.EOL;
    $ihm .= '</tree>'.EOL;

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

//Construction de l'arbre du r�seau d'utilisateur delicious     
function GetTreeDeliciousNetwork($oDlcs,$login='',$type='TreeDeliciousNetwork'){

	if($login=='')
		$login=$oDlcs->sUsername;
	$network = simplexml_load_string($oDlcs->GetNetworkMembers($login));
	$idUtis = $network->xpath("channel/item");
	sort($idUtis);		
	//construction de l'ent�te du tree
	$ihm .= '<tree                  
		enableColumnDrag="true"
        flex="1"        
        id="'.$type.'" 
        multiple="true"';
    $ihm .= ' onselect="SelectNetwork(\''.$type.'\',-1);">'.EOL;
    $ihm .= '<treecols >'.EOL;
	$ihm .= '<treecol hidden="false" flex="1" id="TreeCol_'.$type.'_1" label="login"  persist="width ordinal hidden"  />'.EOL;
    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
    $ihm .= '<treecol hidden="false" flex="1" id="TreeCol_'.$type.'_2" label="depuis"  persist="width ordinal hidden"  />'.EOL;
//    $ihm .= '<splitter class="tree-splitter"/>'.EOL;
//    $ihm .= '<treecol label="actif" flex="1" id="TreeCol_'.$type.'_3" type="checkbox" editable="true"/>'.EOL;
    $ihm .= '</treecols>'.EOL;
    $ihm .= '<treechildren >'.EOL;

    //ajoute le bookmark du login
	$item = '<treeitem id="TreeItem_" >'.EOL;  
    $item .= '<treerow id="TreeRow_" '.$style.' >'.EOL;
    $item .= '<treecell label="'.$login.'"  />'.EOL ;      	
    $item .= '<treecell label=""  />'.EOL ;      	
	$item .= '</treerow>'.EOL;
    $item .= '</treeitem>'.EOL;
	$ihm .= $item;	
    
	foreach($idUtis as $idUti){
		$style="";
		$item = '<treeitem id="TreeItem_'.$idUti->guid.'" >'.EOL;  
        $item .= '<treerow id="TreeRow_'.$idUti->guid.'" '.$style.' >'.EOL;
       	$item .= '<treecell label="'.$idUti->title.'"  />'.EOL ;      	
       	$item .= '<treecell label="'.$idUti->pubDate.'"  />'.EOL ;      	
//       	$item .= '<treecell value="false"  />'.EOL ;      	
       	$item .= '</treerow>'.EOL;
        $item .= '</treeitem>'.EOL;
		
		$ihm .= $item;	
	}
    
	//termine le tree
	$ihm .= '</treechildren>'.EOL;
    $ihm .= '</tree>'.EOL;

    return $ihm;    
	
        
}
	// function pur la construction de dictionnaire IEML
       //<-----------------------------------------------------------------------------------
       function GetTreeIemlOnto($type){
                        
        //adresse de la datasource
                $label="Dictionnaire Ieml";
                if($this->trace)
               		 echo "Xul:GetTree_ieml_onto:Cols".print_r($Cols)."<br/>";
               	$tree.='<?xml version="1.0" ?>'.EOL;
               	$tree.='<overlay id="TreeDectioIeml"
         				xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" flex="1">'.EOL;
                $tree='<vbox id="treeDicoIeml" flex="1" style="background-color:yellow;" >'.EOL;
                $tree.='<label value="'.$label.'" style="font:arial;size:10;color:blue"  />'.EOL;
                $tree.='<box  flex="1"  class="editableTree" >'.EOL;
                $tree.='<tree id="'.$type.'"
                      flex="1"
                      style="width:600; height:400"
                      onselect="SelectDictio(\''.$type.'\',\'treecol_ieml\',\'treecol_descp\');"
                      >'.EOL;
                      //le conteneur doit avoir comme id id pour editableTree
                       $tree.= '<treecols >'.EOL;
                             $tree.= '<treecol id="treecol_Tagdel"  primary="true" label="Tag Delicious"  persist="width ordinal hidden" />'.EOL;
                             $tree.= '<splitter class="tree-splitter"/>'.EOL;
                             $tree.= '<treecol id="treecol_descp"  label="Description"  persist="width ordinal hidden" />'.EOL;
                             $tree.= '<splitter class="tree-splitter"/>'.EOL;
                             $tree.= '<treecol id="treecol_'.$type.'"  label="Traduction"  persist="width ordinal hidden" />'.EOL;
                       $tree.= '</treecols>'.EOL;  
                       $Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='get_hierarchie_ieml1']";
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
                                                 $tree.= '<treecell label="'.$reponse[1].'"/>' .EOL;
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
            $tree.='</overlay>'.EOL;
            if($this->trace)
            echo "Xul:GetTree_ieml_onto:tree". $tree."<br/>";      
            echo $tree;
       }
        
   	   function GetTreeChildrenDictio($id,$parent){
        $container="false";   
        		$Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='get_hierarchie_Dictio_children']";
                $Q = $this->site->XmlParam->GetElements($Xpath);
                $where = str_replace("-niv-", $id, $Q[0]->where);
                $sql = $Q[0]->select.$Q[0]->from.$where;
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
