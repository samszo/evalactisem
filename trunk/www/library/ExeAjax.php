<?php
	$ajax = true;
	require_once ("../../param/Constantes.php");
	require_once ("../../param/ParamPage.php");
	//charge le fichier de param�trage
	$objSite->XmlParam = new XmlParam(PathRoot."/param/SolAcc.xml");

	$resultat = "";
	if(isset($_GET['f']))
		$fonction = $_GET['f'];
	else
		$fonction = '';
	if(isset($_GET['cols']))
		$cols = $_GET['cols'];
	else
		$cols = -1;
	if(isset($_GET['id']))
		$id = $_GET['id'];
	else
		$id = -1;

	switch ($fonction) {
		case 'AddTrad':
			$resultat = AddTrad($_GET['idIeml'],$_GET['id10eF']);
			break;
		case 'SetProc':
			$resultat = SetProc($_GET['id'],$_GET['code'],$_GET['desc']);
			break;
		case 'SetOnto':
			$resultat = SetOnto($_GET['type'],$_GET['col'],$_GET['id'],$_GET['value']);
			break;
		case 'GetTree':
			$resultat = GetTree($_GET['type'],$cols,$id);
			break;
		case 'GetTabForm':
			$resultat = GetTabForm($_GET['type'],$id);
			break;
		case 'AddGrilles':
			$resultat = AddGrilles($_GET['src'], $_GET['dst'], false);
			break;
		case 'AddPlacemark':
			$resultat = AddPlacemark($_GET['dst'], $_GET['kml']);
			break;
		case 'SetVal':
			$resultat = SetVal($_GET['idDon'],$_GET['champ'],$_GET['val']);
			break;
		case 'GetCurl':
			$resultat = GetCurl($_GET['url']);
			break;
	}

	echo  utf8_encode($resultat);	
	
	function GetCurl($url)
	{
	
		$oCurl = curl_init($url);
		// set options
	   // curl_setopt($oCurl, CURLOPT_HEADER, true);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		//echo $sCmd."<br/>";
		//$arrInfos = curl_getinfo($ch);
		//print_r($arrInfos);
		//echo "sResult=<br/>";
		//print_r($sResult);
		//echo "<br/>";
		//fin ajout samszo

		// request URL
		$sResult = curl_exec($oCurl);
		// close session
		curl_close($oCurl);

		return $sResult;
	
	}
	
	function SetVal($idDon,$champ,$val){
	
		global $objSite;
		$g = new Grille($objSite);

		//modifie la valeur 
		$row = array("champ"=>$champ,"valeur"=>utf8_decode($val));
		$g->SetChamp($row, $idDon);

		return "donn�e enregistr�e = ".utf8_decode($val);
	}

	function SetOnto($type,$col,$id,$valeur){
	
		global $objSite;
				
		// requ�te pour v�rifier l'existence de la traduction
		/*
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-AddTrad-VerifExist']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		$where = str_replace("-id10eF-", $id10eF, $Q[0]->where);
		$where = str_replace("-idIeml-", $idIeml, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		*/
		$sql = "INSERT INTO ieml_flux (flux_ieml, flux_date) 
			VALUES ('".$type.$col.$id.$valeur."',now())";
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$message = mysql_affected_rows()." modifi�e";
		$db->close();
		return $message;
		
	}


	function AddTrad($idIeml,$id10eF){
	
		global $objSite;
				
		// requ�te pour v�rifier l'existence de la traduction
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-AddTrad-VerifExist']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		$where = str_replace("-id10eF-", $id10eF, $Q[0]->where);
		$where = str_replace("-idIeml-", $idIeml, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		$row = mysql_fetch_row($result);
		if($row[0]>0)
			return "La traduction existe d�j� !";
			
		//requ�te pour ajouter une traduction
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-AddTrad-Insert']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		$values = str_replace("-id10eF-", $id10eF, $Q[0]->values);
		$values = str_replace("-idIeml-", $idIeml, $values);
		$sql = $Q[0]->insert.$values;
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$message = mysql_affected_rows()." traduction ajout�e";
		$db->close();
		
		return $message;
		
	}

	function SetProc($id,$code,$desc){
	
		global $objSite;
		$sem = New Sem($objSite, PathRoot."/param/EvalActiSem.xml", "");
		return $sem->SetSem($id,$code,$desc);				
		
	}

	function GetTree($type,$Cols,$id){
		global $objSite;
		
		//r�cup�ration des colonnes
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/col";
		$Cols = $objSite->XmlParam->GetElements($Xpath);		

		//une seule s�lection possible seltype='single' onselect=\"GetTreeSelect('tree".$type."','TreeTrace',2)" seltype='multiple' single
		//	class='editableTree' 			width='100px' height='100px' 

		//r�cup�ration des js
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/js";
		$js = $objSite->GetJs($Xpath, array($type));
		
		$tree = "<tree flex=\"1\" 
			id=\"tree".$type."\"
			seltype='multiple'
			".$js."
			>";
		$tree .= '<treecols>';
		$tree .= '<treecol  id="id" primary="true" cycler="true" flex="1" persist="width ordinal hidden"/>';
		$tree .= '<splitter class="tree-splitter"/>';

		$i=0;
		foreach($Cols as $Col)
		{
			//la premi�re colonne est le bouton pour d�plier
			if($i!=0){
				if($Col["hidden"])
					$visible = $Col["hidden"];
				else
					$visible = "false";
				if($Col["type"]=="checkbox"){
					$tree .= '<treecol id="treecol_'.$Col["tag"].'" label="'.$Col["tag"].'" type="checkbox" editable="true" persist="width ordinal hidden" />';
				}else{
					$tree .= '<treecol id="treecol_'.$Col["tag"].'" hidden="'.$visible.'" label="'.$Col["tag"].'" flex="3"  persist="width ordinal hidden" />';
					$tree .= '<splitter class="tree-splitter"/>';
				}
			}
			$i++;
		}
		$tree .= '</treecols>';
		$tree .= $objSite->GetTreeChildren($type, $Cols, $id);
		$tree .= '</tree>';
		/*
		header('Content-type: application/vnd.mozilla.xul+xml');
		$tree = $objSite->GetTreeChildren($type, $Cols, $id);
		*/
		return $tree;
		
	}

	function GetTabForm($type, $idRub){
		global $objSite;
		$g = new Grille($objSite);
		$xul = $g->GetXulTab($type, $idRub);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}

	function AddGrilles($idRubSrc, $idRubDst, $redon){
		global $objSite;
		$g = new Grille($objSite);
		$xul = $g->AddGrilles($idRubSrc, $idRubDst, $redon);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}
	
	function AddPlacemark($idRubDst, $kml){
		global $objSite;
		$g = new Grille($objSite);
		//cr�ation de la grille g�olocalisation
		$idDon = $g->AddDonnee($idRubDst, $objSite->infos["GRILLE_GEO"], false);
		
		//ajoute la valeur du kml
		$row = array("champ"=>"texte_1","valeur"=>$kml);
		$g->SetChamp($row, $idDon);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return "donn�e cr�� = ".$idDon;
		
	}
?>

