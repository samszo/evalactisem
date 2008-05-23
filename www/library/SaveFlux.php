<?php
session_start();

class SauvFlux{
	public $descFlux_Band;
	public $niveauFlux_Band;
	public $parentsFlux_Band;
	public $descFlux;
	public $niveauFlux;
	
	
	function _construct($desc_Band,$niv_Band,$parent_Band,$desc,$niv){
		
		$this->descFlux_Band=$desc_Band;
		$this->niveauFlux_Band=$niv_Band;
		$this->parentsFlux_Band=$parent_Band;
		$this->descFlux=$desc;
		$this->niveauFlux=$niv;
		
		
	}
    
	function aGetAllBundles($objSite,$oDelicious,$iduti){
		
		$desc_Band="bundels";
		$niv_Band=0;
		$parent_Band="";
		$desc="tag";
		$niv=1;
		
		
	   if ($aPosts = $oDelicious->GetAllBundles()) {
			
			foreach ($aPosts as $aPost) { 
		           
				   $tags=$aPost['tags']." ";
				   $name.=$aPost['name'].";";
	               $tag.=$aPost['tags']." ";
				   
	               $Xpath=Xpath('Ieml_Onto_existe');
				   $Q=$objSite->XmlParam->GetElements($Xpath);
				   $where=str_replace("-tag-",$aPost['name'],$Q[0]->where);
				   $sql=$Q[0]->select.$Q[0]->from." ".$where;
				   $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
				   $db->connect();
				   $req = $db->query($sql);
				   if(@mysql_num_rows($req)==0){
					    
		           	    $Xpath=Xpath('Ieml_Onto_Flux');
		           	    $Q=$objSite->XmlParam->GetElements($Xpath);
		           	    $value = str_replace("-descFlux-",$desc_Band,$Q[0]->values);
						$value = str_replace("-codeFlux-",addslashes($aPost['name']),$value );
						$value = str_replace("-niveauFlux-",$niveau_Band,$value );
						$value = str_replace("-parentsFlux-", $parents_Band,$value );
					    $sql = $Q[0]->insert.$value;
					    $req = $db->query($sql);
			  	        $idparentflux=mysql_insert_id();
					    $this->flux_uti($objSite,$iduti,$idparentflux,$db);
			  	        
			  	       
					   $Xpath=Xpath('Flux_Foret');
			           
					   $Q=$objSite->XmlParam->GetElements($Xpath);
			           $values=str_replace("-idFlux-",$idparentflux,$Q[0]->values);
					   $values=str_replace("-idparentsFlux-",0,$values);
					   $sql = $Q[0]->insert.$values;
					   $req = $db->query($sql);
			           $enfant=explode(" ",$tags);
				      
				     for($i=0;$i<sizeof($enfant)-1;$i++){				  
				    
				       $Xpath=Xpath('Ieml_Onto_existe');
		               $Q=$objSite->XmlParam->GetElements($Xpath);
		               
		               $where=str_replace("-tag-",$enfant[$i],$Q[0]->where);
					   $sql=$Q[0]->select.$Q[0]->from." ".$where;
			           $res = $db->query($sql);
					   $result=mysql_fetch_array($res);
					   $parents=$result[0].$aPost['name'].";";
					   $id=$result[1];
	                   $idpred=$id;
					   if(@mysql_num_rows($res)==0){
						    
					   	    $Xpath=Xpath('Ieml_Onto_Flux');
	
					   	    $Q=$objSite->XmlParam->GetElements($Xpath);
						  
					   	    $value = str_replace("-descFlux-",$desc,$Q[0]->values);
							$value = str_replace("-codeFlux-",$enfant[$i],$value );
							$value = str_replace("-niveauFlux-",$niv,$value );
							$value = str_replace("-parentsFlux-",$aPost['name'].";",$value );
						    
							$Xpath=Xpath('Flux_Foret');
							$sql = $Q[0]->insert.$value;
							$req = $db->query($sql);
						    $idflux=mysql_insert_id();
					        
					        $Q=$objSite->XmlParam->GetElements($Xpath);
					        $values=str_replace("-idFlux-",$idflux,$Q[0]->values);
					        $values=str_replace("-idparentsFlux-",$idparentflux,$values);
					        $sql = $Q[0]->insert.$values;
					        $req = $db->query($sql);

					        $idpred=$id;
				           
					   }else
				        	if(@mysql_num_rows($res)!=0){
				              
				        	   $Xpath=Xpath('Ieml_Onto_Flux1');
				               $Q=$objSite->XmlParam->GetElements($Xpath);
				               
				               $where=str_replace("-enfant-",$enfant[$i],$Q[0]->where);
				               $update=str_replace("-parentsFlux-",$parents,$Q[0]->update);
				                
				               $sql=$update.$where;
				               $req = $db->query($sql);
	
				               $Xpath=Xpath('foret_update_parent');
				               $Q=$objSite->XmlParam->GetElements($Xpath);
				               $where=str_replace("-Fluxid-",$id,$Q[0]->where);
				               $sql=$Q[0]->select.$Q[0]->from." ".$where;
				               $req = $db->query($sql);
				               $resl=mysql_fetch_array($req);
				               $parent=$resl[0];
				               if(($idpred==$id)&&($parent!=-1)){
	
				               	        $Xpath=Xpath('Flux_Foret');
										$sql = $Q[0]->insert.$value;
										$req = $db->query($sql);
									    $idflux=mysql_insert_id();
								        
								        $Q=$objSite->XmlParam->GetElements($Xpath);
								        $values=str_replace("-idFlux-",$id,$Q[0]->values);
								        $values=str_replace("-idparentsFlux-",$idparentflux,$values);
								        $sql = $Q[0]->insert.$values;
								        $req = $db->query($sql);
								        $db->close();
								        $idpred=$id;
				               	
				               }else{
					               
					               	   $Xpath=Xpath('Ieml_Onto_foret_update');
						               $Q=$objSite->XmlParam->GetElements($Xpath);
						               $where=str_replace("-idFlux-",$id,$Q[0]->where);
						               $update=str_replace("-idparentsFlux-",$idparentflux,$Q[0]->update);
						               $sql=$update.$where;
						               $req = $db->query($sql);
					               	   
				                      // echo $id." ".$idpred;
				                       $idpred=$id;
				               }
				               
							}
				        
						}
			             
				   }
			$db->close();  		 
			}
		
					 $Xpath=Xpath('Ieml_foret_update');
					 $Q=$objSite->XmlParam->GetElements($Xpath);
					 $sql=$Q[0]->update.$Q[0]->where;
					 $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
					 $db->connect();
					 $req = $db->query($sql);
					 $db->close();
		        	
		} 
		 else {
		        echo $oDelicious->LastErrorString();
		 }
	   	 $sTag=explode(" ", $tag);
	   	 $aTag=implode(";", $sTag);
	   	 
		 return "<marque ieml='t.u.-'> <bundles> $name </bundles> <Nbrtag > $aTag </Nbrtag> </marque>"; 
	   	 
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
				   $db->connect();
				   $req = $db->query($sql);
				   $db->close();
				   if(@mysql_num_rows($req)==0){
					 		
				   			$Xpath=Xpath('Ieml_Onto_Flux');
							$Q=$objSite->XmlParam->GetElements($Xpath);
						  	
					   	    $value = str_replace("-descFlux-",$desc,$Q[0]->values);
							$value = str_replace("-codeFlux-",addslashes($aPost['tag']),$value );
							$value = str_replace("-niveauFlux-",$niv,$value );
							$value = str_replace("-parentsFlux-",$parentsFlux,$value );
						    $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		                    $db->connect();
							$sql = $Q[0]->insert.$value;
						    $req = $db->query($sql);
						    $idflux=mysql_insert_id();
					        
						    $Xpath=Xpath('Flux_Foret');
							$Q=$objSite->XmlParam->GetElements($Xpath);
					        $values=str_replace("-idFlux-",$idflux,$Q[0]->values);
					        $values=str_replace("-idparentsFlux-",-1,$values);
					        $sql = $Q[0]->insert.$values;
					        $req = $db->query($sql);
				            $this->flux_uti($objSite,$iduti,$idflux,$db);
					        $db->close();
	  
				   }
	 		} 
	  	    
		    
	 		
		}else {
		        echo $oDelicious->LastErrorString();
		 }
		
		 
		 $result=$tag."*".$count; 
		

		 //$result=str_replace("(.*)&(.*)","et",$result);
		 return $result;
	}



    function aGetPosts($aPosts){

		foreach ($aPosts as $aPost) { 
  			$aDesc.=$aPost['desc'].";";
			$aUrl.=$aPost['url'].";";
  			$aUdate.=$aPost['updated'].";";
  			$aNote.=$aPost['notes'].";";
  	        $tag="";
  			foreach($aPost['tags'] as $aTags){
  	    		
  				$tag.=$aTags.";";
  	    	}
  	    	$t.=$tag."*";
  	    	
  	    }
  		
	 
        $result="<marque ieml='t.u.-'><tags><![CDATA[$t]]></tags><description><![CDATA[$aDesc]]></description><url><![CDATA[$aUrl]]></url><date><![CDATA[$aUdate]]></date></marque>";
  	
    //$result=str_replace("&","et",$result); 	   
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
		while($result=mysql_fetch_array($reponse)){
			$parents.=$result[0].";";
			$Xpath=Xpath("repres_graph_flux1");
			$Q=$objSite->XmlParam->GetElements($Xpath);
			$where=str_replace("-parent-",$result[1],$Q[0]->where);
			$sql=$Q[0]->select.$Q[0]->from.$where;
			$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$res = $db->query($sql);
			$db->close();
			$resu=mysql_fetch_array($res);
			$count.=$resu[0].";";
			//echo $parents."*".$count;
		
		    
			
		}
		
		$result="<bundles> $parents </bundles><nbrtag> $count </nbrtag>";
		 
		return $result;
	
}

	function utilisateur($objSite,$uti_login){
		
	  
		
		$Xpath=Xpath('Verif_Exist_Utilisateur');
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$where=str_replace("-login-",$uti_login,$Q[0]->where);
		$sql=$Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$req = $db->query($sql);
		$res=mysql_fetch_array($req);
		if( @mysql_num_rows($req)==0){
			$Xpath=Xpath('Enrg_Utilisateur');
			echo $Xpath;
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
	
	function flux_uti($objSite,$uti_id,$flux_id,$db){
		
		$Xpath=Xpath('flux_utilisateur');
		$Q=$objSite->XmlParam->GetElements($Xpath);
		$values=str_replace("-iduti-",$uti_id,$Q[0]->values);
		$values=str_replace("-idflux-",$flux_id,$values);
		$sql=$Q[0]->insert.$values;
		$sql;
		$reponse = $db->query($sql);
	}

    function aAddPost($objSite){
    	
    }
}
	
	
?>