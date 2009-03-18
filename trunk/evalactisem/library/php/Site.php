<?php
class Site{
  public $id;
  public $idParent;
  public $scope;
  public $NbsTopics;
  public $XmlParam;
  public $trace;
  private $sites;
  public $cache;
  
  function __tostring() {
    return "Cette classe permet de définir et manipuler un site.<br/>";
    }

  function __construct($sites, $id, $scope, $complet=true) {
	//echo "new Site $sites, $id, $scope<br/>";
    $this->sites = $sites;
    $this->id = $id;
    $this->infos = $this->sites[$this->id];
	$this->scope = $scope;
	if(isset($this->scope["FicXml"]))
		$this->XmlParam = new XmlParam($this->scope["FicXml"]);
	else
		$this->XmlParam = new XmlParam($this->infos["FicXml"]);
	
	if($this->infos["SITE_PARENT"]!=-1){
		$Parent = array_keys($this->infos["SITE_PARENT"]);
		$this->idParent = $Parent[0];
	}else{
		$this->idParent = -1;
	}
	if($complet){
		if($this->scope["VoirEn"] == "Mot")
			$Liens = array("page"=>"themes.php?"
				,"pageAjax"=>"design/BlocMilieuMot.php?"
				,"VoirEn"=>"Mot"
				);
		else
			$Liens = array("page"=>"lieux.php?"
				,"pageAjax"=>"design/BlocMilieuTopos.php?"
				,"VoirEn"=>"Topos"
				);
		$this->menu = $this->MenuSite($this->id,0,$Liens);
	}

	// création de l'objet de cache
	$this->cache = new Cache_Lite_Function(array('cacheDir' => CACHEPATH,'lifeTime' => LIFETIME));
	
	//echo "FIN new Site <br/>";
		
    }

  function RequeteSelect($function,$var1,$var2,$val1,$val2){
   	 
   	   $Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='".$function."']";
	   $Q = $this->XmlParam->GetElements($Xpath);
	   $from=str_replace($var1, $val1, $Q[0]->from);
	   $from=str_replace($var2, $val2, $from);
	   $where=str_replace($var1, $val1, $Q[0]->where);
	   $where=str_replace($var2, $val2,$where);
	   $sql = $Q[0]->select.$from.$where;
	   $db = new mysql ($this->infos["SQL_HOST"], $this->infos["SQL_LOGIN"], $this->infos["SQL_PWD"], $this->infos["SQL_DB"]);
	   $link=$db->connect();   
	   $result = $db->query($sql);
	   $db->close($link);
	   
	   return ($result);
   	
   }
   function RequeteInsert($function,$arrVarVal){
   
   	 $Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='".$function."']";
   	 $Q = $this->XmlParam->GetElements($Xpath);
   	 $values=$Q[0]->values;
   	 foreach($arrVarVal as $VarVal){
     	$values=str_replace($VarVal[0], $VarVal[1],$values);	
   	 }
     $sql = $Q[0]->insert.$values;
	 if($this->trace)
     	fb($sql);
	 $db = new mysql ($this->infos["SQL_HOST"], $this->infos["SQL_LOGIN"], $this->infos["SQL_PWD"], $this->infos["SQL_DB"]);
  	 $link=$db->connect();   
	 $db->query($sql);
	 $idTrad= mysql_insert_id();
     $db->close($link);
     		return $idTrad;
   	
   }
    
    
	function utilisateur($uti_login){
		
	 	$Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='Verif_Exist_Utilisateur']";
		
		$Q=$this->XmlParam->GetElements($Xpath);
		$where=str_replace("-login-",$uti_login,$Q[0]->where);
		$sql=$Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "site:utilisateur:login=".$objSite->infos["SQL_LOGIN"]." sql=".$sql."<br/>";
		$db = new mysql ($this->infos["SQL_HOST"], $this->infos["SQL_LOGIN"], $this->infos["SQL_PWD"], $this->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$res=@mysql_fetch_array($req);
		if( @mysql_num_rows($req)==0){
	 		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='Enrg_Utilisateur']";
			$Q=$this->XmlParam->GetElements($Xpath);
			$values=str_replace("-login-",$uti_login,$Q[0]->values);
			$sql=$Q[0]->insert.$values;
			$db = new mysql ($this->infos["SQL_HOST"], $this->infos["SQL_LOGIN"], $this->infos["SQL_PWD"], $this->infos["SQL_DB"]);
			$db->connect();
			$db->query($sql);
			$uti_id=mysql_insert_id();
			$db->close();
			return $uti_id;
		}

		return $res[0]  ;
	}
    
    
    
	public function EstParent($id)
	{
		$arrParent = split("[".DELIM."]", $this->GetParentIds());
		//print_r($arrParent); 
		//echo $id."<br/>";	
		return in_array($id, $arrParent);	
	}

	public function GetParentIds($id = "")
	{
		if($id =="")
			$id = $this->id;
		//echo "GetParentIds = ".$id."<br/>";
			
		if($this->sites[$id]["SITE_PARENT"]!=-1){
			$Parent = array_keys($this->sites[$id]["SITE_PARENT"]);
			$idParent = $Parent[0];
			$valeur .= $this->GetParentIds($idParent);
			$valeur .= $id.DELIM;
		}
		//echo $valeur."<br/>";	
		return $valeur;

	}
	
	public function GetNomSiteParent($id_site=-1)
	{
		if($id_site==-1)
			$id_site=$this->id;
			
		$valeur="";
		//print_r($this->sites[$id_site]["SITE_PARENT"]);
		if(is_array($this->sites[$id_site]["SITE_PARENT"])){
			foreach($this->sites[$id_site]["SITE_PARENT"] as $siteparent=>$type)
			{
				//echo $siteparent."=>".$type."<br/>";
				$valeur .=" ".$this->sites[$siteparent]["NOM"]." ";
				
			}
		}
		return $valeur;	
	}

	public function NextSiteEnfant($id_site)
	{
		$valeur=-1;
		if($this->infos["SITE_ENFANT"]!=-1){		
			$next=false;
			foreach($this->infos["SITE_ENFANT"] as $siteenfant=>$type)
			{
				//echo $this->id." NextSiteEnfant:".$siteenfant."=".$id_site." ".$next."<br/>"; 
				if($next){
					$valeur = $siteenfant;
					break;
				}
				if($siteenfant==$id_site)
					$next=true;				
			}
		}
		return $valeur;
	}

	public function GetSiteEnfant($id_site=-1)
	{
		if($id_site==-1)
			$id_site=$this->id;
			
		$valeur="";
		foreach($this->sites[$id_site]["SITE_ENFANT"] as $siteenfant)
		{
			print_r($siteenfant);
			//$valeur .= $this->GetSiteEnfant($siteenfant=>id);
			//$valeur .= $r['id_rubrique'].DELIM;
			
		}	
	}

	public function GetFilAriane($id=-1)
	{
		if($id==-1)
			$id=$this->id;
			
		$valeur="";
		//echo $this->id." SiteParent=".$this->sites[$id_site]["SITE_PARENT"].'<br/>';
		if($this->sites[$id]["SITE_PARENT"]!=-1){		
			foreach($this->sites[$id]["SITE_PARENT"] as $SiteParent=>$titre)
			{
				$valeur .= $this->GetFilAriane($SiteParent);
			}
		}
		$lien =  "themes.php?site=".$id;
		$valeur .= "<a href='".$lien."'>".$this->sites[$id]["NOM"]."</a> | "."\n";

		return $valeur;		

	}


	public function MenuSite($id_site, $niv=0,$Liens)
	{

		$valeur = "";
		$valon = "";
		$valselect = "";
		$menu =""; 
		//création d'un bloc  pour calculer le nombre de topic
		$g = new Bloc($this,"vide",$this->scope);
		
		//echo $this->id." SiteEnfant=".$this->sites[$id_site]["SITE_ENFANT"].'<br>';
		//echo "création du menu du site et des enfants<br/>";
		if($this->sites[$id_site]["SITE_ENFANT"]!=-1){		
			foreach($this->sites[$id_site]["SITE_ENFANT"] as $siteenfant=>$rptitre)
			{
				//echo $rptitre.' : '.$siteenfant.'<br>';
				$EstParent = $this->EstParent($siteenfant);
				//echo "vérifie la sélection d'un site enfant : ".$this->id." - ".$siteenfant." - EstParent=".$EstParent."<br/>";
				if($siteenfant==$this->id || $EstParent){
					$valon = "<div class='MenuToposOn'></div>";
					$valselect = "<div class='MenuToposLabel'>".$this->sites[$siteenfant]["NOM"]."</div>";
	
					//calcul les enfants
					if($this->sites[$siteenfant]["SITE_ENFANT"]!=-1){
						//echo "calcul le menu des parents : ".$siteenfant."<br/>";
						$menuenfant = $this->MenuSite($siteenfant,$niv-1,$Liens);
					}
				}else{
					//création du lien
					$lien =  $this->GetLien($Liens["page"]
						, array("site","VoirEn","Rub")
						, array($siteenfant,$Liens["VoirEn"],$this->sites[$siteenfant]["RUB_TopicTopos"])
						, array("PageCourante","Rub","RubSelect","SiteSelect","PasCourant")
						);
					//echo "MenuSite - calcul le nombre de topic pour le site : ".$siteenfant."<br/>";
					//if($niv<0)
					$nbmot = $g->GetSiteNbTopic($siteenfant,-1,-1,0);
					if($nbmot>0){
						//$valeur .= "<a href='".$lien."'>".$this->sites[$siteenfant]["NOM"]." (".$nbmot.")</a><br/>";
						$valeur .= "<a href='".$lien."'>".$this->sites[$siteenfant]["NOM"]."</a><br/>";
					}else
						$valeur .= $this->sites[$siteenfant]["NOM"]."<br/>";
				}
				
			}
	
			//calcul du lien
			/*
			if($id_site=="france")
				$lien =  "topictopos.php?site=".$id_site;
			else{
				$lien =  $this->GetLien($Liens["pageAjax"]
					, array("site","VoirEn")
					, array($id_site,"Topos")
					,array("PageCourante")
					);
				$jsFunctions = "onclick=\"AjaxRequest('".$lien."', 'SetBlocMilieuTopos');fcthtmlExpand(".$niv.",'site')\"";
				$lien =  $this->GetLien($Liens["page"]
					, array("site","VoirEn")
					, array($id_site,$Liens["VoirEn"])
					,array("PageCourante","Rub","RubSelect","SiteSelect","PasCourant")
					);
			}
			*/
			$jsFunctions = "onclick=\"fcthtmlExpand(".$niv.",'site')\"";
			
			//création de l'entête
			$menu .= "<script language='JavaScript'>maxHtmlExpand++;</script>";			
			$menu .= "<div class='MenuTopos' >";
			//vérifie si un élément est sélectionné
			if($valon!=""){
				$menu .= $valon;			
				$menu .= "<div class='MenuToposTitre'>".$rptitre."</div>";
				$menu .= "</div>";			
				$menu .= $valselect;
				//$menu .= "<div class='MenuToposLienTous' style='cursor: pointer; cursor: hand;' ".$jsFunctions." > <a href='".$lien."'> >Toutes les ".$rptitre." </a></div>";
				//$menu .= "<div class='MenuToposLienTous' style='cursor: pointer; cursor: hand;' ".$jsFunctions." > >Tout afficher </div>";
				$menu .= "<div class='MenuToposLienTous' style='display:bloc;' id='siteExpand".$niv."' >";
			}else{
				$menu .= $valon;			
				$menu .= "<div class='MenuToposTitre'>".$rptitre."</div>";
				$menu .= "</div>";			
				//$menu .= "<div class='MenuToposLabel'>Tout afficher </div>";
				$menu .= "<div class='MenuToposLienTous' style='display:bloc;' id='siteExpand".$niv."' >";			
			}
			$menu .= $valeur;			
			$menu .= "</div>";
			$menu .= $menuenfant;
		}			
	
		return $menu;
	}
	
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
	  $theValue = str_replace("'","''",$theValue);
	  $theValue = str_replace("\"","''",$theValue);
	  $theValue = htmlentities($theValue);
	  //echo $theValue."<br/>";

	  switch ($theType) {
	    case "text":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;    
	    case "long":
	    case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	    case "double":
	      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
	      break;
	    case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	    case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	  }
	  return $theValue;
	}


	public function GetLien($url, $type_select, $new_val, $arrSup=false)
	{
		if($this->scope!=-1){		
			foreach($this->scope as $param=>$val)
			{
				//prise en compte du tableau des valeurs de paramètre à modifier
				if(is_array($type_select)){
					$i = 0;
					$change = false;
					foreach($type_select as $type)
					{
						if($type==$param){
							$url .= $param."=".$new_val[$i]."&";
							$change = true;
						}
						$i ++;
					}
					if(!$change){
						if($arrSup){
							if (in_array($param, $arrSup))
								$url .= "";
							else
								$url .= $param."=".$val."&";
						}else
							$url .= $param."=".$val."&";
					}
				}else{			
					if($type_select==$param)
						$url .= $param."=".$new_val."&";
					else{
						if($arrSup){
							if (in_array($param, $arrSup))
								$url .= "";
							else
								$url .= $param."=".$val."&";
						}else
							$url .= $param."=".$val."&";
					}
				}
			}
		}
		//enlève la dernière virgule
		$url = substr($url, 0, -1);
		
		return $url;
	}

	function GetSiteResult($site){
	
		$DBSearch = new DatabaseSearch(
			$site->infos["SQL_HOST"]
			,$site->infos["SQL_DB"]
			,$site->infos["SQL_LOGIN"]
			,$site->infos["SQL_PWD"]
			, false
			);
		//echo "DBSearch->needle=".$DBSearch->needle."<br/>";

		$recherche = $DBSearch->needle;

		//Search in table news, return data from column id, search in column tresc
		//It will use value from form (if defined) as needle.
		//$search_result = $DBSearch->DoSearch("spip_rubriques","id_rubrique",array("titre","texte"),"","AND");
		$search_result = $DBSearch->DoSearch("spip_rubriques","id_rubrique",array("texte"),"","AND");
		//print_r($search_result);
		if($search_result){
			$rstRub = array("nb"=>count($search_result),"ids"=>implode(",", $search_result));
			/*	
			$search_result = $DBSearch->DoSearch("spip_mots m INNER JOIN spip_mots_rubriques mr ON mr.id_mot = m.id_mot","id_rubrique",array("titre"),"","AND");
			$rstMot = array("nb"=>count($search_result),"ids"=>implode(",", $search_result));
			*/
			return array("site"=>$site->id,"recherche"=>$recherche,"rstRub"=>$rstRub);
		}
		
	}

	function GetAllResult($site=-1)
	{
		if($site==-1)
			$site = $this;
		
		$SitesEnfants = $site->infos["SITE_ENFANT"];
		//echo "vérifie le calcul des sites enfants ".$SitesEnfants."<br/>";
		$NbT = 0;
		if(is_array($SitesEnfants)){
			//boucle sur les enfants
			$i = 0;
			foreach($SitesEnfants as  $SiteEnfant=>$type)
			{
				//echo "boucle sur les enfants ".$type." : ".$SiteEnfant." ".$this->site->sites[$SiteEnfant]."<br/>";
				$siteEnf = new Site($site->sites, $SiteEnfant, $site->scope, false);
				$R = $this->GetSiteResult($siteEnf);
				if($R){
					$Result[$i] = $R;
					//enregistre le résultat
					$site->NbsTopics[$SiteEnfant]=$Result[$i]["rstRub"]["nb"];
					//additionne le nombre de topic du site enfant
					//$NbT += $site->NbsTopics[$SiteEnfant];
					$i ++;
				}else
					$site->NbsTopics[$SiteEnfant]=0;

			}	
		}
		// enregistre le résultat
		//ajoute le nb de TOPIC du scope
		//$NbT += $site->NbsTopics[$site->id];
		$R = $this->GetSiteResult($site);
		if($R){
			$Result[$i] = $R;
			$site->NbsTopics[$site->id]=$Result[$i]["rstRub"]["nb"];
		}
		//print_r($site->NbsTopics);

		return $Result;
		
	}	
	
	function GetJs($Xpath, $arrParam)
	{
		$nodesJs = $this->XmlParam->GetElements($Xpath);		
		$js = "";
		$i = 0;
		foreach($nodesJs as $nodeJs)
		{
			if($arrParam[$i])
				$Param = $arrParam[$i];
			$function = str_replace("-param".$i."-", $Param, $nodeJs["function"]);
			$js .= " ".$nodeJs["evt"]."=\"".$function."\"";
			$i ++;
		}
		return $js;
	}
	

	public function GetCurl($url){
		
		if($this->trace)
			echo "Site:GetCurl:url=".$url."<br/>";
		$oCurl = curl_init($url);
		// set options
	   // curl_setopt($oCurl, CURLOPT_HEADER, true);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		$arrInfos = curl_getinfo($oCurl);
		if($this->trace)
			echo "Site:GetCurl:arrInfos=".print_r($arrInfos)."<br/>";

		// request URL
		$sResult = curl_exec($oCurl);
		if($this->trace)
			echo "Site:GetCurl:sResult=".$sResult."<br/>";
		
		// close session
		curl_close($oCurl);

		return $sResult;
		
	}
	
	
  }


?>