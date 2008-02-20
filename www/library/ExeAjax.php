<?php
	$ajax = true;
	require_once ("../param/ParamPage.php");
	//charge le fichier de paramètrage
	$objSite->XmlParam = new XmlParam(PathRoot."/param/EvalActiSem.xml");

	$resultat = "";
	if(isset($_GET['f'])){
		$fonction = $_GET['f'];
	}
	else
		$fonction = '';

	switch ($fonction) {
		case 'AddTrad':
			$resultat = AddTrad($_GET['idIeml'],$_GET['id10eF']);
			break;
	}
	switch ($fonction) {
		case 'SupTrad':
			$resultat = SupTrad($_GET['idIeml'],$_GET['id10eF']);
			break;
	}

	echo $resultat;	
	
	function AddTrad($idIeml,$id10eF){
	
		global $objSite;
				
		// requête pour vérifier l'existence de la traduction
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
			return "La traduction existe déjà !";
			
		//requête pour ajouter une traduction
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-AddTrad-Insert']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		$values = str_replace("-id10eF-", $id10eF, $Q[0]->values);
		$values = str_replace("-idIeml-", $idIeml, $values);
		$sql = $Q[0]->insert.$values;
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$message = mysql_affected_rows()." traduction ajoutée";
		$db->close();
		
		return $message;
		
		
	}

	function SupTrad($idIeml,$id10eF){
	
		global $objSite;
		
		//requête pour Supprimer une traduction
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-SupTrad-Delete']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		//echo $Q;
		$where = str_replace("-id10eF-", $id10eF, $Q[0]->where);
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
		
	
?>
