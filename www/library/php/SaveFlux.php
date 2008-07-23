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
		
		$desc_Band="bundels";
		$niv_Band=0;
		$parent_Band=-1;
		$desc="tag";
		$niv=1;
		
		
	   if ($aPosts = $oDelicious->GetAllBundles()) {
			
			foreach ($aPosts as $aPost) { 
		           
				   $tags=$aPost['tags']." ";
				   $name.=$aPost['name'].";";
	               $tag.=$aPost['tags']." ";
				   $bundles.=$aPost['name'].";";
	               $counts.=(sizeof(explode(" ",$tags))-1).";";
	               $Xpath=Xpath('Ieml_Onto_existe');
				   $Q=$objSite->XmlParam->GetElements($Xpath);
				   $where=str_replace("-tag-",$aPost['name'],$Q[0]->where);
				   $sql=$Q[0]->select.' '.$Q[0]->from.' '.$where;
				   $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
				   $link=$db->connect();
				   $req = $db->query($sql);
				   $reponse=@mysql_fetch_assoc($req);
				   $rows=@mysql_num_rows($req);
				   $db->close($link);
				   if($rows==0){
					    
		           	    $Xpath=Xpath('Ieml_Onto_Flux');
		           	    $Q=$objSite->XmlParam->GetElements($Xpath);
		           	    $value = str_replace("-descFlux-",$desc_Band,$Q[0]->values);
						$value = str_replace("-codeFlux-",addslashes($aPost['name']),$value );
						$value = str_replace("-niveauFlux-",$niv_Band,$value );
						$value = str_replace("-parentsFlux-", 0 ,$value );
					    $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
				        $link1=$db->connect();
						$sql = $Q[0]->insert.$value;
					    $req = $db->query($sql);
			  	        $idparentflux=mysql_insert_id();
			  	        $db->close($link1);
					    $this->flux_uti($objSite,$iduti,$idparentflux);
			  	        $enfant=explode(" ",$tags);
				      
				     for($i=0;$i<sizeof($enfant)-1;$i++){				  
				    
				       $Xpath=Xpath('Ieml_Onto_existe');
		               $Q=$objSite->XmlParam->GetElements($Xpath);
		               
		               $where=str_replace("-tag-",$enfant[$i],$Q[0]->where);
					   $sql=$Q[0]->select.$Q[0]->from." ".$where;
			            $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
				       $link3=$db->connect();
					   $res = $db->query($sql);
					   $result=@mysql_fetch_array($res);
					   $lignes=@mysql_num_rows($res);
					   $db->close($link3);
					   $parents=$result[0].$idparentflux.";";
					   $id=$result[1];
	                   $idpred=$id;
	                   
					   if($lignes==0){
						    
					   	    $Xpath=Xpath('Ieml_Onto_Flux');
	
					   	    $Q=$objSite->XmlParam->GetElements($Xpath);
						  
					   	    $value = str_replace("-descFlux-",$desc,$Q[0]->values);
							$value = str_replace("-codeFlux-",$enfant[$i],$value );
							$value = str_replace("-niveauFlux-",$niv,$value );
							$value = str_replace("-parentsFlux-",$idparentflux.";",$value );
						    
							
							$sql = $Q[0]->insert.$value;
							$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
				            $link4=$db->connect();
							$req = $db->query($sql);
						    $idflux=mysql_insert_id();
						    $db->close($link4);
					        $idpred=$id;
				           
					   }else
				        	if($lignes!=0){
				              
				        	   $Xpath=Xpath('Ieml_Onto_Flux1');
				               $Q=$objSite->XmlParam->GetElements($Xpath);
				               
				               $where=str_replace("-enfant-",$enfant[$i],$Q[0]->where);
				               $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
				               $link5=$db->connect();
				               $update=str_replace("-parentsFlux-",$parents,$Q[0]->update); 
				               $sql=$update.$where;
				               $req = $db->query($sql);
				               $Xpath=Xpath('foret_update_parent');
				               $Q=$objSite->XmlParam->GetElements($Xpath);
				               $where=str_replace("-Fluxid-",$id,$Q[0]->where);
				               $sql=$Q[0]->select.$Q[0]->from." ".$where;
				               $req = $db->query($sql);
				               $resl=mysql_fetch_array($req);
				               $db->close($link5);
				               $parent=$resl[0];
				              
				               
							}
				        
						}
			             
				   }else{
				   	 
				   	 		   	 
				   	 	$this->flux_uti($objSite,$iduti,$reponse['onto_flux_id']);
				   	
				   }
			  		 
			}
		
					
		        	
		} 
		 else {
		        echo $oDelicious->LastErrorString();
		 }
	   	 $sTag=explode(" ", $tag);
	   	 $aTag=implode(";", $sTag);
	   	 
	   	 
		 return "<bundles> $name </bundles><nbrtag> $counts </nbrtag>"; 
	   	 
	}
	
     function aGetAllTags($objSite,$oDelicious,$iduti){
		
		
		$desc="tag";
		$niv=1;
		
		if ($aPosts = $oDelicious->GetAllTags()) {
	  	  foreach ($aPosts as $aPost) { 
		           
				   $tags.=$aPost['tag']." ";
				   $count.=$aPost['count'].";";
	  	           $tag.=$aPost['tag'].";";
				   
	  	           $Xpath=Xpath('Ieml_Onto_existe');
	               $Q=$objSite->XmlParam->GetElements($Xpath);
	               
	               $where=str_replace("-tag-",addslashes($aPost['tag']),$Q[0]->where);
				   $sql=$Q[0]->select.$Q[0]->from." ".$where;
				   $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
				   $link=$db->connect();
				   $req = $db->query($sql);
				   $row=@mysql_num_rows($req);
				   $reponse=@mysql_fetch_assoc($req);
				   $db->close($link);
				   
				   if($row==0){
					 		
				   			$Xpath=Xpath('Ieml_Onto_Flux');
							$Q=$objSite->XmlParam->GetElements($Xpath);
						  	
					   	    $value = str_replace("-descFlux-",$desc,$Q[0]->values);
							$value = str_replace("-codeFlux-",addslashes($aPost['tag']),$value );
							$value = str_replace("-niveauFlux-",$niv,$value );
							$value = str_replace("-parentsFlux-",0,$value );
							$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
				            $link1=$db->connect();
							$sql = $Q[0]->insert.$value;
						    $req = $db->query($sql);
						    $idflux= mysql_insert_id();
						    $db->close($link1);
				            $this->flux_uti($objSite,$iduti,$idflux);
					       
	  
				   }else{
	  	   			
				   	   	 
				   	 	$this->flux_uti($objSite,$iduti,$reponse['onto_flux_id']);
				   	
				   }
				    
	 		} 
	
		}else {
		        echo $oDelicious->LastErrorString();
		 }
		
		 
		 $result=$tag."*".$count; 
		

		 $result=str_replace("(.*)&(.*)","et",$result);
		 return $result;
	}



    function aGetPosts($aPosts,$format_result){
        
    	
		foreach ($aPosts as $aPost) { 
  			$aDesc.=$aPost['desc'].";";
			$aUrl.=$aPost['url'].";";
  			$aDate.=$aPost['updated'].";";
  			$aNote.=$aPost['notes'].";";
  	        $tag="";
  			foreach($aPost['tags'] as $aTags){
  	    		
  				$tag.=$aTags.";";
  	    	}
  	    	$t.=$tag."*";
  	    	
  	    }
  		
	    if($format_result=='xml'){
        	$result="<marque ieml='t.u.-'><tags><![CDATA[$t]]></tags><description><![CDATA[$aDesc]]></description><url><![CDATA[$aUrl]]></url><date><![CDATA[$aDate]]></date><note><![CDATA[$aNote]]></note></marque>";
	    }else
	    	if($format_result=='string'){
	    		$result=$aUrl."#".$aDesc."#".$t."#".$aDate."#".$aNote;
	    }
     	   
    return $result;
}
 
    function GraphTagBund($objSite,$iduti){
	
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
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
				$Xpath =Xpath("repres_graph_flux1");
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

	function utilisateur($objSite,$uti_login){
		
		$Xpath=Xpath('Verif_Exist_Utilisateur');
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$where=str_replace("-login-",$uti_login,$Q[0]->where);
		$sql=$Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "SaveFlux:utilisateur:login=".$objSite->infos["SQL_LOGIN"]." sql=".$sql."<br/>";
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$req = $db->query($sql);
		$res=@mysql_fetch_array($req);
		if( @mysql_num_rows($req)==0){
			$Xpath=Xpath('Enrg_Utilisateur');
			$Q=$objSite->XmlParam->GetElements($Xpath);
			$values=str_replace("-login-",$uti_login,$Q[0]->values);
			$sql=$Q[0]->insert.$values;
			
			$reponse = $db->query($sql);
			$uti_id=mysql_insert_id();
			return $uti_id;
		}
		$db->close();
		return $res[0]  ;
	}
	
	function flux_uti($objSite,$uti_id,$flux_id){
		$Xpath=Xpath('Ieml_Uti_Onto_Flux_existe');
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$where=str_replace("-idflux-",$flux_id,$Q[0]->where);
		$where=str_replace("-iduti-",$uti_id,$where);
		
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$link=$db->connect();
		$sql=$Q[0]->select.$Q[0]->from.$where;
		$r = $db->query($sql);
		
		if(@mysql_num_rows($r)==0){				   	 
			  	
			$Xpath=Xpath('flux_utilisateur');
			$Q=$objSite->XmlParam->GetElements($Xpath);
			$values=str_replace("-iduti-",$uti_id,$Q[0]->values);
			$values=str_replace("-idflux-",$flux_id,$values);
			$sql=$Q[0]->insert.$values;
			$db->query($sql);
			if($this->trace)
				echo "SaveFlux:flux_uti:login=".$objSite->infos["SQL_LOGIN"]." sql=".$sql."<br/>";
			
			
			$db->close($link);
		}
	}

    
}
	
	
?>