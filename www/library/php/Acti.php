<?php
//require('XmlParam.php');

class Acti{
	Public $code;
	Public $desc;
	Public $id;
    
	function ___construct(){
		
	}
	
	function AddActi($code,$iduti){
		global $objSite;
		//recuperation de l'id de l'activité
		$Xpath = "/XmlParams/XmlParam[@nom='Activite']/Querys/Query[@fonction='Select_Acti_id']";
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$where=str_replace('-codeActi-',$code,$Q[0]->where);
		$sql=$Q[0]->select.$Q[0]->from." ".$where;
		$req = $db->query($sql);
		$reponse=mysql_fetch_array($req);
		
		//insetion de l'activité dans la table ieml_acti_uti
		$Xpath = "/XmlParams/XmlParam[@nom='Activite']/Querys/Query[@fonction='AddActi']";
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$values=str_replace("-idActi-",$reponse[0],$Q[0]->values);
		$values=str_replace("-iduti-",$iduti,$values);
		$sql=$Q[0]->insert.$values;
		$req = $db->query($sql);
		 
		 $db->close();
		
	}
	
	

}


?>