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
    public $Sequences;
    public $cache;
    
    
	function __construct($Site, $FicXml, $So, $De="", $Tr="", $cache="") {
		
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
			"U"=>1
			, "A"=>2
			, "S"=>3
			, "B"=>4
			, "T"=>5
			, "E"=>6
			);
			
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

	public function GetExagramme($code){
		
		//parse le code
		/*
		if($this->cache)
			$xml = $this->cache->call('cParse',$code);
		else
*/
			$xml = $this->parse($code);
		
		if(strstr($xml,"ERROR:")){
			return $xml;
		}
		
		$this->Sequences =array();

		//récupère les couches
		$this->GetCouches($xml,$xml->xpath("//category/genOp"));
		
		//initialisation du svg
		$js = "";
		$exa = new Exagramme($this->StarParam); 	
		$exa->ShowSequence($this->Sequences);

	}

	function GetCouches($xml,$couches){
		
		foreach($couches as $c){
			if($c["role"]){
				//récupère le role
				$this->GetSequence($c,$c["layer"],$c["role"]);		
			}
			if($c->children()){
				$this->GetCouches($xml,$c->children());
			}
		}
		
	}
	
	
	function VerifExpIEML($code,$lib){
		
		//vérifie le parse du code ERROR:
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
	
	
	 function AddTradAuto($idFlux,$tag,$lang='fr'){
		foreach($this->LiveMetalRequest($lang,$tag,'getExpression') as $entry){
			//recuppere ieml_lib, level et perent
			$iemlEntry=$this->LiveMetalRequest('ieml',$entry->id,'getEntry');
			$iemlLibEntry=$this->LiveMetalRequest($lang,$entry->id,'getEntry');
			//verfie si le mot Ieml existe dans ieml_onto
			//$rs=$this->site->RequeteSelect('Ieml_Find_Code',"-code-","", $iemlEntry->entry->expression.'',"");
			//if(!$rs)
				$idIeml=$this->AddIemlOnto($iemlEntry->entry->expression.'',$iemlLibEntry->entry->expression.'',$iemlEntry->entry->level.'',$iemlEntry->entry->parent.'');
			//else
				//$idIeml=$rs['ieml_id'];
			$this->Add_Trad("","",$this->site->infos["UTI_TRAD_AUTO"],false,array($idFlux,$idIeml));
		}
	 }
	 
	
	public function GetUsl($Couches,$Trads){
		//construction d'un USL
		$usl="";
		$i=0;
		foreach($Couches as $c){
			if($c!=""){
				$usl .= $this->StarParam["usl"];
				//création de l'expression d'union
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
	
	
	function GetSequence($roles, $layer, $role){

		if(!$roles)
			return;
		
		if($layer="L1"){
			$tag = $roles->children()->getName();
			if($tag && $tag!="genOp"){
				//décompose l'événement
				$event = $this->xmlEvent->xpath("//event[@compact='".$tag.".']");
				//traduit l'événement en exa
				$this->Sequences[]=	array("layer"=>$layer,"role"=>$role,"tag"=>$tag.$this->StarParam["closing"]["event"],"exa"=>$this->GetExaEvent($event));	
			}
		}else{
			foreach($roles as $genOp){
				$tag = $genOp->children()->getName();
				if($tag){
					//décompose l'événement
					$event = $this->xmlEvent->xpath("//event[@compact='".$tag.".']");
					//traduit l'événement en exa
					$this->Sequences[]=	array("layer"=>$layer,"role"=>$role,"tag"=>$tag.$this->StarParam["closing"]["event"],"exa"=>$this->GetExaEvent($event));	
				}
			}
		}

	}
	
	function GetExaEvent($event){
		$i=1;
		if(count($event)>0){
			//récupère le tableau des primitives
			$arrPrimis = split($this->StarParam["closing"]["primitive"],$event[0]["integral"]);
			//récupère la position des primitives
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
	    set_time_limit(1000);
		if($code=="")
			$code=$this->Src;
		$code = stripslashes ($code);
	    $lien = 'http://starparser.ieml.org/cgi-bin/star2xml.cgi?iemlExpression='.$code;
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
	    	$sResult = str_replace('<?xml version="1.0" encoding="UTF-8"?>'," ",$sResult);
			$xml = simplexml_load_string($sResult);
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
            $message = mysql_affected_rows().utf8_encode(" traduction supprimée");
			$db->close();

			return $message;
   }
   
   
   function Add_Trad($codeflux,$codeIeml,$iduti=-1,$getId=false,$res=-1){
   				$objSite = $this->site;
   				$Activite= new Acti();
   				if($iduti==-1)
	   				$iduti=$_SESSION['iduti'];
   				
	   			if($res==-1){	
					//vérifie si le code existe
					$req=$this->site->RequeteSelect('Ieml_Find_Code',"-code-","", $codeIeml,"");
					$rs=mysql_fetch_array($req);
					if(!$rs){
						/*
						$EntryExp=LiveMetalRequest('ieml',$codeIeml,'getEntry');
		                $EntryIeml=LiveMetalRequest('ieml',$EntryExp,'getEntry');
		   				$this->AddIemlOnto();	*/   								
					}
	   				//recuperation des identifiants ieml_id et ieml_onto_flux
		        	$res=mysql_fetch_array($this->RequeteSelect($objSite,'ExeAjax_recup_id','-codeFlux-','-Iemlcode-',utf8_decode($codeflux),Trim($codeIeml) ));
	   			}else{
	   				//les identifiants sont passés en paramètre
	   			}
	   			
	   			if(!$res){
	   				return "ERREUR : la traduction de '".$codeflux."' en *".utf8_encode($codeIeml."** n'a pas été ajoutée");	   				
	   			}
	   			
	            $rs=mysql_fetch_array($this->RequeteSelect($objSite,'ExeAjax-AddTrad-VerifExist',"-idflux-","-idIeml-", $res[0] ,$res[1] ));
                if(!$rs){
	                // insertion dans la table de traductions des identifiants
	                 echo $idTrad=$this->RequeteInsert($objSite,'ExeAjax-AddTrad-Insert',array(array("-idflux-", $res[0]),array("-idIeml-",$res[1])));

                	//vérifie si le code ieml est déjà attribué à l'auteur
              		$verif=mysql_fetch_array($this->RequeteSelect($objSite,'VerifIemlUtiOnto','-IdIeml-','-IdUti-',$res[1],$iduti));
                	if(!$verif){		                	
	                	//insertion de la traduction dans la table des utilisateurs
		                $this->RequeteInsert($objSite,'ieml_uti_onto',array(array("-idieml-", $res[1]),array("-iduti-",$iduti)));		                	
	                }

	                //insertion du partage de la trad pour l'utilisateur
                	$this->RequeteInsert($objSite,'InsertPartageTrad',array(array("-idTrad-", $idTrad),array("-idUti-", $iduti)));
                	
                	//si l'utilisateur est "automatique" on ajoute un partage à l'utilisateur connecté
                	//pour pouvoir supprimer cette traduction par la suite 
                	if($iduti==$this->site->infos["UTI_TRAD_AUTO"]){
                		$this->RequeteInsert($objSite,'InsertPartageTrad',array(array("-idTrad-", $idTrad),array("-idUti-", $_SESSION['iduti'])));
                	}
	                
	                $message = "Traduction de '".$codeflux."' en *".utf8_encode($codeIeml."** ajoutée");
	                
                	$Activite->AddActi("AddTrad",$iduti);
                
                }else{
                	echo $idTrad = $rs['trad_id'];                
                	//vérifie si la traduction est déjà attribué à l'auteur
              		$verif=mysql_fetch_array($this->RequeteSelect($objSite,'VerifPartageTrad','-idTrad-','-idUti-',$idTrad,$_SESSION['iduti']));
                	if($verif["nb"]==0){		                	
	                	//insertion du partage de la trad pour l'utilisateur
                		$this->RequeteInsert($objSite,'InsertPartageTrad',array(array("-idTrad-", $idTrad),array("-idUti-", $_SESSION['iduti'])));
                		$message = "La traduction de '".$codeflux."' en *".utf8_encode($codeIeml."** est ajoutée pour ".$_SESSION['loginSess']);
                	}else{            	
	                	$message = "La traduction de '".$codeflux."' en *".utf8_encode($codeIeml."** existe déjà");
	                }
                }
                
                if($getId)
                	return $idTrad;
                else
	                return $message;
  
   }
  function RequeteSelect($objSite,$function,$var1,$var2,$val1,$val2){
   	 
   	   $Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='".$function."']";
	   $Q = $objSite->XmlParam->GetElements($Xpath);
	   $from=str_replace($var1, $val1, $Q[0]->from);
	   $from=str_replace($var2, $val2, $from);
	   $where=str_replace($var1, $val1, $Q[0]->where);
	   $where=str_replace($var2, $val2,$where);
	   $sql = $Q[0]->select.$from.$where;
	   $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	   $link=$db->connect();   
	   $result = $db->query($sql);
	   $db->close($link);
	   
	   return ($result);
   	
   }
   function RequeteInsert($objSite,$function,$arrVarVal){
   
   	 $Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='".$function."']";
   	 $Q = $objSite->XmlParam->GetElements($Xpath);
   	 $values=$Q[0]->values;
   	 foreach($arrVarVal as $VarVal){
     	$values=str_replace($VarVal[0], $VarVal[1],$values);	
   	 }
     $sql = $Q[0]->insert.$values;
     $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	 $link=$db->connect();   
	 $db->query($sql);
	 $idTrad= mysql_insert_id();
     $db->close($link);
     		return $idTrad;
   	
   }
   function Sup_Trad($codeIeml,$codeflux){
   				$objSite = $this->site;
   				
   	            $Activite= new Acti();
   				$iduti=$_SESSION['iduti'];
   	            $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from=str_replace("-codeFlux-",addslashes(utf8_decode($codeflux)), $Q[0]->from);
                $from=str_replace("-codeIeml-", $codeIeml, $from);
                $sql = $Q[0]->select.$from.$Q[0]->where;
                echo $sql;
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
   
function GetIemlLevel($IemlExp,$getlevel){
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
				//récupère le parse de l'expression
           		$xml = $this->Parse(trim($row['code'.$i]));
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
        	$db->connect();
        	$result = $db->query(utf8_decode($sql));
      	    $db->close();
			$results = array();
			while($data = mysql_fetch_array($result)) {
				$results['lib'][]=utf8_encode($data['onto_flux_code']);
			}
     	}else
   			$results=$this->rechLiveMetal($lang,$query);
     	
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
		if($type=='getEntry')
			$lien="http://evalactisem.ieml.org/entries/".$param."/".$lang;
		else
			$lien="http://evalactisem.ieml.org/searchField/expression/".$param."/".$lang;
		
		$xml = simplexml_load_file($lien);	
			
        
		if($this->trace)
			echo "Sem.php:LiveMetalRequest:sResult".$xml."<br/>";

		$Xpath = "//entry";
		$entry=$xml->xpath($Xpath);
		if($type=='getEntry')
		  return $xml;
		else
		  return $entry;
		
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
	 	$db->connect();   
	 	$db->query($sql);
	 	$id= mysql_insert_id();
	 	$db->close();
     		return $id;
	}
   function rechLiveMetal($lang,$query){
   		$Entrys=$this->LiveMetalRequest($lang,$query,'LN');
   		$results = array();
     	foreach($Entrys as $entry){
     		//$EntrysIeml=$this->LiveMetalRequest('ieml',$entry->id.'','getEntry');
     		$results['lib'][]=$entry->expression.'';
			//$results['niv'][]=$EntrysIeml->entry->parent.'';
			//$results['code'][]=$EntrysIeml->entry->expression.'';
     	}
     	return $results;
   }
 
}
?>
