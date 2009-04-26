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
	public $StarParse;
	public $XmlParam;
	private $site;
	private $trace;
    public  $parse;
    public  $Primis;
    public $xmlPrimis;
    public  $Events;
    public $xmlEvent;
    public $Sequences;
    public $cache;
    
    
	function __construct($Site, $FicXml="", $So="", $De="", $Tr="", $cache="") {
		
		$this->trace = TRACE;
		if($FicXml==""){
			$this->XmlParam = $Site->XmlParam;		    
		}

		//$this->parse = $FicXml;
		$this->site = $Site;	
		$this->Src = $So;

		if($cache!="")
			$this->cache = $cache;
		else
			$this->cache = false;
		
		
		$StarParam = $this->site->XmlParam->GetElements("/XmlParams/StarIEML");

		$this->StarParam = array(
			"full"=>$StarParam[0]->Seme[0]["full"]
			, "empty"=>$StarParam[0]->Seme[0]["empty"]
			, "verb"=>$StarParam[0]->Seme[0]["verb"]
			, "noun"=>$StarParam[0]->Seme[0]["noun"]
			, "copy"=>$StarParam[0]->go["copy"]
			, "opening"=>$StarParam[0]->go["opening"]
			, "union"=>$StarParam[0]->slo["union"]
			, "difference"=>$StarParam[0]->slo["difference"]
			, "intersection"=>$StarParam[0]->slo["intersection"]
			, "usl"=>"/"
			, "closing"=> array(
				"seme"=>"_"
				,"phrase"=>","
				,"idea"=>"'"
				,"relation"=>"-"
				,"event"=>"."
				,"primitive"=>":"
				,"L5"=>"_"
				,"L4"=>","
				,"L3"=>"'"
				,"L2"=>"-"
				,"L1"=>"."
				,"L0"=>":"
				)
			, "niveau"=> array(
				"seme"=>6
				,"phrase"=>5
				,"idea"=>4
				,"relation"=>3
				,"event"=>2
				,"primitive"=>1
				)
			);
		//print_r($this->StarParam);
		$this->ExaParam = array(
			"T"=>1
			, "S"=>2
			, "B"=>3
			, "A"=>4
			, "U"=>5
			, "E"=>6
			);
			
		//charge les param�tres des layers
		if (file_exists(PathRoot."/param/events.xml")) {
		    $this->xmlEvent = simplexml_load_file(PathRoot."/param/events.xml");
		} else {
		    exit('Echec lors de l\'ouverture du fichier events.xml.');
		}
		if (file_exists(PathRoot."/param/primitives.xml")) {
		    $this->xmlPrimis = simplexml_load_file(PathRoot."/param/primitives.xml");
		} else {
		    exit('Echec lors de l\'ouverture du fichier primitives.xml.');
		}
		
				
	}

	public function GetExagramme($code){
		
		//parse le code
		$oCacheXml = new Cache("StarParser/".$this->site->strtokey($code),CACHETIME);
		if(!$oCacheXml->Check()){
			$xml = $this->parse($code,false);
			$oCacheXml->Set($xml,true);			
		}else{
			$xml = $oCacheXml->Get(true);
		}
		
		if(strstr($xml,"ERROR:")){
			return $xml;
		}
    	$this->StarParse= simplexml_load_string($xml);
		
    	/*	
		//calcule un tableau des couches à partir de l'ancienne version du parser
		$this->Sequences =array();
		$this->GetCouches($this->StarParse->children());
		$exa->ShowSequence($this->Sequences);
 		*/
    			
		//initialisation du svg
		$exa = new Exagramme($this); 	
		$exa->GetSequence();

	}

	
	
	function GetCouches($couches){
		
		foreach($couches as $c){
			$primis = $c->xpath("//genOpAtL0");
			$this->GetSequence($primis);		
		}
		
	}
		
	
	function VerifExpIEML($code,$lib){
		
		//v�rifie le parse du code ERROR:
		$xml = $this->Parse($code);
		if(strstr($xml,"ERROR:")){
			return $xml;
		}else{
			//ajoute la nouvelle expression
			$arrVarVal = array(array("-idflux-",$res[0]),array("-idIeml-",$res[1]));
            //$idTrad=$this->site->RequeteInsert('AddTrad_Insert_onto_flux',$arrVarVal);
		}
		return "OK";
		
	}
	
	 function AddTradAuto($idFlux,$tag,$libIeml,$lang,$insAddTrad=-1){ 
	 	set_time_limit(9000);
	 	$xml="";
	 	$Entrys=$this->LiveMetalRequestAll($tag,'getExpression');

 	   	$Xpath = "//entry[@lang='".$lang."']";
    	foreach($Entrys->xpath($Xpath) as $entry){
			$iemlEntry=$this->LiveMetalRequest('ieml',$entry->id,'getEntry');
			$xml.="<entry lang='".$lang."' id='".$iemlEntry->entry->id."' >";
			$xml.="<iemlCode>".$iemlEntry->entry->expression."</iemlCode>";
			$xml.="<iemlLib>".$entry->expression."</iemlLib>";
			$xml.="<iemlLevel>".$iemlEntry->entry->level."</iemlLevel>";
			$xml.="<iemlParent>".$iemlEntry->entry->parent."</iemlParent>";
			$xml.="</entry>";
		 	if($insAddTrad==-1){
				$this->Add_Trad("","","",$this->site->infos["UTI_TRAD_AUTO"],false,array($idFlux,$iemlEntry->entry->id),$lang);
		 	}else{
		 		$oCacheXml = new Cache("LiveMetal/".$_SESSION['loginSess'], CACHETIME);
		 		$xmlString=simplexml_load_string($oCacheXml->Get(true));
		 		$Xpath = "//entry[@id='".$iemlEntry->entry->id."'][@lang='".$lang."']";
		 		$Entrys=$xmlString->xpath($Xpath);
		 		if(sizeof($Entrys)==0){
		 			$xml=str_replace('</Ieml>',$xml,$xmlString.'');
		 		}
		 		if(!$this->VerifTradGetFlux($idFlux,$iemlEntry->entry->id,array($this->site->infos["UTI_TRAD_AUTO"],$_SESSION['iduti']))){
		 			$this->Add_Trad("","","",$this->site->infos["UTI_TRAD_AUTO"],false,array($idFlux,$iemlEntry->entry->id),$lang);
		 		}
		 	}
		 }

		return $xml;  
	 	
	}
	 
	public function GetUsl($Couches,$Trads){
		//construction d'un USL
		$usl="";
		$i=0;
		foreach($Couches as $c){
			if($c!=""){
				$usl .= $this->StarParam["usl"];
				//cr�ation de l'expression d'union
				$arrTrad = split(",",$Trads[$i]);
				$usl .= "(";
				foreach($arrTrad as $t){
					if($t!="")
						$usl .= $t.$this->StarParam["union"];
				}
				$usl = substr($usl,0,-1);
				$usl .= ")";
			}
			$i++;
		}
		return $usl;
	}
	
	


	function Parser_Ieml_Exp($code){
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
				
				//construction des donn�es de l'event
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
		
				//construction des donn�es de primitive
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
		
				//construction des donn�es de primitive
				
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
		$Xpath = "//genOp[@layer='L1']";
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
		$Xpath = "//genOp[@layer='L0']";
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
	
	function GetEventPrimis($tag){
		//décompose l'évenement
		$event = $this->xmlEvent->xpath("//event[@compact='".$tag.".']");
		if(count($event)>0){
			//récupère le tableau des primitives
			$arrPrimis = split($this->StarParam["closing"]["L0"],$event[0]["integral"]);
			return $arrPrimis;
		}		
	}
	
	function GetSequence($roles){

		if(!$roles)
			return;
		
		$i=0;
		$j=0;
		$arrL0=array();
		foreach($roles as $primis){
			//crée le tableau des primitives	
			if($i==3){
				$this->GetExaPrimi($arrL0, $primis, $j);				
				$arrL0=array();
				$i=0;
				$j++;
			}
			$arrL0[$i]=$primis["symbol"]."";
			$i++;
		}

	}
	

	function GetExaPrimi($arrPrimis, $layer, $role){
		if(count($arrPrimis)>0){
			//récupére la position des primitives
			$j=0;
			foreach($arrPrimis as $primi){
				$arrExa = array();
				if(isset($this->ExaParam[$primi])){
					$posi = $this->ExaParam[$primi];
					for($i=1; $i<=6; $i++) {
						if($posi==$i){
							$arrExa[]=true;		
						}else{
							$arrExa[]=false;						
						}
					}
					//traduit en exagramme
					$this->Sequences[]=	array("layer"=>$layer,"role"=>$role,"tag"=>$primi.$this->StarParam["closing"]["L0"],"exa"=>$arrExa);	
				}
				$j++;
			}
			//complète la séquence par des vides
			for($i=$j; $i<=3; $i++) {
				$arrExa = array(false,false,false,false,false,true);
				//traduit en exagramme
				$this->Sequences[]=	array("layer"=>$layer,"role"=>$role,"tag"=>"E".$this->StarParam["closing"]["L0"],"exa"=>$arrExa);					
			}
		}else{
			//cas empty
			$arrExa = array(false,false,false,false,false,true);
			//traduit en exagramme
			$this->Sequences[]=	array("layer"=>$layer,"role"=>$role,"tag"=>"E".$this->StarParam["closing"]["L0"],"exa"=>$arrExa);	
		}
	}
	
	function GetExaEvent($event){
		$i=1;
		if(count($event)>0){
			//récupére le tableau des primitives
			$arrPrimis = split($this->StarParam["closing"]["primitive"],$event[0]["integral"]);
			//récupére la position des primitives
			foreach($arrPrimis as $primi){
				if($primi!=$this->StarParam["closing"]["event"])
					$posis[] = $this->ExaParam[$primi];			
			}
			//tri les positions
			sort($posis);
			//construit les données de l'exagramme
			$arrExa = array();
			foreach($posis as $posi){
				if($posi==$i){
					$arrExa[]=true;		
					$i++;
				}else{
					for($j=$i; $j<$posi; $j++) {
						$arrExa[]=false;						
					}
					$arrExa[]=true;						
					$i=$j+1;
				}
			}
			for($j=$i; $j<=6; $j++) {
				$arrExa[]=false;						
			}
		}else{
			//cas empty
			$arrExa = array(false,false,false,false,false,true);
		}
		
		return $arrExa;
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
		
		//construction des donn�es de event
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
		if(file_exists(PATH_FILE_FLUX.$file)){
				unlink(PATH_FILE_FLUX.$file);
		}
    	$fichier = fopen(PATH_FILE_FLUX.$file,"w");
	    fwrite($fichier,$xmlTrad);
	    fclose($fichier);
    	
	}
	
	function Parse($code="",$GetObjet=true){
	    set_time_limit(1000);
		if($code=="")
			$code=$this->Src;
		$code = stripslashes ($code);
	    $lien = PATH_STAR_PARSER.$code;
		if($this->trace)
			echo "Sem:Parse:$lien=".$lien."<br/>";
			
	    $oCurl = curl_init($lien);
		// set options
	    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		
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
	    	$xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>'," ",$sResult);
			if($GetObjet)
	    		$xml = simplexml_load_string($xml);
			  return $xml;
	    }else{
	    	return  $sResult;
	    }
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
   
   function VerifPartageTradAuto($idTradAuto,$idUti){
   			
   			//vérifie le partage
	        $Xpath="/XmlParams/XmlParam/Querys/Query[@fonction='VerifPartageTradAuto']";
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
		//récupére les traductions automatiques supprimées par l'utilisateur
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
		//récupére les traductions automatiques supprim�es par l'utilisateur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='VerifTradUtiFlux']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$from = str_replace("-idUti-", $idUti, $Q[0]->from);
		$where = str_replace("-idFlux-", $idFlux, $Q[0]->where);
		$sql = $Q[0]->select.$from.$where;	
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
            $message = mysql_affected_rows()." traduction supprimée";
			$db->close();

			return $message;
   }

   function GetIemlTrad($login,$tag){
   	
		$rs = $this->site->RequeteSelect('GetTagTradUti',array(array('-tag-',$tag),array('-login-',$login)));
		$usl="";
		while($r=mysql_fetch_assoc($rs)){
			$usl .= $this->StarParam["usl"]."(".$r["ieml_code"].")";
		}
		return $usl;
   	
   }
   
   function Add_Trad($codeflux,$codeIeml,$libIeml,$iduti=-1,$getId=false,$res=-1,$lang=''){
   				$objSite = $this->site;
   				$Activite= new Acti();
	   			$sem = new Sem($this->site,$this->site->infos["XML_Param"],"");
   				
	   			if($iduti==-1)
	   				$iduti=$_SESSION['iduti'];
	   			if($res==-1){	
	   				//recuperation des identifiants ieml_id et ieml_onto_flux
	   				$res= array();
	   				$EntryExp=$this->LiveMetalRequest('ieml',trim($codeIeml),'getId');
					$idF=mysql_fetch_array($this->site->RequeteSelect('ExeAjax_recup_id',array(array('-codeFlux-',$codeflux),array('--',' '))));
		        	$res[0]=$idF['onto_flux_id'];
	   			    $res[1]=$EntryExp->entry->id;
	   			    
	   			    //vérifie si l'expression ieml est dans le dictionnaire de l'utilisateur
	   			    $oCacheXml = new Cache("LiveMetal/".$_SESSION['loginSess'], $iCacheTime=10);
					$xml= simplexml_load_string($oCacheXml->Get(true));
					$Xpath = "//entry[@id='".$EntryExp->entry->id."']";
   					$entry=$xml->xpath($Xpath);
   					if(!$entry[0]['id']){
   						//enregistre la nouvelle expression dans le dictionnaire de l'utilisateur
   						$noeud ="<entry lang='".$lang."' id='".$EntryExp->entry->id."' >";
						$noeud.="<iemlCode>".$EntryExp->entry->expression."</iemlCode>";
						$noeud.="<iemlLib>".$libIeml."</iemlLib>";
						$noeud.="<iemlLevel>".$EntryExp->entry->level."</iemlLevel>";
						$noeud.="<iemlParent>".$EntryExp->entry->parent."</iemlParent>";
						$noeud.="</entry>";
						$noeud.='</Ieml>';
						$xmlString=$oCacheXml->Get(true);
						$xmlString=str_replace('</Ieml>',$noeud,$xmlString);
						$oCacheXml->SET($xmlString,true);
   					}
   						
	   			}
	   			//vérifie si la traduction existe
	            $rs=mysql_fetch_array($this->site->RequeteSelect('ExeAjax-AddTrad-VerifExist'
	            	,array(array("-idflux-",$res[0]),array("-idIeml-", $res[1]))
	            	));
	            if(!$rs){
	                // insertion dans la table de traductions des identifiants
	                $idTrad=$this->site->RequeteInsert('ExeAjax-AddTrad-Insert',array(array("-idflux-", $res[0]),array("-idIeml-",$res[1])));
                     
                	//vérifie si le code ieml est déjà attribué à l'auteur
              		$verif=mysql_fetch_array($this->site->RequeteSelect('VerifIemlUtiOnto'
              		,array(array('-IdIeml-',$res[1]),array('-IdUti-',$iduti))
              		));
                	if(!$verif){		                	
	                	//insertion de la traduction dans la table des utilisateurs
		                $this->site->RequeteInsert('ieml_uti_onto',array(array("-idieml-", $res[1]),array("-iduti-",$iduti)));		                	
	                }

	                //insertion du partage de la trad pour l'utilisateur
                	$this->site->RequeteInsert('InsertPartageTrad',array(array("-idTrad-", $idTrad),array("-idUti-", $iduti)));
                	
                	//si l'utilisateur est "automatique" on ajoute un partage à l'utilisateur connecté
                	//pour pouvoir supprimer cette traduction par la suite 
                	if($iduti==$this->site->infos["UTI_TRAD_AUTO"]){
                		$this->site->RequeteInsert('InsertPartageTrad',array(array("-idTrad-", $idTrad),array("-idUti-", $_SESSION['iduti'])));
                	}
	                
	                $message = "Traduction de '".$codeflux."' en *".$codeIeml."** ajoutée";
	                
                	$Activite->AddActi("AddTrad",$iduti);
                
                }else{
                	$idTrad = $rs['trad_id'];                
                	//vérifie si la traduction est déjà attribué à l'auteur
              		$verif=mysql_fetch_array($this->site->RequeteSelect('VerifPartageTrad'
              			,array(array('-idTrad-',$idTrad),array('-idUti-',$_SESSION['iduti']))
              			));
                	if($verif["nb"]==0){		                	
	                	//insertion du partage de la trad pour l'utilisateur
                		$this->RequeteInsert('InsertPartageTrad',array(array("-idTrad-", $idTrad),array("-idUti-", $_SESSION['iduti'])));
                		$message = "La traduction de '".$codeflux."' en *".$codeIeml."** est ajoutée pour ".$_SESSION['loginSess'];
                	}else{            	
	                	$message = "La traduction de '".$codeflux."' en *".$codeIeml."** existe déjà";
	                }
                }
                
                if($getId)
                	return $idTrad;
                else
	                return $message;
  
   }

   function Sup_Trad($codeIeml,$codeflux){
   				$objSite = $this->site;
   				
   	            $Activite= new Acti();
   				$iduti=$_SESSION['iduti'];
   	            $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $where=str_replace("-codeFlux-",addslashes($codeflux), $Q[0]->where);;
                $sql = $Q[0]->select.$Q[0]->from.$where;
                if($this->trace)
                	echo "Sem:Sup_Trad:sql1=".$sql."<br/>";
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
                $link=$db->connect();
                //$db->query("SET CHARACTER SET 'utf8';", $link)or die(mysql_error());
                $result = $db->query($sql);
               	$db->close();
                $res=mysql_fetch_array($result);
                //recuppere l'ieml_id
                $EntryIeml=$this->LiveMetalRequest("ieml",$codeIeml,'getId');
                if($res){
                	$res[1]=$EntryIeml->entry->id;
                //requete pour Supprimer une traduction
	                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_Trad']";
	                $Q = $objSite->XmlParam->GetElements($Xpath);
	                $where = str_replace("-idflux-", $res[0], $Q[0]->where);
	                $where = str_replace("-idIeml-", $res[1], $where);               
	                $sql = $Q[0]->delete.$Q[0]->from.$where;
	                if($this->trace)
	                	echo "Sem:Sup_Trad:sql2=".$sql."<br/>";
                	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
                    //$db->query("SET CHARACTER SET 'utf8';", $link)or die(mysql_error());
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
	                		
	                $message = "Traduction de '".$codeflux."' en *".$codeIeml."** supprimée";
                }else{
	                $message ="Problème lors de la suppression";
                }
                
                $Activite->AddActi("DelTrad",$iduti);
                return $message;
        
   }
		function VerifTradGetFlux($idFlux,$idIeml,$ArridUti){
			    $objSite = $this->site;
			    $exist=false;
				//verfie si la trad existe
				foreach($ArridUti as $idUti){
				    $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='VerifExistTrad']";
	                $Q = $objSite->XmlParam->GetElements($Xpath);
	                $from = str_replace("-idIeml-", $idIeml, $Q[0]->from);
	                $from = str_replace("-idUti-", $idUti, $from);
	                $where = str_replace("-idFlux-", $idFlux, $Q[0]->where);           
	                $sql = $Q[0]->select.$from.$where;
	                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	                //$db->query("SET CHARACTER SET 'utf8'; ");
	                $result=$db->query($sql);
	               	$db->close();
	                $res=mysql_fetch_array($result);
	                if($res)
	                	$exist=true;
				}
		 return $exist;	
		}
			
function GetIemlLevel($IemlExp,$getlevel=true){
	$l=substr($IemlExp,strlen($IemlExp)-1);
	foreach($this->StarParam["closing"] as $level=>$close){
		if($l==$close){
			$niv = $this->StarParam["closing"][$level];
			$lvl = $level;
		}
	}
	if($getlevel)
		return $lvl;
	else
		return $niv;
}  
function Crea_Cycle($json){
	$json=eregi_replace('(%20)*',$json,'');
	$Tab=json_decode(stripslashes($json),true);
	$html="<table border='1' id='".$Tab[0]['key']."CycleRows'>";
	foreach($Tab as $row){
		$html.="<tr>";
		for($i =0; $i < (sizeof($row)/2)-1; $i++){
			$html.="<td>";
			if($row['code'.$i]!= "vide"){
				//r�cup�re le parse de l'expression
           		$xml = $this->Parse(trim($row['code'.$i]));
				$class = "NoSelect";						
           		if(is_object($xml)){							
					if($this->trace)
						echo "Sem.php:GetCycle:xml".print_r($xml)."<br/>";
						$this->GetOccurrence($xml);
						//récupére les primitives
						$arrDon = $this->GetDonneePrimis();
						$Primis = " primitives='".$arrDon["codes"]."' ";						
						//récupére les events
						$arrDon = $this->GetDonneeEvents();
						$Events = " events='".$arrDon["codes"]."' ";						
						$title .= $Primis.$Events;						
						$error = "";
						$title=$row['code'.$i];
					}else{
						$Primis = " primitives='' ";						
						$Events = " events='' ";
						$error = $xml;						
						$class = "Error";
						$title=	$xml;					
					}
					$html.='<a href="#" id="'.$key.'*'.$row['code'.$i].'**" '.$Primis.$Events.' class="'.$class.'" title="'.$title.'" onclick="AfficheIeml(\''.$key.'*'.$row['code'.$i].'**\') " >';
					$html.=$row['descp'.$i]."</a>";
					
           	}else{
           		$html.='<a href="#" id="* **"  primitives="" events="" class="NoSelect" />';
					
           	}
           $html.="</td>";
		}
		$html.="</tr>";	
	}
	$html.="</table>";
	echo $html;
}
function recherche($query,$type,$IdUti,$lang){
		$objSite = $this->site;
		if($type=='tag'){
     		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTree']/Querys/Query[@fonction='ExeAjax_recherche_".$type."']";
     	    $Q = $objSite->XmlParam->GetElements($Xpath);
        	$from=str_replace("-iduti-",$IdUti, $Q[0]->from);
        	$where = str_replace("-query-",$query , $Q[0]->where);               
	    	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
        	$sql = $Q[0]->select.$from.$where; 
        	$link=$db->connect();
        	//$db->query("SET CHARACTER SET 'utf8';", $link)or die(mysql_error());
        	$result = $db->query($sql);
      	    $db->close();
			$results = array();
			while($data = mysql_fetch_array($result)) {
				$results['lib'][]=$data['onto_flux_code'];
			}
     	}else{
   			$results=$this->rechLiveMetal($lang,$query);
     	}
     	
     	$json = json_encode($results);
     	return $json;
       }
	
	function Evalactisem ($oDelicious,$login,$mdp){
		// connexion a delicious
		global $con;
		if(TRACE)
			echo "Sem:Evalactisem:login:".$login." mdp=".$mdp."<br/>";
			$_SESSION['loginSess']=$login;
			$_SESSION['mdpSess']=$mdp;
			$_SESSION['Delicious']=$oDelicious;
			if(TRACE)
				echo "ParamPage:Debug:oDelicious=".$oDelicious->sUsername."<br/>";
			$oDelicious->DeliciousRequest('posts/delete', array('url' => $sUrl));
			$con=$oDelicious->LastError();
			return $con;
		
	}
	
	function GetUtiOntoFlux($idUti){
		$objSite = $this->site;
     	$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTree']/Querys/Query[@fonction='GetUtiOntoFlux']";
     	$Q = $objSite->XmlParam->GetElements($Xpath);
     	$where = str_replace("-idUti-",$idUti , $Q[0]->where);               
	    $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
        $sql = $Q[0]->select.$Q[0]->from.$where;      
        $db->connect();
        $req = $db->query($sql);
        return mysql_num_rows($req);
	}
	
	function LiveMetalRequest($lang,$param,$type){

		$objSite = $this->site;
		$oCacheXml = new Cache("LiveMetal/".$type."_".$this->site->strtokey($param)."_".$lang,CACHETIME);
		if(!$oCacheXml->Check()){
			switch ($type) {
				case 'getEntry':
					$lien= $objSite->infos["PATH_LiveMetal"]."/entries/".$param."/".$lang;
					break;
				case 'getEntryRech':
					$lien= $objSite->infos["PATH_LiveMetal"]."/entries/".$param."/".$lang;
					break;
				case 'LikeRech':
			 		$lien= $objSite->infos["PATH_LiveMetal"]."/searchField/expression/".$param."/".$lang."/start";
					break;
				case 'getId':
			 		$lien= $objSite->infos["PATH_LiveMetal"]."/search/".$param;
					break;
				default:
					$lien= $objSite->infos["PATH_LiveMetal"]."/searchField/expression/".$param."/".$lang;
					break;
			}
			
			if($this->trace)
				echo "Sem.php:LiveMetalRequest:lien".$lien."<br/>";

			$sResult = $this->site->GetCurl($lien);      

			//nettoie le résultat du parser
			$sResult = $this->cleanResult($sResult);
						
			$oCacheXml->Set($sResult);			
		}else{
			$sResult = $oCacheXml->Get();
		}
		$xml = simplexml_load_string($sResult);

		switch ($type) {
			case 'getEntry':
				$result = $xml;
				break;
			case 'getId':
				$result = $xml;
				break;
			case 'getEntryRech':
				$result = $xml->entry->expression.' ';
				break;
			default:
				$Xpath = "//entry[@lang='".$lang."']";		
				$result = $xml->xpath($Xpath);
		 		break;
		}		

		return $result;
		
	}
	function LiveMetalRequestAll($param,$type){
		
		$oCacheXml = new Cache("LiveMetal/".$type."_".$param,CACHETIME);
		if(!$oCacheXml->Check()){
			if($type=='getEntryAll')
				$lien=$this->site->infos["PATH_LiveMetal"]."/entries/".$param."/all";
			else
				$lien=$this->site->infos["PATH_LiveMetal"]."/searchField/expression/".$param."/all";			
			$xml = $this->site->GetCurl($lien);      
			$oCacheXml->Set($xml);			
		}else{
			$xml = $oCacheXml->Get();
		}
		$oXml = simplexml_load_string($xml);      
		return $oXml;
	}
	
	function AddIemlOnto($iemlCode,$iemlLib,$iemlNiv,$iemlParent){
		$objSite = $this->site;
     	$Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='InsertIemlOnto']";
     	$Q = $objSite->XmlParam->GetElements($Xpath);
     	$values=str_replace('-iemlCode-',addslashes($iemlCode),$Q[0]->values);
     	$values=str_replace('-iemlLib-',utf8_decode($iemlLib),$values);
     	$values=str_replace('-iemlNiv-',$iemlNiv,$values);
     	$values=str_replace('-iemlParent-',$iemlParent,$values);
     	$sql = $Q[0]->insert.$values;
     	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	 	$link=$db->connect();   
	 	//$db->query("SET CHARACTER SET 'utf8';", $link)or die(mysql_error());
	 	$db->query($sql);
	 	$id= mysql_insert_id();
	 	$db->close();
     		return $id;
	}
   function rechLiveMetal($lang,$query){
   		$Entrys=$this->LiveMetalRequest($lang,$query,'LikeRech');
   		$results = array();
     	foreach($Entrys as $entry){
     		$results['lib'][]=$entry->expression.'';
     		$results['id'][]=$entry->id.'';
		}
     	return $results;
   }
   function getLangLiveMetal(){
   	    $lien = $this->site->infos["PATH_LiveMetal"]."/languages";
		$sResult = $this->site->GetCurl($lien);      
		if($this->trace)
			echo "Sem.php:Parse:sResult".$sResult."<br/>";
				
		//nettoie le résultat du parser
		$sResult = $this->cleanResult($sResult);

		$xml = simplexml_load_string($sResult);	
		$Xpath = "//language";
		$Entrys=$xml->xpath($Xpath);
		$ArrLang=array();
		foreach($Entrys as $entry){
			$ArrLang[]=$entry->code.'';
		}
		return json_encode($ArrLang);
   }
   function cleanResult($sResult){
		//nettoie le résultat du parser
		$sResult = str_replace('<?xml version="1.0" encoding="utf-8"?>',"",$sResult);
	    $sResult = str_replace('<!DOCTYPE wikimetal SYSTEM "'.$this->site->infos["PATH_LiveMetal"].'/livemetal.dtd">',"",$sResult);
   		return $sResult;
   }
   
}
?>
