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

    function aSetTagLinks($oDelicious, $oUti, $tag, $niv=0){
    	
     	// end start benchmark
     	$start = microtime(); 
     	
    	/*requêtes utiles
    	 * le nombre de liens total, le nombre de tag lié aux niveaux 0 et 1 pour un utilisateur 
SELECT count(*) nbTot, count(distinct n0.onto_flux_id_rela) nb0, count(distinct n1.onto_flux_id_rela) nb1
FROM `ieml_uti_onto_flux_related` n0
INNER JOIN ieml_uti_onto_flux_related n1 ON n1.onto_flux_id = n0.onto_flux_id_rela AND n1.uti_id = n0.uti_id 
WHERE n0.uti_id = 32 AND n0.onto_flux_id = 551
    	 */
    	
     	//vérifie que le tag existe pour l'utilisateur
     	$idflux = $this->VerifUserFlux($oUti->id,$tag);
	    
     	//ajoute les posts
		$this->aSetTagsPosts($oDelicious,$oUti,$tag);
		
		//récupère les tags liés
     	if($rssTagsRela = $oDelicious->GetUserTagsRelated($oUti->login, $tag)) { 
	     	$xmlTagsRela = simplexml_load_string($rssTagsRela);
     		foreach ($xmlTagsRela->channel->item as $TagRela) { 
     			//vérifie que le tag existe pour l'utilisateur
	     		$idfluxRela = $this->VerifUserFlux($oUti->id, $TagRela->title); 
	     		//vérifie que la relation entre tags existe pour l'utilisateur
	     		$this->VerifUserFluxRela($oUti->id,$idflux,$idfluxRela,$TagRela->description);
	     		//calcul les liens du tag lié au tag de base
	     		if($niv==0){
	     			$this->aSetTagLinks($oDelicious, $oUti, $TagRela->title, $niv+1);
	     		}
				$end = microtime(); 
				$t2 = ($this->site->getmicrotime($end) - $this->site->getmicrotime($start)); 
				// end benchmark timing
				//echo "<br/>".str_repeat("- ", $niv+1)."Total Tag Time: $TagRela->title <b>$t2</b>";
				//echo "<br/><br/>";
     		}
	    }else {
		   echo $oDelicious->LastErrorString();
		}	

		// benchmark timing 
		$end = microtime(); 
		$t2 = ($this->site->getmicrotime($end) - $this->site->getmicrotime($start)); 
		// end benchmark timing
		echo "<p>".str_repeat("- ", $niv+1)."Total aSetTagLinks Time: $tag <b>$t2</b>";
		$mem=memory_get_usage(true);$mem=$mem/1048576;
		echo "<br/>$mem M <br/>";
		echo "<br/><br/>";
		
    }
	
	function aSetTagsLinks($oDelicious,$oUti){

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
					
					//ajoute les posts
					$this->aSetTagsPosts($oDelicious,$oUti,$Tag);
		     	} 
		    }else {
			   echo $oDelicious->LastErrorString();
			}	
		}
	}
	
	function GetUserTags($oUti){
		
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$sql="SELECT of.onto_flux_code tag, of.onto_flux_id 
			FROM ieml_onto_flux of 
				INNER JOIN ieml_uti_onto_flux uof ON uof.onto_flux_id = of.onto_flux_id AND uti_id = ".$oUti->id."";
		$rs = $db->query($sql);
		$db->close();
		return $rs;		
	}
	
	function GetUserTagsSansDoc($oUti){
		
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$sql="SELECT COUNT( uof.onto_flux_id ) nb, of.onto_flux_code tag, of.onto_flux_id id, u.uti_login
			FROM ieml_onto_flux of
				INNER JOIN ieml_uti_onto_flux uof ON uof.onto_flux_id = of.onto_flux_id
				INNER JOIN ieml_uti u ON u.uti_id = uof.uti_id AND u.uti_id = ".$oUti->id."
				LEFT JOIN flux_tags_docs td ON td.id_tag = uof.onto_flux_id
			WHERE td.id_doc IS NULL
			GROUP BY uof.onto_flux_id";
		$rs = $db->query($sql);
		$db->close();
		return $rs;		
	}
	

	function DelUserTags($oUti){
		
		$rs = $this->GetUserTagsSansDoc($oUti);
		while($r=mysql_fetch_assoc($rs)){
			$this->DelTags($r["id"]);			
		}
	}

	function DelTags($idTag){
		
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		
		$sql="DELETE FROM ieml_onto_flux 
			WHERE onto_flux_id = ".$idTag;
		$db->query($sql);
		
		$sql="DELETE FROM ieml_uti_onto_flux 
			WHERE onto_flux_id = ".$idTag;
		$db->query($sql);
		
		$sql="DELETE FROM ieml_uti_onto_flux_related 
			WHERE onto_flux_id = ".$idTag;
		$db->query($sql);

		$sql="DELETE FROM ieml_trad 
			WHERE onto_flux_id = ".$idTag;
		$db->query($sql);
		
		$sql="DELETE FROM flux_tags_docs 
			WHERE id_tag = ".$idTag;
		$db->query($sql);
				
		$db->close();

	}
	
	
	function aSetUserTagsPosts($oDelicious,$oUti){
		
		//$rs = $this->GetUserTags($oUti);
		$rs = $this->GetUserTagsSansDoc($oUti);
		while($r=mysql_fetch_assoc($rs)){
			$this->aSetTagsPosts($oDelicious,$oUti,$r["tag"]);			
		}
	}
	
	function aSetTagsPosts($oDelicious,$oUti,$tag){
     	set_time_limit(9000);

     	// end start benchmark
     	$start = microtime(); 
     	
     	/*ajoute à la base les post d'un tag
     	*/
     	if($oDelicious->isUpdatePost() || FORCE_CALCUL){
     		//on prend les 100 derniers post
     		$rssTags = $oDelicious->GetUserPosts($oUti->login,$tag);
	     	$xmlTags = simplexml_load_string($rssTags);
     		if ($xmlTags) { 
     			foreach ($xmlTags->channel->item as $item) { 
					
		     		//vérifie que le doc existe pour l'utilisateur
		     		$idDoc = $this->VerifUserDoc($oUti->id,$item);
					//echo $idDoc." : ".$item->title."<br/>-> tags : ";  		     		
		     		//vérifie que les tags existent pour le doc
		     		foreach ($item->category as $tag) { 
		     			$idTag = $this->VerifTagDoc($idDoc,$tag);
		     			//echo $tag." (".$idTag."), ";
		     		}
					// benchmark timing 
					$end = microtime(); 
					$t2 = ($this->site->getmicrotime($end) - $this->site->getmicrotime($start)); 
					// end benchmark timing
					//echo "<br/>Total Post Time: <b>$t2</b>";
					//echo "<br/><br/>";
     			} 
		    }else {
			   echo $oDelicious->LastErrorString();
			}	
		}
		
		// benchmark timing 
		$end = microtime(); 
		$t2 = ($this->site->getmicrotime($end) - $this->site->getmicrotime($start)); 
		// end benchmark timing
		echo "<p>Total aSetTagsPosts Time: <b>$t2</b>";
		$mem=memory_get_usage(true);$mem=$mem/1048576;
		echo "<br/>$mem M <br/>";
		echo "<br/><br/>";		
		
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
        
	function VerifTagDoc($idDoc, $tag){

		//vérifie que le tag du flux existe
	    $reponse = $this->VerifFluxExiste($tag);			   
		if(!$reponse){
			//ajoute un nouveau tag de flux
		   	$idTag= $this->InsertFlux($tag);			   	
		}else{
			$idTag=$reponse['onto_flux_id'];
		}
		//vérifie si le doc possède le flux
		$this->SetTagDoc($idTag,$idDoc);		
				
	    
	    return $idTag;				
	}
	
	function VerifUserDoc($UtiId, $item){

		//vérifie que le doc existe
	    $reponse = $this->VerifDocExiste($item->link);			   
		if(!$reponse){
			//ajoute un nouveau tag de flux
		   	$idDoc= $this->InsertDoc($item);			   	
		}else{
			$idDoc=$reponse['id_doc'];
		}
		//Vérifie si la relation existe
		$this->SetUtiDoc($UtiId,$idDoc);		
	    
	    return $idDoc;				
	}
	

	function SetUtiDoc($idUti,$idDoc){
		//vérifie que la relation existe
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
        $db->connect();
		$sql = "SELECT id_uti 
	    	FROM flux_utis_docs
	    	WHERE id_uti = $idUti AND id_doc = $idDoc";
	    $req = $db->query($sql);
	    $db->close();
	    $reponse=mysql_fetch_assoc($req);
		if(!$reponse){
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			$sql = "INSERT INTO flux_utis_docs 
		    	(id_uti, id_doc)
		    	VALUES($idUti,$idDoc)";
		    $req = $db->query($sql);
	    	$db->close();
		}
	}
	
	function SetTagDoc($idTag,$idDoc){
		//vérifie que la relation existe
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
        $db->connect();
		$sql = "SELECT id_tag 
	    	FROM flux_tags_docs
	    	WHERE id_tag = $idTag AND id_doc = $idDoc";
	    $req = $db->query($sql);
		$reponse=mysql_fetch_assoc($req);
	    $db->close();
		if(!$reponse){
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
	        $db->connect();
			$sql = "INSERT INTO flux_tags_docs 
		    	(id_tag, id_doc)
		    	VALUES($idTag,$idDoc)";
		    $req = $db->query($sql);
		    $db->close();	
		}
	}
	
	
	function UpdateUserFluxPoids(){

		//vérifie que la relation existe
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
        $db->connect();
		$sql = "SELECT SUM(uofr.poids) poids, of.onto_flux_id, of.onto_flux_code, uof.uti_id
			FROM ieml_onto_flux of
				INNER JOIN ieml_uti_onto_flux uof ON uof.onto_flux_id = of.onto_flux_id
				INNER JOIN ieml_uti_onto_flux_related uofr ON uofr.onto_flux_id = of.onto_flux_id AND uofr.uti_id = uof.uti_id 
			GROUP BY of.onto_flux_id, of.onto_flux_code, uof.uti_id";
	    $rs = $db->query($sql);
	    $db->close();
	    while($r=mysql_fetch_assoc($rs))
		{
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			$sql = "UPDATE ieml_uti_onto_flux
				SET poids = ".$r["poids"]."
    			WHERE uti_id = ".$r["uti_id"]." AND onto_flux_id = ".$r["onto_flux_id"];
    		$req = $db->query($sql);
		    $db->close();
	    }
						
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
					SET poids = $poids
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

	function InsertDoc($item){
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
        $db->connect();
        //calcul le poids de l'url
        $poids = $this->GetDocPoids($item->link);
        $dPost = new DateTime($item->pubDate);
		$sql = "INSERT INTO flux_docs (url,titre,branche,tronc,poids,pubDate, maj)
			VALUES(\"".$item->link."\",".$this->site->GetSQLValueString($item->title,"text").",0,0,".$poids.",\"".$dPost->format('c')."\", now())";
	    $req = $db->query($sql);
	    $idDoc= mysql_insert_id();
	    $db->close();
	    
	    return $idDoc;
				
	}
	
	function GetDocPoids($url){		
		$infos = $this->site->GetCurl($url,true);
		//return strlen($doc);
		return $infos['size_download'];
	}
	
	function VerifDocExiste($url){
	   $sql="SELECT id_doc FROM flux_docs WHERE url = \"".$url."\"";
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
