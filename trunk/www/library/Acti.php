<?php
//require('XmlParam.php');

class Acti{
	Public $code_Acti;
	Public $desc_Acti;
	Public $id_Acti;

	function AddActi($code_Acti,$desc_Acti){
		global $objSite;
		
		$Xpath = "/XmlParams/XmlParam[@nom='Activite']/Querys/Query[@fonction='AddActi']";
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$values=str_replace("-codeActi-",$code_Acti,$Q[0]->values);
		$values=str_replace("-descActi-",$desc_Acti,$values);
		
		$sql=$Q[0]->insert.$values;
		
		 $req = $db->query($sql);
		 
		 $db->close();
		
	}
	
	

}

$Activite= new Acti();
?>