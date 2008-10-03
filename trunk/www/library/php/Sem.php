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
	public $Desc;
	public $Tra;
	public $StarParam;
	public $XmlParam;
	private $site;
	private $trace;
    public  $parse;
    public  $Primis;
    public $xmlPrimis;
    public  $Events;
    public $xmlEvent;
    
    
	function __construct($Site, $FicXml, $So, $De="", $Tr="") {
		
		$this->trace = TRACE;
        /*
		if($FicXml=="")
			$FicXml==$Site->scope["FicXml"];
		if($this->trace){
			echo "On charge les paramètres : ".$FicXml."<br/>\n";
			$this->XmlParam = new XmlParam($FicXml);
		    
		}*/
        //$this->parse = $FicXml;
		$this->site = $Site;	
		$this->Src = $So;

		$StarParam = $this->site->XmlParam->GetElements("/EvalActiSem/StarIEML");

		$this->StarParam = array(
			"full"=>$StarParam[0]->Seme[0]["full"]
			, "empty"=>$StarParam[0]->Seme[0]["empty"]
			, "verb"=>$StarParam[0]->Seme[0]["verb"]
			, "noun"=>$StarParam[0]->Seme[0]["noun"]
			, "copy"=>$StarParam[0]->Go[0]["copy"]
			, "opening"=>$StarParam[0]->Go[0]["opening"]
			, "union"=>$StarParam[0]->Slo[0]["union"]
			, "difference"=>$StarParam[0]->Slo[0]["difference"]
			, "intersection"=>$StarParam[0]->Slo[0]["intersection"]
			, "closing"=> array(
				"seme"=>"_"
				,"phrase"=>","
				,"idea"=>"'"
				,"relation"=>"-"
				,"event"=>"."
				,"primitive"=>":"
				)
			);
		//print_r($this->StarParam);
		
		//charge les paramètres des layers
		if (file_exists("../../param/events.xml")) {
		    $this->xmlEvent = simplexml_load_file("../../param/events.xml");
		} else {
		    exit('Echec lors de l\'ouverture du fichier events.xml.');
		}
		if (file_exists("../../param/primitives.xml")) {
		    $this->xmlPrimis = simplexml_load_file("../../param/primitives.xml");
		} else {
		    exit('Echec lors de l\'ouverture du fichier primitives.xml.');
		}
		
				
	}
	
	public function CreaFlux($Acte){
		//echo 'On cherche le xpath '.$Xpath.'<br/>';
		return $this->xml->xpath($Xpath);
	}
	
	public function GetFlux($Acte){
		//echo 'On cherche le xpath '.$Xpath.'<br/>';
		return $this->xml->xpath($Xpath);
	}
	
	public function GetDesc($So){
		return $this->GetInfos($So,'dsc');
	}

	public function GetId($So){
		return $this->GetInfos($So,'id');
	}

	public function GetInfos($So, $champ){
		//requête pour modifier le processus
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='Sem-GetInfos']";
		$Q = $this->XmlParam->GetElements($Xpath);
		$where = str_replace("-So-", str_replace("'","''",$So), $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;	
		//echo $sql."<br/>"; 
		$db = new mysql ($this->site->infos["SQL_HOST"]
			, $this->site->infos["SQL_LOGIN"]
			, $this->site->infos["SQL_PWD"]
			, $this->site->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$val = "";
		while($r = mysql_fetch_assoc($req))
		{
			$val = $r[$champ];
		}
		return $val;
	
	}

	public function SetSem($id,$code,$desc){
	
		//formate les valeurs
		$code = (!get_magic_quotes_gpc()) ? addslashes($code) : $code;
		$desc = (!get_magic_quotes_gpc()) ? addslashes($desc) : $desc;					
						
		//requête pour modifier le processus
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-SetProc']";
		$Q = $this->XmlParam->GetElements($Xpath);
		$values = str_replace("-code-", $code, $Q[0]->values);
		$values = str_replace("-desc-", $desc, $values);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->update.$values.$where;
		//echo $sql."<br/>"; 
		$db = new mysql ($this->site->infos["SQL_HOST"]
			, $this->site->infos["SQL_LOGIN"]
			, $this->site->infos["SQL_PWD"]
			, $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$message = mysql_affected_rows()." modification effectuée";
		$db->close();
	    return $message;
	}

	function GetMark($end, $layer=-1){
		if($this->trace)
			echo "récupération des marks  $end, $layer<br/>";

		//suivant le dernier caractère de l'expression IEML
		if($end!="")
			$Xpath = "/EvalActiSem/StarIEML/Mark[@closing='".$end."']";
		// ou le n° du layer
		if($layer!=-1)
			$Xpath = "/EvalActiSem/StarIEML/Mark[@layer=".$layer."]";
		
		if($this->trace){
			echo "GetMark Xpath=".$Xpath."<br/>";
		}
		
		return $this->XmlParam->GetElements($Xpath);

	}

	function GetSvgBarre($code){
		if($code=="")
			$code=$this->Src;
		if($this->trace)
			echo "Sem.php:GetSvgBarre:code".$code."<br/>";
			
		
		$parse = $this->Parse($code);
		if($this->trace)
			echo "Sem.php:GetSvgBarre:parse".$parse."<br/>";
		
		//nettoie le résultat du parser
	    $parse = str_replace("<XMP>","",$parse);
	    $parse = str_replace("</XMP>","",$parse);
	    $parse = str_replace("<?xml version=\"1.0\"?>"," ",$parse);
	    $xml = simplexml_load_string($parse);
		if($this->trace)
			echo "Sem.php:GetSvgBarre:xml".print_r($xml)."<br/>";
		$donnees = "";
		$noms = "";
		foreach($xml->xpath("//genOp") as $genOps){
			if($this->trace)
				echo "Sem.php:GetSvgBarre:genOps".print_r($genOps)."<br/>";
			$a = $genOps->attributes();
			//print_r($a);
		    $noms .= $a->layer."_".$a->role."_first:".$a->first.";";
		    $donnees .= $a->first.";";
		    $noms .= $a->layer."_".$a->role."_last:".$a->last.";";
		    $donnees .= $a->last.";";
		    $noms .= ";";
		    $donnees .= "0;";
		}
		
		$lien= 'library/stats.php?large=350';
		$lien.='&haut=300';
		$lien.='&titre='.urlencode($code);
		$lien.='&donnees='.$donnees;
		$lien.='&noms='.$noms;
		$lien.='&type=barre';
		$lien.='&col1=yellow';
		$lien.='&col2=red';
		$lien.='&col3=blue';
		$lien.='&col4=black';
		

		/*$oCurl = curl_init($lien);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		$sResult = curl_exec($oCurl);
		// close session
		curl_close($oCurl);

		header("Content-type: image/svg+xml");*/
		return $lien;
	}

	function GetSvgPie($code){
		if($code=="")
			$code=$this->Src;
		if($this->trace)
			echo "Sem.php:GetSvgPie:code".$code."<br/>";
			
		$xml = $this->Parse($code);
		 
		if(is_object($xml)){
			
				if($this->trace)
					echo "Sem.php:GetSvgPie:xml".print_r($xml)."<br/>";
		
				//$xmlEvent = simplexml_load_file("../param/events.xml");
				$this->GetOccurrence($xml);
				
				//construction des données de l'event
				$arrDon = $this->GetDonneeEvents();
		
				$lien= PathWeb.'library/php/stats.php?large=300';
				$lien.='&haut=300';
				$lien.='&titre='.$arrDon["titre"];
				$lien.='&donnees='.$arrDon["donnees"];
				$lien.='&noms='.$arrDon["noms"];
				$lien.='&type=pie';
				$lien.='&col1=yellow';
				$lien.='&col2=red';
				$lien.='&col3=blue';
				$lien.='&col4=black';
				
				$arrResult = array();
				$arrResult["GraphEvent"]=$lien;
		
				//construction des données de primitive
				$arrDon = $this->GetDonneePrimis();
				$lien= PathWeb.'library/php/stats.php?large=300';
				$lien.='&haut=300';
				$lien.='&titre='.$arrDon["titre"];
				$lien.='&donnees='.$arrDon["donnees"];
				$lien.='&noms='.$arrDon["noms"];
				$lien.='&type=pie';
				$lien.='&col1=yellow';
				$lien.='&col2=red';
				$lien.='&col3=blue';
				$lien.='&col4=black';
				
				$arrResult["GraphPrimitive"]=$lien;
		}else{
			
				$lien= PathWeb.'library/php/stats.php?large=400';
				$lien.='&haut=300';
				$lien.='&titre=Erreur';
				$lien.='&donnees='.$arrDon["donnees"];
				$lien.='&noms='.$xml;
				$lien.='&type=pie';
				$lien.='&col1=yellow';
				$lien.='&col2=red';
				$lien.='&col3=blue';
				$lien.='&col4=black';
				
				$arrResult = array();
				$arrResult["GraphEvent"]=$lien;
		
				//construction des données de primitive
				
				$lien= PathWeb.'library/php/stats.php?large=400';
				$lien.='&haut=300';
				$lien.='&titre=Erreur';
				$lien.='&donnees='.$arrDon["donnees"];
				$lien.='&noms='.$xml;
				$lien.='&type=pie';
				$lien.='&col1=yellow';
				$lien.='&col2=red';
				$lien.='&col3=blue';
				$lien.='&col4=black';
			    $arrResult["GraphPrimitive"]=$lien;
		}
		return $arrResult;
		
	   
}
	
	function GetOccuEvents($xml){
		
		//construction des tableaux du nombre d'occurrence
		$Xpath = "//genOp[@layer='event']";
		foreach($xml->xpath($Xpath) as $genOps){
			if($this->trace)
				echo "Sem.php:GetOccuEvents:genOps".print_r($genOps)."<br/>";
			$a = $genOps->attributes();
			foreach ($genOps->children() as $tag=>$val) {
				if(array_key_exists($tag,$this->Events)){
					$this->Events[$tag]=$this->Events[$tag]+1;
				}else{
					$this->Events[$tag]=1;
				}
				if($this->trace)
					echo "Sem.php:GetOccuEvents:tag=".$tag."<br/>";
				
				
				//récupére les paramètres du tag
				$event = $this->xmlEvent->xpath("//event[@compact='".$tag.".']");
				//calcul le tableau des primitives de l'event
				$prims = split($this->StarParam["closing"]["primitive"],$event[0]["integral"]);
				foreach ($prims as $prim) {
					//exclusion des vides
					if($prim!="." && $prim!=".."){
						//construction des occurences
						if(array_key_exists($prim,$this->Primis)){
							$this->Primis[$prim]=$this->Primis[$prim]+1;
						}else{
							$this->Primis[$prim]=1;
						}				
					}
				}
					
			}
		}
		
		
	}

	function AjouteOccu($key,$arr){
		
		if(array_key_exists($key,$arr)){
			$arr[$key]=$arr[$key]+1;
		}else{
			$arr[$key]=1;
		}
		
		return $arr;	
	}
	
	function GetOccuPrimis($xml){
		
		//construction des tableaux du nombre d'occurrence
		$Xpath = "//genOp[@layer='primitive']";
		foreach($xml->xpath($Xpath) as $genOps){
			if($this->trace)
				echo "Sem.php:GetOccuPrimis:genOps".print_r($genOps)."<br/>";
			$a = $genOps->attributes();
			foreach ($genOps->children() as $tag=>$val) {
				$this->Primis = $this->AjouteOccu($tag,$this->Primis);
				//gestion des regroupement de primitive
				if($tag=="O"){
					$this->Primis = $this->AjouteOccu("A",$this->Primis);
					$this->Primis = $this->AjouteOccu("U",$this->Primis);
				}
				if($tag=="M"){
					$this->Primis = $this->AjouteOccu("T",$this->Primis);
					$this->Primis = $this->AjouteOccu("B",$this->Primis);
					$this->Primis = $this->AjouteOccu("S",$this->Primis);
				}
					
				if($this->trace)
					echo "Sem.php:GetOccuPrimis:tag=".$tag."<br/>";						
			}
		}
		
		
	}
	
	
	function GetOccurrence($xml){
		
		$this->Events =array();
		$this->Primis = array();
		
		$this->GetOccuPrimis($xml);
		$this->GetOccuEvents($xml);
		
		
		if($this->trace)
			echo "Sem.php:GetOccurrence:arrEvents=".print_r($this->Events)."<br/>";
		if($this->trace)
			echo "Sem.php:GetOccurrence:arrPrims=".print_r($this->Primis)."<br/>";
				
	}

	function GetDonneeEvents(){
		
		//construction des données de event
		$donnees = "";
		$noms = "";
		$titre = "Events";
		foreach ($this->Events as $tag=>$val) {
			$event = $this->xmlEvent->xpath("//event[@compact='".$tag.".']");
			$noms .= $event[0]["descriptor"].";";
		    $donnees .= $val.";";
		    $codes .= $tag.";";	
		}
		if($noms!=''|| $donnees!=''){
			$ExpIemlXml="<Ieml><noms>".$noms."</noms><donnees>".$donnees."</donnees></Ieml>";
		}
		$file=$titre."_".XmlGraphIeml;
		$this->CreatFileXml($ExpIemlXml,$file);
		return array("noms"=>$noms,"donnees"=>$donnees,"titre"=>$titre,"codes"=>$codes);
		
	}
	
	function GetDonneePrimis(){
		
		//construction des données de event
		$donnees = "";
		$noms = "";
		$titre = "Primitives";
		foreach ($this->Primis as $tag=>$val) {
			if($this->trace)
				echo "Sem.php:GetDonneePrimis:".$tag." ".$val."<br/>";
			$primis = $this->xmlPrimis->xpath("//primitive[@compact='".$tag.".']");
			if($this->trace)
				echo "Sem.php:GetDonneePrimis://primitive[@compact='".$tag.".']<br/>";
			$noms .= $primis[0]["descriptor"].";";
		    $donnees .= $val.";";
		    $tags .= $tag.";";	
		}
		$ExpIemlXml="<Ieml><noms>".$noms."</noms><donnees>".$donnees."</donnees></Ieml>";
		$file=$titre."_".XmlGraphIeml;
		$this->CreatFileXml($ExpIemlXml,$file);
		return array("noms"=>$noms,"donnees"=>$donnees,"titre"=>$titre,"codes"=>$tags);
		
	}
	
	//	Creation de fichier xml pour les traduction 
	function CreatFileXml($xmlTrad,$file_name){
       $file=md5($file_name).".xml";
		if(file_exists(Flux_PATH.$file)){
				unlink(Flux_PATH.$file);
		}
    	$fichier = fopen(Flux_PATH.$file,"w");
	    fwrite($fichier,$xmlTrad);
	    fclose($fichier);
    	
	}
	
	function Parse($code=""){
	
		if($code=="")
			$code=$this->Src;
		$code = stripslashes ($code);
	    //$lien ='https://iemlparser:semantic@www.infoloom.com/cgi-bin/ieml/test2.cgi?iemlExpression='.$code;
	    $lien = 'http://starparser.ieml.org/cgi-bin/test2.cgi?iemlExpression='.$code;
		if($this->trace)
			echo "Sem:Parse:$lien=".$lien."<br/>";
			
	    $oCurl = curl_init($lien);
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
		if($this->trace)
			echo "Sem.php:Parse:sResult".$sResult."<br/>";
		
		
		//nettoie le résultat du parser
		
		$sResult = str_replace("<XMP>","",$sResult);
	    $sResult = str_replace("</XMP>","",$sResult);
	    if(eregi('<(.*)>(.*)<(.*)>',$sResult)){
	    	$sResult = str_replace("<?xml version=\"1.0\"?>"," ",$sResult);
			$xml = simplexml_load_string($sResult);
			  return $xml;
	    }else{
	    	return  $sResult;
	    }
		
	  
	    
	}

	function GetEventListener($id,$params){
			$js = "var v".$id."= document.getElementById('".$id."');";
			$js .= " if(v".$id."){ v".$id.".addEventListener('click', mPressed, true); ";
			foreach($params as $p=>$value)
			{
				$js .= " v".$id.".".$p." = '".$value."';";
			}
			$js .= " }";

		return $js;
	}
	
	function GetChoixNavig($So="", $De="", $Tr="", $NumEtap=-1) {

		
		//cette fonction permet de construire un menu en xul
		// la source du menu est une expression ieml par exemple : l.o.-t.o.-we.b.-'
		// cette expression est le parent (hiérarchie sql) d'une série d'expression ieml 
		//organisées avec les opérateurs d'union, d'intersection et opening
		//union pour la liste des items d'un menu
			//intersection pour la séquence des destinations
				//opening pour le source de chaque destination
		//source = nom de l'item 
		//destination = la source des sous-menus générés dynamiquement
		//chaque parcourt du menu créé une expression ieml par exemple : e.a.-a.e.-a.a.'e.a.-a.e.-a.a.-',e.a.-a.e.-a.a.'e.a.-a.e.-a.a.-',_
		
			
		if($this->trace){
			echo "<br/>";
			echo str_repeat("--", $NumEtap+2);
			echo "GetChoixNavig($So, $De, $Tr, $NumEtap)<br/>";
			echo str_repeat("--", $NumEtap+2);
			echo "close=".$this->StarParam["closing"]["idea"]."\n";
			echo "union=".$this->StarParam["union"]."\n";
			echo "intersection=".$this->StarParam["intersection"]."\n";
			echo "opening=".$this->StarParam["opening"]." ".ord($this->StarParam["opening"])." ".ord("~")."<br/>\n";
		}

		//recupere les expression ieml
		//on prend les enfants de la source
		$Xpath = "/EvalActiSem/Querys/Query[@fonction='Sem-GetChoixNavig-infoSo']";
		$Q = $this->XmlParam->GetElements($Xpath);
		$where = str_replace("-So-", str_replace("'","''",$So), $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;	
		if($this->trace){
			echo str_repeat("--", $NumEtap+2);
			echo "sql = ".$sql."<br/>\n";
		}

		//construction du javascript en attribut
		$code = str_replace("'","\'",$So);
		$desc = str_replace("'","\'",$this->GetDesc($So));
		$trad = str_replace("'","\'",$Tr);
		$id = $this->GetId($So);
		$js = "onclick=\"ShowProc(".$NumEtap.",'".$code."','".$desc."','".$trad."');\"";
		$js="";
		$idObj = $id."_".($NumEtap+1);
		$Params = array("sqlId"=>$id,"code"=>$code,"desc"=>$desc,"trad"=>$trad);
		$jsEvents = $this->GetEventListener($idObj,$Params);
		
		//initialisation du menu 
		$liste.='<menu id="'.$idObj.'" label="'.$desc.'" '.$js.'>';
		$liste.='<menupopup id="file-popup">';
		
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$nb = mysql_num_rows($req);
		while($r = mysql_fetch_assoc($req))
		{
			$sousliste="";
			//construction de la translation
			$trad = $Tr.$So.substr($So, -1, 1);

			//cas du premier appel de la fonction
			if($NumEtap==-1){
				if($this->trace){
					echo str_repeat("--", $NumEtap+2);
					echo "//on construit les paramètres de navigation avec l'expression ieml<br/>\n";
					echo str_repeat("--", $NumEtap+2);
					echo "ieml=".$r['Decode']."<br/>\n";
				}
				$De = split('['.$this->StarParam["union"].']',$r['Decode']);

				if($this->trace){
					echo str_repeat("--", $NumEtap+2);
					print_r($De);
					echo "<br/>".str_repeat("--", $NumEtap+2);
					echo "//chaque item est la sources pour les étapes de navigation<br/>\n";
				}
				$arrR= $this->GetChoixNavig($De[$NumEtap+1], $De, $trad, $NumEtap+1); 
				$sousliste.= $arrR["liste"];
				$jsEvents .= $arrR["js"];
			}else{
				//cas de parcourt de l'arboressence sql
				if($this->trace){
					echo str_repeat("--", $NumEtap+2);
					echo "//on construit la hiérarchie de l'ontologie sql à partir de l'étape de navigation en cours <br/>\n";
					echo str_repeat("--", $NumEtap+2);
					echo "Source=".$r['Decode']."<br/>\n";
				}
				$arrR = $this->GetChoixNavig($r['Decode'], $De, $trad, $NumEtap);
				$sousliste.= $arrR["liste"];
				$jsEvents .= $arrR["js"];

				//dans le cas où il n'y a plus d'enfant sql
				if($sousliste==""){
					if($this->trace){
						echo str_repeat("--", $NumEtap+2);
						echo "//on passe à l'étape de navigation suivante $NumEtap<br/>\n";
						echo str_repeat("--", $NumEtap+2);
						echo "Source=".$De[$NumEtap]."<br/>\n";
					}
					$arrR= $this->GetChoixNavig($De[$NumEtap+1], $De, $trad, $NumEtap+1);
					$sousliste.= $arrR["liste"];
					$jsEvents .= $arrR["js"];
				}
			}

			//construction du javascript
			$code = str_replace("'","\'",$r['Decode']);
			$desc = str_replace("'","\'",$r['Delib']);
			$trad = str_replace("'","\'",$trad);
			$js = "onmouseover=\"ShowProc(".$r['Deid'].",'".$code."','".$desc."','".$trad."');\"";
			$id = $r['Deid'];
			$idObj = $id."_".($NumEtap+1);
			$js="";

			if($sousliste!=""){
				//initialisation du menu 
				$liste.="<menu id=\"".$idObj."\" label=\"".$desc."\" ".$js.">";
				$liste.="<menupopup id=\"file-popup\">";
				$liste.=$sousliste;		
				$liste.= '</menupopup>';
				$liste.= "</menu>";
			}else
				$liste.='<menuitem id="'.$idObj.'" label="'.$desc.'" '.$js.' />';				
			
			//construction du javascript eventlistener
			$Params = array("sqlId"=>$id,"code"=>$code,"desc"=>$desc,"trad"=>$trad);
			$jsEvents .= $this->GetEventListener($idObj,$Params);
			
		}

		$liste.= '</menupopup>';
		$liste.= "</menu>";
		if($nb==0)
			$liste= "";
		
		return array("liste"=>$liste,"js"=>$jsEvents);
	}

		/*récupération du n° de couche suivant le dernier caractère de l'expression IEML
		$Mark = $this->GetMark($end);
		$close = $Mark[0]["closing"];
		$Xpath = "/EvalActiSem/StarIEML/Mark[@layer=".($NumEtap-1)."]";
		$MarkParent = $this->XmlParam->GetElements($Xpath);
		$closeParent = $MarkParent[0]["closing"];
		if($this->trace){
			echo "Sem GetChoixNavig récupère la définition du layer<br/>";
			print_r($Mark);
			echo "<br/>close ".$close."<br/>";
			print_r($MarkParent);
			echo "<br/>closeParent ".$closeParent."<br/>";
			echo "sql = ".$sql."<br/>\n";
		}
		*/		
   function InsertIemlOnto($Iemlcode,$Iemllib,$Imelparent){
 	global $objSite;	
     			$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
		        $db->connect();   
                	// requête pour vérifier l'existence de la traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='AddTrad_Insert_onto_flux']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $values = str_replace("-codeIeml-", $Iemlcode,$Q[0]->values);
                $values = str_replace("-libIeml-", utf8_decode($Iemllib),$values);
                
     			if($Imelparent=="elements"){
                	 $values = str_replace("-nivIeml-", 1 ,$values);
                	  $values = str_replace("-parentIeml-", "elements",$values);
                }
                elseif($Imelparent=="relations"){
                	 $values = str_replace("-nivIeml-", 3 ,$values);
                	 $values = str_replace("-parentIeml-", "relations",$values);
                }elseif($Imelparent=="ideas"){
                	$values = str_replace("-nivIeml-", 5 ,$values);
                	$values = str_replace("-parentIeml-","ideas",$values);
                	
                }elseif($Imelparent=="events"){
                	$values = str_replace("-nivIeml-", 2 ,$values);
                	$values = str_replace("-parentIeml-", "events",$values);
                }elseif($Imelparent=="cycles"){
                	$values = str_replace("-nivIeml-", 4 ,$values);
                	$values = str_replace("-parentIeml-","cycles",$values);
                }

                $sql = $Q[0]->insert.$values;
                $result = $db->query($sql);
                $idieml=mysql_insert_id();
                
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='Insert_ieml_foret']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
               
                $values = str_replace("-idparent", -1,$Q[0]->values);
                $values = str_replace("-idieml-", $idieml,$Q[0]->values);
		     	if($Imelparent=="elements"){
		                	 $values = str_replace("-idparent-",1,$values);
		                }
		                elseif($Imelparent=="relations"){
		                	 $values = str_replace("-idparent-",3,$values);
		                }elseif($Imelparent=="ideas"){
		                	$values = str_replace("-idparent-",5,$values);
		                	
		                }elseif($Imelparent=="events"){
		                	$values = str_replace("-idparent-",2,$values);
		                }elseif($Imelparent=="cycles"){
		                	$values = str_replace("-idparent-",4,$values);
		                }
                echo $sql = $Q[0]->insert.$values;
                $result = $db->query($sql);
                $db->close();
 }
   
   function RecupOntoTrad(){
    	global $objSite;	
    	
   				$result=$this->GetDonneeBdd($objSite,"GetTradUtiSignle",$link);
    			while($reponse=mysql_fetch_assoc($result)){
    				$Trad.=$reponse["ieml_code"]."#;";
    				$Desc.=$reponse["ieml_lib"].";";
    				$Tag.=$reponse["onto_flux_code"].";";
    				$Couche.=$reponse["ieml_parent"].";";
    			}
    			// récupperation des tags qui on plusieurs traduction 
    			$resul=$this->GetDonneeBdd($objSite,"GetTradUtiMulti"); 
   			    while($repons=mysql_fetch_assoc($resul)){
   			    		$MultiCouche=$this->GetDonneeBdd($objSite,"GetTrad",true,$repons["onto_flux_code"],true,$repon["ieml_parent"]);
   			    	    while($rep=mysql_fetch_assoc($MultiCouche)){
   			    	    	// récupération des tags qui ont la même couche ieml
   			    	    	$GetCoucheMulti=$this->GetDonneeBdd($objSite,"GetCouche",true,$repons["onto_flux_code"],true,$rep["ieml_parent"]);
   			    	    	while($Couches=mysql_fetch_assoc($GetCoucheMulti)){
   			    	        	$T.=$Couches["ieml_code"].":";
   			    	        	$D.=$Couches["ieml_lib"].":";
   			    	        }
   			    	        $Tra.=$T."#";
   			    	        $Des.=$D."#";
   			    	        $C.=$rep["ieml_parent"]."#";
   			    	       
   			    	        
   			    	    }
   			            $GetCouche=$this->GetDonneeBdd($objSite,"GetCoucheSignl",true,$repons["onto_flux_code"],true,$rep["ieml_parent"]);
   			    	    while($SCouches=mysql_fetch_assoc($GetCouche)){
   			    	       $Tra.=$SCouches["ieml_code"]."#";
   			    	       $Des.=$SCouches["ieml_lib"]."#";
   			    	       $C.=$SCouches["ieml_parent"]."#";
   			    	    }
   			    	    $Tag.=$repons["onto_flux_code"].";";
   			    	    $Trad.=$Tra.";";
   			    	    $Desc.=$Des.";"; 
   			    	    $Couche.=$C.";";
   			    	    
   			    	}
   			    	
   			    	 
   			    
    			// recuperartion de fichier xml
    			if($this->trace)
    			echo "Sem.php:RecupOntoTrad:file:".$file;
    			$file=Flux_PATH.md5(XmlFlux).".xml";
    			if (file_exists($file)){
					$xml = simplexml_load_file($file);
			    }
    			return $xml->tags."*".$Trad."*".utf8_encode($Desc)."*".utf8_encode($Tag)."*".$Couche;
               
     }   
   
     function GetDonneeBdd($objSite,$function,$getTag=false,$tag="",$getCouche=false,$couche=""){
   		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
		$link=$db->connect();   
        $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='".$function."']";
        $Q = $objSite->XmlParam->GetElements($Xpath);
        $from = str_replace("-iduti-", $_SESSION['iduti'],$Q[0]->from);
        if($getTag){
        	$from = str_replace("-tag-",$tag,$from);
        }
        if($getCouche){
        	$from = str_replace("-couche-",$couche,$from);
        }
        $sql = $Q[0]->select.$from;
      
        $result = $db->query($sql);
        $db->close($link);
        return($result);
   }
     
   function VerifPartageTrad($idTrad,$idUti){
   			
   			//vérifie le partage
	        $Xpath="/XmlParams/XmlParam/Querys/Query[@fonction='VerifPartageTrad']";
		    $Q=$this->site->XmlParam->GetElements($Xpath);
	        $where=str_replace("-idTrad-",addslashes($idTrad),$Q[0]->where);
	        $where=str_replace("-idUti-",addslashes($idUti),$where);
	        $sql=$Q[0]->select.$Q[0]->from." ".$where;
		    $db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			$result = $db->query($sql);
			$db->close();
			$r =  $db->fetch_assoc($result);
			if($r['nb']==0)
				return false;
			else
				return true;
   }

	function GetAutoTradSup($idUti){
		//récupère les traductions automatiques supprimées par l'utilisateur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetTradAutoSup']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idUti-", $idUti, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;	
		//echo $sql."<br/>"; 
		$db = new mysql ($this->site->infos["SQL_HOST"]
			, $this->site->infos["SQL_LOGIN"]
			, $this->site->infos["SQL_PWD"]
			, $this->site->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		return $req;
	
	}
   
	function VerifTradUtiFlux($idUti,$idFlux){
		//récupère les traductions automatiques supprimées par l'utilisateur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='VerifTradUtiFlux']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$from = str_replace("-idUti-", $idUti, $Q[0]->from);
		$where = str_replace("-idFlux-", $idFlux, $Q[0]->where);
		$sql = $Q[0]->select.$from.$where;	
		//echo $sql."<br/>"; 
		$db = new mysql ($this->site->infos["SQL_HOST"]
			, $this->site->infos["SQL_LOGIN"]
			, $this->site->infos["SQL_PWD"]
			, $this->site->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
        $rs=mysql_fetch_array($req);
		if($rs[0]>0)
			return true;
		else
	        return false;
	
	}
	
   function SupPartageTrad($idTrad,$idUti){
   			
   			//vérifie le partage
	        $Xpath="/XmlParams/XmlParam/Querys/Query[@fonction='SupPartageTrad']";
		    $Q=$this->site->XmlParam->GetElements($Xpath);
	        $where=str_replace("-idTrad-",addslashes($idTrad),$Q[0]->where);
	        $where=str_replace("-idUti-",addslashes($idUti),$where);
	        $sql=$Q[0]->delete.$Q[0]->from." ".$where;
		    $db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			$db->query($sql);
            $message = mysql_affected_rows().utf8_encode(" traduction automatique supprimée");
			$db->close();

			return $message;
   }
   
     
   function Add_Trad($libIeml,$codeflux,$codeIeml,$iduti=-1,$getId=false){
   				global $objSite;
   				$Activite= new Acti();
   				
   				if($iduti==-1)
	   				$iduti=$_SESSION['iduti'];
   					   				
   				$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
		        $db->connect();   
		        
		        //recuperation des identifiants ieml_id et ieml_onto_flux
		        
		        $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax_recup_id']";
		        $Q = $objSite->XmlParam->GetElements($Xpath);
		        $where=str_replace("-codeFlux-", utf8_decode($codeflux), $Q[0]->where);
                $where=str_replace("-Iemlcode-",Trim($codeIeml), $where);
                $sql = $Q[0]->select.$Q[0]->from.$where;
                if($this->trace)
                  echo"ExeAjex:AddTrad:sql:$sql ";
        		
                $result = $db->query($sql);
                $res=mysql_fetch_array($result);
                $db->close();
                
                if($res){
                
	                //vérifie que la trad existe
	                $Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='ExeAjax-AddTrad-VerifExist']";
	                $Q = $objSite->XmlParam->GetElements($Xpath);
	                $where=str_replace("-idflux-", $res[0], $Q[0]->where);
	                $where=str_replace("-idIeml-", $res[1],$where);
	                $sql = $Q[0]->select.$Q[0]->from.$where;
	   				$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
			        $db->connect();   
	                $result = $db->query($sql);
	                $db->close();
	                $rs=mysql_fetch_array($result);
	                
	                if(!$rs){
		                // insertion dans la table de traductions des identifiants
		                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-AddTrad-Insert']";
		                $Q = $objSite->XmlParam->GetElements($Xpath);
		                $values=str_replace("-idflux-", $res[0], $Q[0]->values);
		                $values=str_replace("-idIeml-", $res[1],$values);
		                $sql = $Q[0]->insert.$values;
		   				$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				        $db->connect();   
		                $db->query($sql);
			    		$idTrad= mysql_insert_id();
		                $db->close();
			    		
		                //insertion de la traduction dans la table des utilisateurs
		                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ieml_uti_onto']";
		                $Q = $objSite->XmlParam->GetElements($Xpath);
		                $values=str_replace("-idieml-", $res[1],$Q[0]->values);
		                $values=str_replace("-iduti-", $iduti, $values);
		                $sql = $Q[0]->insert.$values;
		   				$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				        $db->connect();   
		                $db->query($sql);
		                $message = "Traduction de '".$codeflux."' en *".utf8_encode($codeIeml."** ajoutée");
		                $db->close();
		                
		                if($iduti==$objSite->infos["UTI_TRAD_AUTO"]){
		                	//insertion du partage de la trad
			                $Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='InsertPartageTrad']";
			                $Q = $objSite->XmlParam->GetElements($Xpath);
			                $values=str_replace("-idTrad-", $idTrad,$Q[0]->values);
			                $values=str_replace("-idUti-", $_SESSION['iduti'], $values);
			                $sql = $Q[0]->insert.$values;
			   				$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
					        $db->connect();   
			                $db->query($sql);
			                $db->close();		                	                	
		                }
	                	
		                $Activite->AddActi("AddTrad",$iduti);
	                
	                }else{
	                	$idTrad = $rs['trad_id'];                
                	}
                }else{
                	return false;
                }
                if($getId)
                	return $idTrad;
                else
	                return $message;
  
   }
   function Sup_Trad($codeIeml,$libIeml,$codeflux){
   	global $objSite;
   	            $Activite= new Acti();
   				$iduti=$_SESSION['iduti'];
   	            $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from=str_replace("-codeFlux-",addslashes(utf8_decode($codeflux)), $Q[0]->from);
                $from=str_replace("-codeIeml-", $codeIeml, $from);
                $from=str_replace("-Iemllib-",addslashes(utf8_decode($libIeml)), $from);
                $sql = $Q[0]->select.$from.$Q[0]->where;
                if($this->trace)
                	echo "Sem:Sup_Trad:sql1=".$sql."<br/>";
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
                $db->connect();
                $result = $db->query($sql);
               	$db->close();
                $res=mysql_fetch_array($result);
                
                if($res){
                //requête pour Supprimer une traduction
	                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_Trad']";
	                $Q = $objSite->XmlParam->GetElements($Xpath);
	                $where = str_replace("-idflux-", $res[0], $Q[0]->where);
	                $where = str_replace("-idIeml-", $res[1], $where);               
	                $sql = $Q[0]->delete.$Q[0]->from.$where;
	                if($this->trace)
	                	echo "Sem:Sup_Trad:sql2=".$sql."<br/>";
                	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	                $db->query($sql);
               		$db->close();
	                
	                //suppression de la traduction de la tableExeAjax-SupTrad-Delete_ieml_uti_onto;
	                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_uti_onto']";
	                $Q = $objSite->XmlParam->GetElements($Xpath);
	                $where = str_replace("-idIeml-", $res[1], $Q[0]->where);
	                $sql = $Q[0]->delete.$Q[0]->from.$where;
	                if($this->trace)
	                	echo "Sem:Sup_Trad:sql3=".$sql."<br/>";
                	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	                $db->query($sql);
               		$db->close();
	                		
	                $message = "Traduction de '".$codeflux."' en *".utf8_encode($codeIeml."** supprimée");
                }else{
	                $message = utf8_encode("Problème lors de la suppression");
                }
                
                $Activite->AddActi("DelTrad",$iduti);
                return $message;
        
   }
   function GetCycle($key){
  	$Xul='';
   	$lien ='http://spreadsheets.google.com/pub?key='.$key;
  	
  	if($this->trace)
			echo "Sem:GetCycle:lien=".$lien."<br/>";
			
	    $oCurl = curl_init($lien);
	    
		// set options
	   // curl_setopt($oCurl, CURLOPT_HEADER, true);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		
		// request URL
		curl_exec($oCurl);
		
		$sResult = curl_exec($oCurl);
		
		// close session
		curl_close($oCurl);
		
		if($this->trace)
			echo "Sem.php:GetCycle:sResult".$sResult."<br/>";
			
		eregi('<TABLE (.*) >(.*)</TABLE>',$sResult, $chaine);
	    eregi('(<BODY (.*) >)(.*)</BODY>',$chaine[0], $Html);
        eregi('(<td (.*) >)',$Html[0], $chaine);
   
        $a=eregi_replace('class=\'[[:alnum:]]* \'|class=\'[[:alpha:]]*\'|<br/>|colspan=[[:digit:]]*|class=hd' ,' ',$chaine[0]);
        $a=eregi_replace('<p style=\'height:[[:digit:]]*px;\'>.</td>' ,'<td>',$a);
        $a=eregi_replace('style=\'width:[[:digit:]]*px;\'|style=\'display:none;\'|style=\'width:[[:digit:]]*;\'' ,'',$a);
        $a=eregi_replace('<td[[:space:]]*>' ,'<td> ',$a);
        $ArrTr=explode("<tr>",$a);
        
        //lien vers le googledoc
        $Xul .= "<vbox flex='1' >";
		$Xul.="<label class='text-link' onclick=\"OuvreLienOnglet('".$lien."');\" value='Consulter le GoogleDoc' />";
		
        //construction de la grille
        $Xul.='<grid id="'.$key.'GridCycle" flex="1" >';
		$cell=explode("<td>",$ArrTr[0]);
		$Xul.='<columns id="cols">';	
		for($j=1;$j<(sizeof($cell)/2);$j++){
			
			$Xul.='<column id="col_'.$j.'" ></column>';
		}
		$Xul.='</columns>';
	    $Xul.='<rows id="'.$key.'CycleRows">';
        for($i=1;$i<sizeof($ArrTr)-1;$i++){
                 $ArrTd=explode("<td>",$ArrTr[$i]);
                 $Xul.='<row id="row_'.$i.'" >';
                 for($j=3;$j<sizeof($ArrTd);$j++){
                 if($i%2!=0){
                 	$Td[$j]=$ArrTd[$j];
                 }else{
                 	if($Td[$j]!=" " && $ArrTd[$j]!=" "){
                 		//récupère le parse de l'expression
           				$xml = $this->Parse(trim($Td[$j]));
						$toolTip = $Td[$j];
						$class = "NoSelect";						
           				if(is_object($xml)){							
							if($this->trace)
								echo "Sem.php:GetCycle:xml".print_r($xml)."<br/>";
							$this->GetOccurrence($xml);
							//récupère les primitives
							$arrDon = $this->GetDonneePrimis();
							$Primis = " primitives='".$arrDon["codes"]."' ";						
							//récupère les events
							$arrDon = $this->GetDonneeEvents();
							$Events = " events='".$arrDon["codes"]."' ";						
							$toolTip .= $Primis.$Events;						
							$error = "";
						}else{
							$Primis = " primitives='' ";						
							$Events = " events='' ";
							$error = $xml;						
							$class = "Error";						
						}
                 		$Xul.='<label id="'.$key.'*'.$Td[$j].'**" '.$Primis.$Events.'   tooltiptext="'.$toolTip.'"  class="'.$class.'" onclick="AfficheIeml(\''.$key.'*'.$Td[$j].'**\') ">'.$ArrTd[$j].' '.$error.' </label>';
                 	   
                 	}else{
                 		$Xul.='<label id="* **"    ></label>';
                 		
                 	}
                 }
                 }
        	 $Xul.='</row>';
            }
        $Xul.='</rows>';
	    $Xul.='</grid>';
        
	    $Xul .= "</vbox>";
	    
	    echo $Xul;
   }
function GetIemlLevel($IemlExp){
	$l=substr($IemlExp,strlen($IemlExp)-1);
	switch ($l){
		case '.' :  
			$level="event";break;
		case '-' :  
			$level="relations";break;
		case '\'': 
			$level="ideas";break;
		case '[a-z]':
			$level="elements";break;
	}
		return $level;
}  
	
}
?>
