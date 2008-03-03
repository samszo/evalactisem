<?php
class Grille{
  public $id;
  public $XmlParam;
  public $trace;
  private $site;

  function __tostring() {
    return "Cette classe permet de définir et manipuler des grilles.<br/>";
    }

  function __construct($site, $id=-1, $complet=true) {
	//echo "new Site $sites, $id, $scope<br/>";
	$this->trace = false;

    $this->site = $site;
    $this->id = $id;
	if($this->site->scope["FicXml"]!=-1)
		$this->XmlParam = new XmlParam($this->site->scope["FicXml"]);
	
	if($complet){
	}

	//echo "FIN new grille <br/>";
		
    }

	function AddGrilles($idRubSrc, $idRubDst, $redon=false){
			
		//récuparation des grilles des articles publiés de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetGrillesPublie']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		//echo $sql."<br/>";
		
		$result = ""; 
		while ($row =  $db->fetch_assoc($rows)) {
			$idDon = $this->AddDonnee($idRubDst, $row["id_form"], $redon);
			$result .= $row["id_form"]." ".$row["titre"]." ".$idDon."<br/>";		
		}
		
		//récupération des rubriques dans les liens de la rubrique Src 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubInLiens']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		echo $sql."<br/>";
		while ($row =  $db->fetch_assoc($rows)) {
			$result .= $this->AddGrilles($row["idRub"], $idRubDst, $row["idRub"]);
		}
		
		return $result;
	}
	


	function AddDonnee($idRub, $idGrille=-1, $redon=false){
		
		if($idGrille==-1)
			$idGrille=$this->id;
			
		//récuparation du granulat
		$g = new Granulat($idRub, $this->site);
		//"récupération ou création du dernier article en cours de rédaction"; 
		$idArt = $g->GetArticle(" AND a.statut='prepa'");
				
		if($redon){
			//récupère les dernières données publiées
			$g = new Granulat($redon, $this->site);
			$rows = $g->GetGrille($idGrille, " AND a.statut='publie'");
			$oDonnee="";
			while ($row =  mysql_fetch_assoc($rows)) {
				//vérifie s'il on change de donnee
				if($row["id_donnee"]!=$oDonnee){
					$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
					$oDonnee=$row["id_donnee"];
				}
				$this->SetChamp($row, $idDon, false);
				//echo "--- ".$donId." nouvelle valeur ".$i;
			}
		}else{
			//récupération ou création d'une nouvelle donnée
			$idDon = $g->GetIdDonnee($idGrille, $idArt);
			//récupère la définition des champs sans valeur
			$rows = $this->GetChamps($idGrille);
			//initialisation de la donnée
			$this->SetChamps($rows, $idDon);
		}

		echo "idRub = ".$idRub." idArt = ".$idArt." idDon = ".$idDon."<br/>"; 
		return $idDon;
	
	}

	function GetChamps($idGrille=-1){
	
		if($idGrille==-1)
			$idGrille=$this->id;

		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChamps']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idGrille-", $idGrille, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo $sql."<br/>";
		
		return $result;
	
	}
	
	function SetChamps($rows, $donId) {

		//suppression des éventuelle champ pour la donnée
		$this->DelDonnee($donId);
		
		//création des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	  
	function DelDonnee($donId) {

		//Supression des valeurs de champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		//echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	

	function SetChamp($row, $donId, $del=true) {

		if($del)
			//supression de la valeur
			$this->DelChamp($row, $donId); 
		
		//création de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_InsChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$values = str_replace("-idDon-", $donId, $Q[0]->values);
		$values = str_replace("-champ-", $row["champ"], $values);
		$values = str_replace("-val-", $row["valeur"], $values);
		$sql = $Q[0]->insert.$values;
		if($this->trace)
			echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
	
	function DelChamp($row, $donId) {

		//supression de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$where = str_replace("-champ-", $row["champ"], $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
	
	function GetXulTab($src, $id, $dst="Rub", $recur = false){


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


	function GetXulTabPanels($src, $id, $dst="Rub", $recur = false){

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

  function GetRubDon($idDon) {
  
  
		//requête pour récupérer la rubrique de la donnée
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubDon']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetRubDon ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$r = $db->fetch_assoc($req);
		
		return $r["id"];
		
		
	}
	
			
  function GetXulForm($idDon, $idGrille) {
  
  
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
	
	function GetXulCarto($idDon)
	{
	
		return	"<iframe height='550px' width='450px' src='http://www.mundilogiweb.com/onadabase/design/BlocCarte.php?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
	
	
	}

	function GetXulRegLeg($id, $row)
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
	
	function GetXulControl($id, $row)
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
	
  }
class Site{
  public $id;
  public $idParent;
  public $scope;
  public $NbsTopics;
  public $XmlParam;
  private $sites;

  function __tostring() {
    return "Cette classe permet de définir et manipuler un site.<br/>";
    }

  function __construct($sites, $id, $scope, $complet=true) {
	//echo "new Site $sites, $id, $scope<br/>";

    $this->sites = $sites;
    $this->id = $id;
    $this->infos = $this->sites[$this->id];
	$this->scope = $scope;
	if($this->scope["FicXml"]!=-1)
		$this->XmlParam = new XmlParam($this->scope["FicXml"]);
	
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

	//echo "FIN new Site <br/>";
		
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
	
	function GetTreeChildren($type, $Cols=-1, $id=-1){

		if($Cols==-1){
			$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/Cols/col";
			$Cols = $this->XmlParam->GetElements($Xpath);	
			//print_r($Cols);
		}
		
		$Xpath = "/XmlParams/XmlParam[@nom='".$this->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']";
		$Q = $this->XmlParam->GetElements($Xpath);
		//print_r($Q);
		if($id==-1){
			//récupère la valeur par defaut
			$Xpath = "/XmlParams/XmlParam[@nom='".$this->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/from";
			$attrs = $this->XmlParam->GetElements($Xpath);
			//print_r( $attrs[0]["def"]);
			
			if($attrs[0]["niv"])
				$id = $attrs[0]["niv"];
			//echo $id." def<br/>";
		}
		
		$from = str_replace("-parent-", $id, $Q[0]->from);
		//ECHO $FROM;
		$sql = $Q[0]->select.$from;
		//echo $sql;
		
		$db = new mysql ($this->infos["SQL_HOST"], $this->infos["SQL_LOGIN"], $this->infos["SQL_PWD"], $this->infos["SQL_DB"], $dbOptions);
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
			//colonne de l'identifiant
			//$tree .= '<treecell label="'.$r[$i].'"/>'.EOL;
			foreach($Cols as $Col)
			{
				$tree .= '<treecell label="'.$r[$i].'"/>'.EOL;
				$i ++;
			}
			$tree .= '</treerow>'.EOL;
			$tree .= $this->GetTreeChildren($type, $Cols, $r[0]);
			$tree .= '</treeitem>'.EOL;
		}

		if($nb>0)
			$tree .= '</treechildren>'.EOL;
		else
			$tree = '';
		
		return $tree;

	}

	
	
  }
?>