<?php

class SauvFlux{
	public $descFlux_Band;
	public $niveauFlux_Band;
	public $parentsFlux_Band;
	public $descFlux;
	public $niveauFlux;
	public $trace;
	private $site;
	
	
	function __construct($objSite){
		
		$this->trace = TRACE;
		
		$this->site=$objSite;
		
		
	}
    
	function aGetAllBundles($objSite,$oDelicious,$iduti){
	 set_time_limit(5000);
		if ($aPosts = $oDelicious->GetAllBundles()) {
		  foreach ($aPosts as $aPost) {
		  	$name.=$aPost['name'].";";
		  	$reponse= $this->VerifFluxExiste($aPost['name']);
		  	if(!$reponse){
			  $idflux= $this->InsertFlux($aPost['name']);
			  $this->flux_uti($iduti,$idflux);
		    }else{
		      $this->flux_uti($iduti,$reponse['onto_flux_id']);
		    }
		    $ArrTags=explode(" ",$aPost["tags"]);
			foreach($ArrTags as $tag){
			   $reponse= $this->VerifFluxExiste($tag);			   
			   if(!$reponse){
				$idflux= $this->InsertFlux($tag);					 		
				$this->flux_uti($iduti,$idflux);
			   }else{
				$this->flux_uti($iduti,$reponse['onto_flux_id']);
			   }
		    } 
		 } 
	   }else {
		 echo $oDelicious->LastErrorString();
	   } 
	  return $name;
	}
	
     function aGetAllTags($objSite,$oDelicious,$oUti,$lang,$getFlux){
     	set_time_limit(9000);
     	$objSem = new Sem($objSite,$objSite->infos["XML_Param"],"");
     	/*verfie s'il y a des nouvelles tags dans le cas où:
     	- le bookmark a été mis à jour
		- il n'y a pas encore de flux enregistré pour l'utilisateur
		- on force la récupération du flux
     	*/
     	if($oDelicious->isUpdatePost() || $objSem->GetUtiOntoFlux($oUti->id)==0 || $getFlux=="true" ){
			$xml='';
			//vérifie si le dictionnaire de l'utilisateur est créé
			$oCacheXml = new Cache("LiveMetal/".$oUti->login,10);
			if(!file_exists($oCacheXml->sFile)){
				$oCacheXml->Set('<IEML><user>'.$oUti->login.'</user></IEML>',true);
			}
			
     		if ($aPosts = $oDelicious->GetAllTags()) { 
		     	foreach ($aPosts as $aPost) { 
			  	  	//vérifie que le tag du flux existe
			  	  	if($aPost['tag']=='tamazight'){
			  	  		$toto = true;	     		
			  	  	}
				    $reponse = $this->VerifFluxExiste($aPost['tag']);			   
					if(!$reponse){
						//ajoute un nouveau tag de flux
					   	$idflux= $this->InsertFlux($aPost['tag']);			   	
						$this->flux_uti($oUti->id,$idflux);
					}else{
						$idflux=$reponse['onto_flux_id'];
						//vérifie si l'utilisateur possède le flux
						$this->flux_uti($oUti->id,$idflux);
					}
					
					//ajoute les traductions automatiques uniquement si c'est demandé par l'utilisateur
	                if($getFlux=="true")
						$xml.=$objSem->AddTradAuto($idflux,$aPost['tag'],"",$lang,1);			   		
					//else	
					//    $xml.=$objSem->AddTradAuto($idflux,$aPost['tag'],"",$lang,-1);	
			  	  } 
			   
		    }else {
			   echo $oDelicious->LastErrorString();
			}	
		}
	}

     function aSetTagsLinks($oDelicious,$oUti){
     	set_time_limit(9000);

     	/*verfie s'il y a des nouvelles tags dans le cas où:
     	- le bookmark a été mis à jour
     	*/
     	if($oDelicious->isUpdatePost() || FORCE_CALCUL){
     		if ($rssTags = $oDelicious->GetUserTags($oUti->login)) { 
		     	$xmlTags = simplexml_load_string($rssTags);
     			foreach ($xmlTags->channel->item as $Tag) { 
					
		     		//vérifie que le tag existe pour l'utilisateur
		     		$idflux = $this->VerifUserFlux($oUti->id,$Tag->title);
		     		
					//récupère les tags liés
		     		if($rssTagsRela = $oDelicious->GetUserTagsRelated($oUti->login, $Tag->title)) { 
				     	$xmlTagsRela = simplexml_load_string($rssTagsRela);
		     			foreach ($xmlTagsRela->channel->item as $TagRela) { 
		     				//vérifie que le tag existe pour l'utilisateur
				     		$idfluxRela = $this->VerifUserFlux($oUti->id, $TagRela->title); 
				     		//vérifie que la relation entre tags existe pour l'utilisateur
				     		$this->VerifUserFluxRela($oUti->id,$idflux,$idfluxRela,$TagRela->description);
				     	}
				    }else {
					   echo $oDelicious->LastErrorString();
					}	
		     	} 
		    }else {
			   echo $oDelicious->LastErrorString();
			}	
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
	
	function flux_uti($uti_id,$flux_id){
		$Xpath=$this->Xpath('Ieml_Uti_Onto_Flux_existe');
		if(!$flux_id){
			$toto=1;
		}
		$Q=$this->site->XmlParam->GetElements($Xpath);
		$where=str_replace("-idflux-",$flux_id,$Q[0]->where);
		$where=str_replace("-iduti-",$uti_id,$where);
		
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$sql=$Q[0]->select.$Q[0]->from.$where;
		$r = $db->query($sql);
		$db->close();
		
		if(mysql_num_rows($r)==0){				   	 
			$db1 = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$Xpath=$this->Xpath('flux_utilisateur');
			$Q=$this->site->XmlParam->GetElements($Xpath);
			$values=str_replace("-iduti-",$uti_id,$Q[0]->values);
			$values=str_replace("-idflux-",$flux_id,$values);
			$sql=$Q[0]->insert.$values;
			$db1->query($sql);
			if($this->trace)
				echo "SaveFlux:flux_uti:login=".$this->site->infos["SQL_LOGIN"]." sql=".$sql."<br/>";
			$db1->close();
		}
		
	}


	function VerifUserFlux($UtiId, $tag){

		//vérifie que le tag du flux existe
	    $reponse = $this->VerifFluxExiste($tag);			   
		if(!$reponse){
			//ajoute un nouveau tag de flux
		   	$idflux= $this->InsertFlux($tag);			   	
		}else{
			$idflux=$reponse['onto_flux_id'];
		}
		//vérifie si l'utilisateur possède le flux
		$this->flux_uti($UtiId,$idflux);
		
	    
	    return $idflux;				
	}

	
	function VerifUserFluxRela($UtiId, $FluxId, $FluxIdRela, $poids){

		//vérifie que la relation existe
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
        $db->connect();
		$sql = "SELECT poids 
	    	FROM ieml_uti_onto_flux_related
	    	WHERE uti_id = $UtiId AND onto_flux_id = $FluxId AND onto_flux_id_rela = $FluxIdRela";
	    $req = $db->query($sql);
		$reponse=mysql_fetch_assoc($req);
	    $db->close();
		if($reponse){
			//vérifie s'il faut mettre à jour le poids
			if($reponse["poids"]!=$poids){
				$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
				$db->connect();
				$sql = "UPDATE ieml_uti_onto_flux_related
					SET poids = $poids)
	    			WHERE uti_id = $UtiId AND onto_flux_id = $FluxId AND onto_flux_id_rela = $FluxIdRela";
	    		$req = $db->query($sql);
			    $db->close();
			}	    	
	    }else{
	    	//ajoute la relation entre tag
	        $db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
	    	$db->connect();
			$sql = "INSERT INTO ieml_uti_onto_flux_related
				(uti_id, onto_flux_id, onto_flux_id_rela, poids)
				VALUES ($UtiId,$FluxId,$FluxIdRela,$poids)";
		    $req = $db->query($sql);
		    $db->close();	    	
	    }
						
	}
	
	
	function InsertFlux($codeFlux){
		$Xpath=$this->Xpath('Ieml_Onto_Flux');
		$Q=$this->site->XmlParam->GetElements($Xpath);
		$value = str_replace("-codeFlux-",addslashes($codeFlux),$Q[0]->values);
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
        $link=$db->connect();
        //$db->query("SET CHARACTER SET 'utf8';", $link)or die(mysql_error());
		$sql = $Q[0]->insert.$value;
	    $req = $db->query($sql);
	    $idflux= mysql_insert_id();
	    $db->close();
	    
	    return $idflux;
				
	}

	
	function VerifFluxExiste($tag){
	   $Xpath=$this->Xpath('Ieml_Onto_existe');
       $Q=$this->site->XmlParam->GetElements($Xpath);        
       $where=str_replace("-tag-",addslashes($tag),$Q[0]->where);
	   $sql=$Q[0]->select.$Q[0]->from." ".$where;
	   $db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
	    $reponse=mysql_fetch_assoc($req);
	    $db->close();	    
	  	return $reponse;
	}
	
	function Xpath($fonction){
	 $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='".$fonction."']";
	 return $Xpath; 
	}
	
	
}
	
	
?>
