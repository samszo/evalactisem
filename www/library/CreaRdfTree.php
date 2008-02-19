<?php
require_once ("../param/ParamPage.php");

	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdfDesc";
	$Desc = $objSite->XmlParam->GetElements($Xpath);
	//print_r($Desc);
	//param des lignes rdf
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdf";
	$Rdfs = $objSite->XmlParam->GetElements($Xpath);	
	//print_r($Rdfs);

	//construction de la requête
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='".$type."']";
	$Q = $objSite->XmlParam->GetElements($Xpath);
	$sql = $Q[0]->select.$Q[0]->from.$Q[0]->where;
	//echo $sql."<br/>"; 

  // Connexion à la base de données
	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
	$db->connect();
	$result = $db->query($sql);
	$db->close();

  // on spécifie au navigateur le type mime des données qu'on va lui envoyé. Ici, c'est du RDF, dont text/xml.

  header('Content-type: text/xml');

  // on commence à lui envoyer le début du fichier RDF

  echo('<?xml version="1.0" encoding="ISO-8859-1"?>'.EOL);
  echo('<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" '.EOL);
  echo('  xmlns:ieml="http://ieml/rdf#" xmlns:NC="http://home.netscape.com/NC-rdf#">'.EOL);
	// boucle de récupération des enregistrements de la requête et génération du contenu RDF
	while ($row = mysql_fetch_row($result))
	{
	    $id = $row[0];
		echo('<rdf:Description rdf:about="urn:'.$Desc[0]["urn"].':'.$objSite->XmlParam->XML_entities($id).'">');
		$i = 0;
		foreach($Rdfs as $Rdf)
		{
		  if($Rdf["parse"])
			$parse = 'NC:parseType="'.$Rdf["parse"].'"';
		  else
			$parse = '';
		  $urn = $Desc[0]["urn"].':'.$Rdf["tag"];
		  $val = $row[$i];
		 // echo "val ".$Rdf["tag"]."=".$val;
	      echo('<'.$urn.' '.$parse.' >'.$objSite->XmlParam->XML_entities($val).'</'.$urn.'>');
		  $i ++;
		}
		echo('</rdf:Description>'.EOL);
    }

  // hierarchie
  if($Desc[0]["tree"])
	echo get_hierarchie($type, "", "");

  // fin du fichier RDF
  echo('</rdf:RDF>'.EOL);


function get_hierarchie($type, $SoId, $SoCode, $Niv=1) {

	//echo "$code, $type";
	global $objSite;
	
	//construction de la requête
	if($Niv==1){
		$hier = '<rdf:Seq rdf:about="urn:roots">'.EOL;
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='get_hierarchie_".$type."1']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		$sql = $Q[0]->select.$Q[0]->from.$Q[0]->where;
	}else{
	  	$hier = '<rdf:Seq rdf:about="urn:ieml:'.$SoId.'">'.EOL;
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='get_hierarchie_".$type."']";
		$Q = $objSite->XmlParam->GetElements($Xpath);
		$where = str_replace("-SoCode-", $SoCode, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
	}
	//echo $sql."<br/>";

	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
	$db->connect();
	$req = $db->query($sql);
	$db->close();
	$nb = mysql_num_rows($req);
	$hierEnfant = "";
	while($r = mysql_fetch_assoc($req))
	{
	  $hier .= '<rdf:li rdf:resource="urn:ieml:'.$r['DeId'].'"/>'.EOL;
	  if($Niv<4)
		  $hierEnfant .= get_hierarchie($type, $r['DeId'], $r['DeCode'], $Niv+1);
	}

	if($nb>0)
		$hier .= '</rdf:Seq>'.EOL;
	else
		$hier = '';
	
	return $hier.$hierEnfant;
}
 
 ?>