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
        
                return  "<iframe height='550px' width='450px' src='http://www.mundilogiweb.com/onadabase/design/BlocCarte.php?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
        
        
        }


        function GetRegLeg($id, $row)
        {
                
                /*résultat de row
                champ   rang    titre   type    obligatoire     extra_info      
                ligne_1         6       valeur étalon   ligne                                            
                ligne_2         7       valeur étalon 2         ligne                                            
                ligne_3         4       Nom de la valeur        ligne                                            
                mot_1   5       opérateur               mot     18                               
                mot_2   8       Unités          mot     19                               
                select_1        9       règle respectée         select radio            
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
        
        
        function GetTreeChildren($type, $Cols=-1, $id=-1){
        $container="false";
                
                if($Cols==-1){
                        $Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/Cols/col";
                        $Cols = $this->site->XmlParam->GetElements($Xpath);
                        //print_r($Cols);
                }
                
                $Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']";
                $Q = $this->site->XmlParam->GetElements($Xpath);
                
                if($id==-1){
                        $container="true";
                        
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
                        $tree .= '<treeitem id="'.$type.'_'.$r[0].'" container="'.$container.'" empty="false" >'.EOL;
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
                        
                if($this->trace)
             echo "Xul:GetTreeChildren:tree". $tree."<br/>";  
                return $tree;


        }
    
   
function GetTreeTrad($flux,$trad,$descp,$type,$primary,$bdd,$couche){
        if($type=="No_Trad"){
                $label = "Aucunne traduction";
                        $flex = "flex='1'";
                $splitter = "";
        }
        if($type=="Multi_Trad"){
                $label = "Plusieurs traductions";
                        $flex = "flex='1'";
                        $splitter = "";
        }
        if($type=="Signl_Trad"){
                $label = "Une traduction";
                        $flex = "flex='1'";
                $splitter = "";
        }
        
        //$ihm = '<vbox '.$flex.' style="background-color:blue;" >';    
        //$ihm .= '<label value="'.$label.'" style="font:arial;size:10;color:yellow" />';
        $ihm .= '<tree                  
                        enableColumnDrag="true"
                        typesource="'.$label.'"
                        flex="1"        
                        id="'.$type.'" 
                        multiple="true"';
                        
         if($type=="No_Trad"){
            $ihm .= ' onselect="Select_NoTrad(\''.$type.'\',\'treecol_descp\');">'.EOL; 
            $ihm .= '<treecols >'.EOL;
              $ihm .= '<treecol id="treecol_TagdelType" flex="1"  label="Traductions automatiques"   persist="width ordinal hidden" />'.EOL;
              $ihm .= '<treecol id="treecol_Tagdel" flex="1"  primary="'.$primary.'" label="Tag del.icio.us"   persist="width ordinal hidden" />'.EOL;
              $ihm .= '<splitter class="tree-splitter"/>'.EOL;
            $ihm .= '</treecols>'.EOL;
            $ihm .= '<treechildren >'.EOL;
            //ajoute les trad auto supprimées
            $i=0;  
            foreach($trad as $k=>$t){
              if($i==0){ 
                $ihm .= '<treeitem container="true" open="true" >'.EOL;
                  $ihm .= '<treerow >'.EOL;
                    $ihm .= '<treecell label="'.utf8_encode(" supprimées").'"/>'.EOL;
                    $ihm .= '<treecell label=""/>'.EOL;
                  $ihm .= '</treerow>'.EOL;
                $ihm .= '<treechildren>'.EOL;
               }
               $ihm .= '<treeitem id="treeitemSup_'.$i.'">'.EOL;
                 $ihm .= '<treerow >'.EOL;
                   $ihm .= '<treecell label=""/>'.EOL;
                   $ihm .= '<treecell id="TtreeitemSup__'.$i.'" label="'.utf8_encode($t).'"/>'.EOL;
                 $ihm .= '</treerow>'.EOL;
               $ihm .= '</treeitem>'.EOL; 
               $i++;
            }
            if($i!=0){
              $ihm .= '</treechildren>'.EOL;
              $ihm .= '</treeitem>'.EOL; 
            }
            //ajoute les no trad
            $i=0;
            foreach($flux as $k=>$f){
              if($i==0){
                 $ihm .= '<treeitem container="true" open="true"  >'.EOL;
                   $ihm .= '<treerow properties="Dictio" >'.EOL;
                     $ihm .= '<treecell label="'.utf8_encode(" non trouvées").'"/>'.EOL;
                     $ihm .= '<treecell label=""/>'.EOL;
                  $ihm .= '</treerow>'.EOL;
                 $ihm .= '<treechildren>'.EOL;
              }
              $ihm .= '<treeitem id="treeitem_'.$i.'">'.EOL;
                $ihm .= '<treerow properties="Dictio" >'.EOL;
                  $ihm .= '<treecell label=""/>'.EOL;
                  $ihm .= '<treecell id="Ttreeitem_'.$i.'" label="'.$f.'"/>'.EOL;
                $ihm .= '</treerow>'.EOL;
              $ihm .= '</treeitem>'.EOL; 
              $i++;
           }
           if($i!=0){
             $ihm .= '</treechildren>'.EOL;
             $ihm .= '</treeitem>'.EOL; 
          }
          $ihm .= '</treechildren>'.EOL;
          $ihm .= '</tree>'.EOL;
      }else{
        $ihm .= ' onselect="Select_Trad(\''.$type.'\',1,2,3);">'.EOL;
        $ihm .= '<treecols >'.EOL;
        if($type=="Signl_Trad")
          $visible = 'hidden="false"';
        else
          $visible = 'hidden="true"';
          $ihm .= '<treecol '.$visible.' flex="1" label="Traductions"  persist="width ordinal hidden"  />'.EOL;
          $ihm .= '<splitter class="tree-splitter"/>'.EOL;
          $ihm .= '<treecol id="treecol_Tagdel" flex="2"  primary="'.$primary.'" label="Tag delicious"  persist="width ordinal hidden"  />'.EOL;
          $ihm .= '<splitter class="tree-splitter"/>'.EOL;
          $ihm .= '<treecol id="treecol_descp" flex="2" label="'.utf8_encode('Tag(s) trouvé(s)').'"  persist="width ordinal hidden" />'.EOL;
          $ihm .= '<splitter class="tree-splitter"/>'.EOL;
          $ihm .= '<treecol id="treecol_'.$type.'" flex="1"  label="IEML"  persist="width ordinal hidden" />'.EOL;
          $ihm .= '</treecols>'.EOL;
          $ihm .= '<treechildren >'.EOL;
          foreach($flux as $k=>$f){
            if($f!=""){
               if($type=="Signl_Trad"){
                  if($this->trace)
                     echo "Xul:GetTreeTrad:".print_r($bdd)."<br/>".EOL;  
                  $Trad = '<treeitem id="'.$type.'_'.$k.'" container="true" open="true">'.EOL;
                     $Trad .= '<treerow>'.EOL;
                       $Trad .= '<treecell label=" "/>'.EOL ;
                       $Trad .= '<treecell label="'.$f.'"/>'.EOL ;
                     $Trad .= '</treerow>'.EOL;
                     if(in_array($trad[$k],$bdd)){
                     	$arrTrad = explode(Diaz,$trad[$k]);
                        $arrCouche=explode(Diaz,$couche[$k]);
                        $arrDescp=explode(Diaz,$descp[$k]);
                        
                        $TradUti .= $Trad.'<treechildren>'.EOL;
                        for($j=0;$j<sizeof($arrTrad)-1;$j++){
                           $arrDescps=explode(Virgule,$arrDescp[$j]);
                           $arrTrads=explode(Virgule,$arrTrad[$j]);
                           $TradUti .= '<treeitem id="'.$arrCouche.'_'.$k.'" container="true" open="true">'.EOL;
                             $TradUti .= '<treerow>'.EOL;
                                $TradUti .= '<treecell label=" "/>'.EOL ;
                                $TradUti .= '<treecell label="'.$arrCouche[$j].'"/>'.EOL ;
                              $TradUti .= '</treerow>'.EOL;
                        $TradUti .= '<treechildren>'.EOL;
                        for($l=0;$l<sizeof($arrTrads)-1;$l++){  
                           $TradUti .= $this->AddTreeItemTrad($type.'_'.$k.$l,"", array("","",$arrDescps[$l],$arrTrads[$l]));
                        }
                        $TradUti .= '</treechildren>'.EOL;
                           $TradUti .= '</treeitem>'.EOL;
                        }
                        $TradUti .= '</treechildren>'.EOL;
                        $TradUti .= '</treeitem>'.EOL;
                        
                    }else{
                        $TradAuto .= $Trad.'<treechildren>'.EOL;
                          $TradAuto .= $this->AddTreeItemTrad($type.'_'.$l.$k,"",array("","",$descp[$k],$trad[$k]));
                        $TradAuto .= '</treechildren>'.EOL;
                      $TradAuto .= '</treeitem>'.EOL;
                                                 
                   }
                            
              }
              if($type=="Multi_Trad"){
                 $ihm .= '<treeitem id="'.$type.'_'.$k.'" container="true" open="true">'.EOL;
                   $ihm .= '<treerow>'.EOL;
                      $ihm .= '<treecell label=""/>'.EOL ;
                      $ihm .= '<treecell label="'.$flux[$k].'"/>'.EOL ;
                      $ihm .= '</treerow>'.EOL;
                      $ihm .= '<treechildren>'.EOL;
                      $arrDescp = explode(PointV,$descp[$k]);
                      $arrTrad = explode(PointV,$trad[$k]);
                      $arrCouche=explode(PointV,$couche);
                      for($j=0;$j<sizeof($arrTrad)-1;$j++){
                        $ihm .= $this->AddTreeItemTrad($type.'_'.$k.'_'.$j.'_'.$arrTrad[$j],"", array("","",$arrDescp[$j],$arrTrad[$j]),$arrCouche[$k]);                                             
                      }
                      $ihm .= '</treechildren>'.EOL;
                      $ihm .= '</treeitem>'.EOL;
             }
           }
         $i++;
      }
      if($type=="Signl_Trad"){
       //ajoute les traductions automatiques
       if($TradAuto){
          $ihm .= '<treeitem  properties="HelpUti" container="true" open="true">'.EOL;
          $ihm .= '<treerow >'.EOL;
          	$ihm .= '<treecell properties="HelpUti" label="Automatiques" />'.EOL ;
          $ihm .= '</treerow>'.EOL;
          $ihm .= '<treechildren>'.EOL;
            $ihm .= $TradAuto;
          $ihm .= '</treechildren>'.EOL;
         $ihm .= '</treeitem>'.EOL;
       }
        //ajoute les traduction de l'utilisateur
       if($TradUti){
         $ihm .= '<treeitem  properties="HelpUti" container="true" open="true">'.EOL;
           $ihm .= '<treerow properties="HelpUti" >'.EOL;
             $ihm .= '<treecell properties="HelpUti" label="'.$_SESSION['loginSess'].'" />' .EOL;
             $ihm .= '</treerow>'.EOL;
             $ihm .= '<treechildren>'.EOL;
                $ihm .= $TradUti;
             $ihm .= '</treechildren>'.EOL;
           $ihm .= '</treeitem>'.EOL;
        }
      }
      $ihm .= '</treechildren>'.EOL;
      $ihm .= '</tree>'.EOL;
    }
    //$ihm .= '</vbox>'.$splitter;
    return $ihm;    
 }
    
	function AddTreeItemTrad($id, $style, $cells){
		
		$item = '<treeitem id="'.$id.'" >'.EOL;  
        $item .= '<treerow '.$style.' >'.EOL;
        foreach($cells as $cell){
        	$item .= '<treecell label="'.$cell.'"  />'.EOL ;      	
        }
        $item .= '</treerow>'.EOL;
        $item .= '</treeitem>'.EOL;
		
        return($item);
		
	}
       function TableFlux($sTag,$sDesc,$sUrl,$sDate,$sNote){
        
                $sTag=$_POST["tag"];
                $sDesc=$_POST["desc"];
                $sUrl=$_POST['url'];
                $sDate=$_POST['date'];
                $sNote=$_POST['note'];
                
                
                $aTag=explode(Etoil,$sTag);
                $aDesc=explode(PointV,$sDesc);
                $aUrl=explode(PointV,$sUrl);
                $aDate=explode(PointV,$sDate);
                $aNote=explode(PointV,$sNote);


                        $table.='<listbox id="boxlist"  flex="1" onclick="GoUrl(this.selectedItem.childNodes[2].getAttribute(\'label\'));">';
                                
                                $table.='<listhead >';
                                        $table.='<listheader label="Tag" flex="1"></listheader>';
                                    
                                        $table.='<listheader label="desc" flex="1"></listheader>';
                                        
                                        $table.='<listheader label="url" flex="1"></listheader>';
                                        
                                        $table.='<listheader label="Note" flex="1"></listheader>';
                                        
                                        $table.='<listheader label="date" flex="1"></listheader>';
                                                
                                                                
                                $table.='</listhead>';
                                $table.='<listcols flex="1" >';
                                        $table.='<listcol flex="1">';
                                        $table.='</listcol>';
                                       
                                        $table.='<listcol flex="1">';
                                        $table.='</listcol>';
                                        		
                                        $table.='<listcol flex="1">';
                                        $table.='</listcol>';
                                       
                                        $table.='<listcol flex="1">';
                                        $table.='</listcol>';
                                      
                                        $table.='<listcol flex="1">';
                                        $table.='</listcol>';
                                $table.='</listcols>';
                        
                                for($i=0;$i<sizeof($aTag);$i++)
                                {   
                                    
                                        $table.=('<listitem>');
                                        $table.=('<listcell label="'.$aTag[$i].'"/>');
                                        $table.=('<listcell label="'.$aDesc[$i].'"/>');
                                        $table.=('<listcell label="'.$aUrl[$i].'"/>');
                                        $table.=('<listcell label="'.$aNote[$i].'"/>');
                                        $table.=('<listcell label="'.$aDate[$i].'"/>');
                                        
                                        $table.=('</listitem>');
                                }                                           
                        
                        $table.='</listbox>';
                
            return $table;
        
    }
        
    
       function GetTreeIemlOnto($type){
                        
        //adresse de la datasource
                        $label="Dictionnaire Ieml";
                $Xpath = "/XmlParams/XmlParam[@nom='".$this->site->scope['ParamNom']."']";
                        $ds = $this->site->XmlParam->GetElements($Xpath);
                        //if($this->trace)
                //echo "Xul:GetTree_ieml_onto:ds".print_r($ds)."<br/>";
                        //echo $ds[0]["datasource"];
                        //print_r($Desc);
                        //param des colonnes
                        
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
 }
?>
