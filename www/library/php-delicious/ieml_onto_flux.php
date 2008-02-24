<?php
require_once ("../param/ParamPage.php");

$codeFlux=$_Get['code_f'];
$descFlux=$_Get['desc_f'];
$niveauFlux=$_Get['niv_f'];
$parentsFlux=$_Get['par_f'];

$values = str_replace("-codeFlux-", $codeFlux, $Q[0]->values);
$values = str_replace("-$descFlux-", $descFlux, $Q[0]->values);
$values = str_replace("-$niveauFlux-", $descFlux, $Q[0]->values);
$values = str_replace("-$parentsFlux-", $parentsFlux, $Q[0]->values);



$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='Ieml_Onto_Flux'";
$Q = $objSite->XmlParam->GetElements($Xpath);
$sql = $Q[0]->insert.$values;

$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$req = $db->query($sql);
			$db->close();
			

?>