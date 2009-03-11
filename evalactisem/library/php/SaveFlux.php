<?php

class SauvFlux{
	public $descFlux_Band;
	public $niveauFlux_Band;
	public $parentsFlux_Band;
	public $descFlux;
	public $niveauFlux;
	public $trace;
	
	
	function _construct($desc_Band,$niv_Band,$parent_Band,$desc,$niv){
		
		$this->trace = TRACE;
		
		$this->descFlux_Band=$desc_Band;
		$this->niveauFlux_Band=$niv_Band;
		$this->parentsFlux_Band=$parent_Band;
		$this->descFlux=$desc;
		$this->niveauFlux=$niv;
		
		
	}
    
	function aGetAllBundles($objSite,$oDelicious,$iduti){
	 set_time_limit(5000);
		if ($aPosts = $oDelicious->GetAllBundles()) {
		  foreach ($aPosts as $aPost) {
		  	$name.=$aPost['name'].";";
		  	$reponse= $this->VerifFluxExiste($objSite,$aPost['name']);
		  	if(!$reponse){
			  $idflux= $this->InsertFlux($objSite,$aPost['name']);
			  $this->flux_uti($objSite,$iduti,$idflux);
		    }else{
		      $this->flux_uti($objSite,$iduti,$reponse['onto_flux_id']);
		    }
		    $ArrTags=explode(" ",$aPost["tags"]);
			foreach($ArrTags as $tag){
			   $reponse= $this->VerifFluxExiste($objSite,$tag);			   
			   if(!$reponse){
				$idflux= $this->InsertFlux($objSite,$tag);					 		
				$this->flux_uti($objSite,$iduti,$idflux);
			   }else{
				$this->flux_uti($objSite,$iduti,$reponse['onto_flux_id']);
			   }
		    } 
		 } 
	   }else {
		 echo $oDelicious->LastErrorString();
	   } 
	  return $name;
	}
	
     function aGetAllTags($objSite,$oDelicious,$iduti){
     	set_time_limit(5000);
     	$objSem = new Sem($objSite,$objSite->infos["XML_Param"],"");
     	//verfie s'il y a des nouvelles tags 
     	if($oDelicious->isUpdatePost() || $objSem->GetUtiOntoFlux($iduti)==0){
	     	if ($aPosts = $oDelicious->GetAllTags()) {
		  	print_r($aPosts);  
	     	foreach ($aPosts as $aPost) { 
		  	  	$tag.=$aPost['tag'].";";
		  	  	//vérifie que le tag du flux existe
			    $reponse = $this->VerifFluxExiste($objSite,$aPost['tag']);			   
				if(!$reponse){
					//ajoute un nouveau tag de flux
				   	$idflux= $this->InsertFlux($objSite,$aPost['tag']);			   	
			  	}else{
				   	$idflux= $reponse['onto_flux_id'];					 		
				}
				//ajoute les tarductions dans la table ieml_onto
				//ajoute les traductions automatiques
			   	$reponse = $objSem->AddTradAuto($idflux,$aPost['tag']);			   
				//enregistre le flux pour l'utilisateur
				$this->flux_uti($objSite,$iduti,$idflux);
							
		  	  } 
			   
		     }else {
			   echo $oDelicious->LastErrorString();
			}	
		   return $tag;
		}
	}
	
    function aGetPosts($aPosts){
        foreach ($aPosts as $aPost) { 
  			$xml.="<url";
  			$tag="";
  			foreach($aPost['tags'] as $aTags){
  	    		
  				$tag.=$aTags." ";
  	    	}
  	    	$xml.="  tag='".$tag."'><![CDATA[".$aPost['url']."]]></url>";
  	    }
        	$result="<marque ieml='t.u.-'>$xml</marque>";
	   
     	   
    return $result;
}
 
    function GraphTagBund($objSite,$iduti){
	
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
		$db->connect();
		
		$Xpath =Xpath("repres_graph_flux");
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$from=str_replace("-iduti-",$iduti,$Q[0]->from);
		$sql=$Q[0]->select.$from.$Q[0]->where;
		$reponse = $db->query($sql);
		$db->close();
		while($result=mysql_fetch_assoc($reponse)){
			$parent=explode(";",$result['onto_flux_parents']);
			$count.=(sizeof($parent)-1).";";
			for($i=0;$i<sizeof($parent)-1;$i++){
				$Xpath =$this->Xpath("repres_graph_flux1");
				$where=str_replace("-parent-",$parent[$i],$Q[0]->where);
				$sql=$Q[0]->select.$Q[0]->from.$where;
				$r = $db->query($sql);
				$reponse=mysql_fetch_array($r);
				$parents.=$reponse[0].";";;
			}
			
			//echo $parents."*".$count;

		}
		
		$result="<bundles> $parents </bundles><nbrtag> $count </nbrtag>";
		 
		//return $result;
	
}
	
	function flux_uti($objSite,$uti_id,$flux_id){
		$Xpath=$this->Xpath('Ieml_Uti_Onto_Flux_existe');
		if(!$flux_id){
			$toto=1;
		}
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$where=str_replace("-idflux-",$flux_id,$Q[0]->where);
		$where=str_replace("-iduti-",$uti_id,$where);
		
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
		$db->connect();
		$sql=$Q[0]->select.$Q[0]->from.$where;
		$r = $db->query($sql);
		$db->close();
		
		if(@mysql_num_rows($r)==0){				   	 
			$db1 = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
			$Xpath=$this->Xpath('flux_utilisateur');
			$Q=$objSite->XmlParam->GetElements($Xpath);
			$values=str_replace("-iduti-",$uti_id,$Q[0]->values);
			$values=str_replace("-idflux-",$flux_id,$values);
			$sql=$Q[0]->insert.$values;
			$db1->query($sql);
			if($this->trace)
				echo "SaveFlux:flux_uti:login=".$objSite->infos["SQL_LOGIN"]." sql=".$sql."<br/>";
			$db1->close();
		}
		
	}


	function InsertFlux($objSite,$codeFlux){
		$Xpath=$this->Xpath('Ieml_Onto_Flux');
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$value = str_replace("-codeFlux-",utf8_decode(addslashes($codeFlux)),$Q[0]->values);
			$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
        $db->connect();
		$sql = $Q[0]->insert.$value;
	    $req = $db->query($sql);
	    $idflux= mysql_insert_id();
	    $db->close();
	    
	    return $idflux;
				
	}

	
	function VerifFluxExiste($objSite,$tag){
	   $Xpath=$this->Xpath('Ieml_Onto_existe');
       $Q=$objSite->XmlParam->GetElements($Xpath);        
       $where=str_replace("-tag-",addslashes(utf8_decode($tag)),$Q[0]->where);
	   $sql=$Q[0]->select.$Q[0]->from." ".$where;
	   $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	   $db->connect();
	   $req = $db->query($sql);
	   $reponse=@mysql_fetch_assoc($req);
	   $db->close();	    
	  	return $reponse;
				
	}
		
	function GetTradBdd($objSite,$iduti){
		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='GetTradBdd']";
		$Q=$objSite->XmlParam->GetElements($Xpath);        
        $from=str_replace("-iduti-",$iduti,$Q[0]->from);
	    $sql=$Q[0]->select.$from;
	    $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	    $db->connect();
	    $req = $db->query($sql);
	    $reponse=@mysql_fetch_assoc($req);
	    $db->close();	    
	  	return $reponse;
	}
	function Xpath($fonction){
	 $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='".$fonction."']";
	 return $Xpath; 
}
	
	
}
	
	
?>
