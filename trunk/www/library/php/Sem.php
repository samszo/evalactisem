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
			, $this->site->infos["SQL_DB"], $dbOptions);
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
			, $this->site->infos["SQL_DB"], $dbOptions);
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
				$arrPrims =array();
				$arrEvents = array();
				//construction des tableaux du nombre d'occurrence
				foreach($xml->xpath("/ieml/group//genOp[@layer='event']") as $genOps){
					if($this->trace)
						echo "Sem.php:GetSvgPie:genOps".print_r($genOps)."<br/>";
					$a = $genOps->attributes();
					foreach ($genOps->children() as $tag=>$val) {
						if(array_key_exists($tag,$arrEvents)){
							$arrEvents[$tag]=$arrEvents[$tag]+1;
						}else{
							$arrEvents[$tag]=1;
						}
						if($this->trace)
							echo "Sem.php:GetSvgPie:tag=".$tag."<br/>";
						
						//récupére les paramètres du tag
						$event = $this->xmlEvent->xpath("//event[@compact='".$tag.".']");
						//calcul le tableau des éléments
						$prims = split($this->StarParam["closing"]["primitive"],$event[0]["integral"]);
						foreach ($prims as $prim) {
							//exclusion des vides
							if($prim!="." && $prim!=".."){
								//construction des occurences
								if(array_key_exists($prim,$arrPrims)){
									$arrPrims[$prim]=$arrPrims[$prim]+1;
								}else{
									$arrPrims[$prim]=1;
								}				
							}
						}
							
					}
				}
				if($this->trace)
					echo "Sem.php:GetSvgPie:arrEvents=".print_r($arrEvents)."<br/>";
				if($this->trace)
					echo "Sem.php:GetSvgPie:arrPrims=".print_r($arrPrims)."<br/>";
				$this->Events = $arrEvents;	
				$this->Primis = $arrPrims;	
				
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
	
	function GetDonneeEvents(){
		
		//construction des données de event
		$donnees = "";
		$noms = "";
		$titre = "Events";
		foreach ($this->Events as $tag=>$val) {
			$event = $this->xmlEvent->xpath("//event[@compact='".$tag.".']");
			$noms .= $event[0]["descriptor"].";";
		    $donnees .= $val.";";	
		}
		if($noms!=''|| $donnees!=''){
			$ExpIemlXml="<Ieml><noms>".$noms."</noms><donnees>".$donnees."</donnees></Ieml>";
		}
		$file=$titre."_".XmlGraphIeml;
		$this->CreatFileXml($ExpIemlXml,$file);
		return array("noms"=>$noms,"donnees"=>$donnees,"titre"=>$titre);
		
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
		}
		$ExpIemlXml="<Ieml><noms>".$noms."</noms><donnees>".$donnees."</donnees></Ieml>";
		$file=$titre."_".XmlGraphIeml;
		$this->CreatFileXml($ExpIemlXml,$file);
		return array("noms"=>$noms,"donnees"=>$donnees,"titre"=>$titre);
		
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
	    $lien ='https://iemlparser:semantic@www.infoloom.com/cgi-bin/ieml/test2.cgi?iemlExpression='.$code;
		if($this->trace)
			echo "Sem:Parse:code=".$code."<br/>";
			
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
		
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
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
     			$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
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
    	
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		        $db->connect();   
                	// requête pour vérifier l'existence de la traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='Tree_dynamique']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from = str_replace("-iduti-", $_SESSION['iduti'],$Q[0]->from);
                $sql = $Q[0]->select.$from;
               
                $result = $db->query($sql);
                $db->close();
    			while($reponse=mysql_fetch_array($result)){
    				$Trad.=$reponse[1].";";
    				$Desc.=$reponse[2].";";
    				$Tag.=$reponse[0].";";
    			}
    			
    			// recuperartion de fichier xml
    			if($this->trace)
    			echo "Sem.php:RecupOntoTrad:file:".$file;
    			$file=Flux_PATH.md5(XmlFlux).".xml";
    			if (file_exists($file)){
					$xml = simplexml_load_file($file);
			    }
    			
    			return $xml->tags."*".$Trad."*".utf8_encode($Desc)."*".utf8_encode($Tag);
               
     }   
   function Add_Trad($libIeml,$codeflux,$codeIeml){
   	global $objSite;
   				$iduti=$_SESSION['iduti'];
   				$Activite= new Acti();
   	            $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		        $db->connect();   
		        
		        //recuperation des identifiants ieml_id et ieml_onto_flux
		        
		        $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax_recup_id']";
		        $Q = $objSite->XmlParam->GetElements($Xpath);
		        $from=str_replace("-codeFlux-", Trim($codeflux), $Q[0]->from);
                $from=str_replace("-Iemlcode-",Trim($codeIeml), $from);
                $from=str_replace("-iduti-", $iduti, $from);
                $sql = $Q[0]->select.$from;
                if(Trace)
                  echo"ExeAjex:AddTrad:sql:$sql ";
        		
                $result = $db->query($sql);
                $res=mysql_fetch_array($result);
                
                // insertion dans la table de traductions des identifiants
                
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-AddTrad-Insert']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $values=str_replace("-idflux-", $res[0], $Q[0]->values);
                $values=str_replace("-idIeml-", $res[1],$values);
                
                $sql = $Q[0]->insert.$values;
                $result = $db->query($sql);
                $resp=mysql_fetch_array($result);
                
                //insertion de la traduction dans la table des utilisateurs
                
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ieml_uti_onto']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $values=str_replace("-idieml-", $res[1],$Q[0]->values);
                $values=str_replace("-iduti-", $iduti, $values);
                $sql = $Q[0]->insert.$values;
                $result = $db->query($sql);
                $res=mysql_fetch_array($result);
                $message = mysql_affected_rows()." traduction ajoutée";
                $db->close();
                $Activite->AddActi("AddTrad",$iduti);
                return $message;
  
   }
   function Sup_Trad($codeIeml,$libIeml,$codeflux){
   	global $objSite;
   	            $Activite= new Acti();
   				$iduti=$_SESSION['iduti'];
   	            $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from=str_replace("-codeFlux-",$codeflux, $Q[0]->from);
                $from=str_replace("-codeIeml-", $codeIeml, $from);
                $from=str_replace("-Iemllib-",utf8_decode($libIeml), $from);
                echo $sql = $Q[0]->select.$from.$Q[0]->where;
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
               
                $res=mysql_fetch_array($result);
                
                //requête pour Supprimer une traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_Trad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                //echo $Q;
                $where = str_replace("-idflux-", $res[0], $Q[0]->where);
                $where = str_replace("-idIeml-", $res[1], $where);
                
                $sql = $Q[0]->delete.$Q[0]->from.$where;
                $result = $db->query($sql);
                //suppression de la traduction de la tableExeAjax-SupTrad-Delete_ieml_uti_onto;
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_uti_onto']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $sql = $Q[0]->delete.$Q[0]->from.$where;
                $result = $db->query($sql);
                $message = mysql_affected_rows()." traduction supprime";
                $db->close();
                 $Activite->AddActi("DelTrad",$iduti);
                return $message;
        
   }
   function GetCycle(){
  	$Xul='';
   	$lien ='http://spreadsheets.google.com/pub?key=p8PAs8y8e1x3J43Fu2t0bDg';
  	
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
	        $Xul.='<grid  id="GridCycle" style="width:200px"  >';
			$cell=explode("<td>",$ArrTr[0]);
			$Xul.='<columns id="cols">';	
			for($j=1;$j<sizeof($cell)/2;$j++){
				
				$Xul.='<column id="col_'.$j.'" ></column>';
			}
			$Xul.='</columns>';
		    $Xul.='<rows id="CycleRows">';
	        for($i=1;$i<sizeof($ArrTr)-1;$i++){
                 $ArrTd=explode("<td>",$ArrTr[$i]);
                 $Xul.='<row id="row_'.$i.'" >';
                 for($j=3;$j<sizeof($ArrTd);$j++){
                 	if($i%2!=0){
                 		$Td[$j]=$ArrTd[$j];
                 	}else{
                 		if($Td[$j]!=" " && $ArrTd[$j]!=" "){
                 			$Xul.='<label id="*'.$Td[$j].'**" value="'.$ArrTd[$j].'" tooltiptext="'.$ArrTd[$j].'('.$Td[$j].')" crop="end" class="NoSelect" onclick="AfficheIeml(\'*'.$Td[$j].'**\') "  />';
                 		   
                 		}else{
                 			$Xul.='<label id="* **"  value=" " class="NoSelect"  />';
                 			
                 		}
                 	}
                 }
	        	 $Xul.='</row>';
            }
             $Xul.='</rows>';
		     $Xul.='</grid>';
		    echo $Xul;
   }
	
}
?>