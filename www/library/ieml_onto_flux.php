<?php
require_once ("../param/ParamPage.php");

$codeFlux=$_GET["code_f"];
$descFlux=$_GET["desc_f"];
$niveauFlux=$_GET["niv_f"];
$parentsFlux=$_GET["par_f"];

if(($_GET["req"]=="GetAllTags")||($_GET["req"]=="GetRecentPosts")||($_GET["req"]=="GetAllPosts")){
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='Ieml_Onto_Flux']";
	$Q = $objSite->XmlParam->GetElements($Xpath);

	$values = str_replace("-codeFlux-", $codeFlux, $Q[0]->values);
	$values = str_replace("-descFlux-", $descFlux, $values);
	$values = str_replace("-niveauFlux-", $niveauFlux, $values);
	$values = str_replace("-parentsFlux-", $parentsFlux, $values);
	
	$sql = $Q[0]->insert.$values;

	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$req = $db->query($sql);
			$db->close();
}
			

?>