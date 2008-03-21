<?php
	$ajax = true;
	require_once ("../param/ParamPage.php");
	//charge le fichier de paramètrage
	$objSite->XmlParam = new XmlParam(PathRoot."/param/ParamXul.xml");

	$resultat = "";
	if(isset($_GET['f']))
		$fonction = $_GET['f'];
	else
		$fonction = '';
	if(isset($_GET['id']))
		$id = $_GET['id'];
	else
		$id = -1;
	if(isset($_GET['code']))
		$code = $_GET['code'];
	else
		$code = -1;
	if(isset($_GET['desc']))
		$desc = $_GET['desc'];
	else
		$desc = -1;
		
		

	switch ($fonction) {
		case 'AddTrad':
			$resultat = AddTrad($_GET['idIeml'],$_GET['idflux']);
			break;
	}
	switch ($fonction) {
		case 'SupTrad':
			$resultat = SupTrad($_GET['idIeml'],$_GET['idflux']);
			break;
				case 'SetProc':
			$resultat = SetProc($_GET['id'],$_GET['code'],$_GET['desc']);
			break;
		case 'SetOnto':
			$resultat = SetOnto($_GET['type'],$_GET['col'],$_GET['id'],$_GET['value']);
			break;
		case 'Parse':
			$resultat = Parse($id,$code,$desc);
			break;
		case 'GetGraph':
			$resultat = GetGraph($id,$code,$desc);
			break;
			
	}
	

	echo $resultat;	
	
	function AddTrad($idIeml,$idflux){
	
		global $objSite;
				
		// requête pour vérifier l'existence de la traduction
		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-AddTrad-VerifExist']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		
		$where = str_replace("-idflux-", $idflux, $Q[0]->where);
		$where = str_replace("-idIeml-", $idIeml, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		$row = mysql_fetch_row($result);
		if($row[0]>0)
			return "La traduction existe déjà !";
			
		//requête pour ajouter une traduction
		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-AddTrad-Insert']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		$values = str_replace("-idflux-", $idflux, $Q[0]->values);
		$values = str_replace("-idIeml-", $idIeml, $values);
		$sql = $Q[0]->insert.$values;
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$message = mysql_affected_rows()." traduction ajoutée";
		$db->close();
		
		return $message;
		
		
	}

	function SupTrad($idIeml,$idflux){
	
		global $objSite;
		
		//requête pour Supprimer une traduction
		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		//echo $Q;
		$where = str_replace("-idflux-", $idflux, $Q[0]->where);
		$where = str_replace("-idIeml-", $idIeml, $where);
		//echo $where;
		$sql = $Q[0]->delete.$Q[0]->from.$where;
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$message = mysql_affected_rows()." traduction supprime";
		$db->close();
		
		return $message;
	}
		
	
function SetOnto($type,$col,$id,$valeur){
	
		global $objSite;
				
		// requête pour vérifier l'existence de la traduction
		/*
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-AddTrad-VerifExist']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		$where = str_replace("-id10eF-", $id10eF, $Q[0]->where);
		$where = str_replace("-idIeml-", $idIeml, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		*/
		//modifie le nom de la colonne du tree pour qu'il corresponde au nom de la colonne de la table
		$col = str_replace(preCol,$type,$col);
		$colId = $type."_id";
		$sql = "UPDATE ieml_onto SET 
			".$col."='".utf8_encode($valeur)."'
			, ieml_date = now()
			WHERE ".$colId."=".$id.""; 
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$message = mysql_affected_rows()." modifiée";
		$db->close();
		return $message.$sql;
		
	}

	function SetFlux($type,$col,$id,$valeur){
	
		global $objSite;
				
		// requête pour vérifier l'existence de la traduction
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
		$message = mysql_affected_rows()." modifiée";
		$db->close();
		return $message;
		
	}
function SetProc($id,$code,$desc){
	
		global $objSite;
		$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
		return $sem->SetSem($id,$code,$desc);				
		
	}

	function Parse($code){
	
		global $objSite;
		$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
		echo $sem->Parse($code);				
		
	}

	function GetGraph($id,$code,$desc){
	
		global $objSite;
		$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
		return $sem->GetSvgBarre($code);				
		
	}
	
	
	
	
	
	?>
