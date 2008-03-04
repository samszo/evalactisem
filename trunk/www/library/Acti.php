<?php
//require('XmlParam.php');

class Acti{
	Public $code;
	Public $desc;
	Public $id;
    
	function ___construct(){
		
	}
	
	function AddActi($code,$desc){
		global $objSite;
		
		$Xpath = "/XmlParams/XmlParam[@nom='Activite']/Querys/Query[@fonction='AddActi']";
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$values=str_replace("-codeActi-",$code,$Q[0]->values);
		$values=str_replace("-descActi-",$desc,$values);
		
		$sql=$Q[0]->insert.$values;
		
		 $req = $db->query($sql);
		 
		 $db->close();
		
	}
	
	

}


?>