<?php
/*
/////////////////////
Nom du fichier : Sem.php

Version : 1.0
Auteur : samszo
Date de modification : 29/11/2007

////////////////////
*/

Class Sem{
	public $Id;
	public $Flux;
	public $Date;
	public $Src;
	public $Dst;
	public $Tra;
	private $site;

	function __construct($So, $De="", $Tr="") {
	    $this->FicXml = $FicXml;
		//echo "On charge les paramètres : ".$FicXml."<br/>\n";
		if ($xml = simplexml_load_file($FicXml))
			$this->xml = $xml;
		
	}
	
	public function CreaFlux($Acte){
		//echo 'On cherche le xpath '.$Xpath.'<br/>';
		return $this->xml->xpath($Xpath);
	}
	
	public function GetFlux($Acte){
		//echo 'On cherche le xpath '.$Xpath.'<br/>';
		return $this->xml->xpath($Xpath);
	}
	
	public function SetFlux()
	{
	    return "";
	}


	function GetChoixNavig($So, $De="", $Tr="", $NumEtap=1) {
	
		//recupere les infos
		if($De==""){
			$Xpath = "/EvalActiSem/Querys/Query[@fonction='Sem->GetChoixNavig->infoSo']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-So-", $So, $Q[0]->where);
			$sql = $Q[0]->select.$Q[0]->from.$where;
		}else{
			$Xpath = "/EvalActiSem/Querys/Query[@fonction='Sem->GetChoixNavig->infoDe']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-De-", $De, $Q[0]->where);
			$sql = $Q[0]->select.$Q[0]->from.$where;
		}

		//récupère la définition du layer
		$Xpath = "/EvalActiSem/StarIEML/Mark[@layer=".$NumEtap."]";
		$Mark = $this->site->XmlParam->GetElements($Xpath);
		$Xpath = "/EvalActiSem/StarIEML/Mark[@layer=".($NumEtap-1)."]";
		$MarkParent = $this->site->XmlParam->GetElements($Xpath);
		
		//echo "sql = ".$sql."<br/>\n";

		$liste="<ul>";
		  	
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		while($r = mysql_fetch_assoc($req))
		{

			//récupère le processus de navigation = chaque layer est une étape
			$navig = split($Mark["closing"],$r['Decode']);

			//conserve la translation
			$Tr = $So.$MarkParent["closing"].$r['Decode'].$MarkParent["closing"].$Mark["closing"].$Tr;
			
			if(count($navig)>=$NumEtap){
				//récupère l'étape
				$etape = $navig[$NumEtap];
				//echo "etape ".$NumEtap." = ".$etape."<br/>\n";
				//vérifie la fin de layer -1
				if($etape!="..."){
					//récupère la base du menu (idée 0) et la liste qui est liée (idée 1)
					$menu = split($MarkParent["closing"],$etape);
					//echo "idée = ".$menu[0]." - ".$menu[1]."<br/>\n";
					if($De=="_"){
						//echo "menu source<br/>\n";
						$sousliste.= get_choix_navig($So, $menu[0], $Tr, $NumEtap);
					}else{
						//echo "menu destination on passe à la navigation suivante<br/>\n";
						$sousliste.= get_choix_navig($menu[1], "", $Tr, $NumEtap+1);
					}
				}
			}
				
			//construction de la requête d'insertion des processus
			$url = PathRoot."includes/NavigArbo.php?f=set_processus&id_parent=-1&So=".$_SESSION["user"]."&De=".$r['Decode']."&Tr=".$Tr;
			$js = "onclick=\"GetResultFonction('".$url."','ProcessusIeml');\"";
			$liste.="<li><a class='MenuBarItemSubmenu' ".$js." href='#'>".utf8_encode($r['Delib'])."</a>";

			$liste.=$sousliste;		
			
			$liste.="</li>";					
		}

		$liste.= "</ul>";
		if($nb==0)
			$liste= "";
		
		return $liste;
	}


	
}
?>