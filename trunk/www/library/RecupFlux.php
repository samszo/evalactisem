<?php
   
   require('XmlParam.php');
   require('php-delicious/php-delicious.inc.php');
   require('../param/Constantes.php');
   require_once ("../param/ParamPage.php");
   define('DELICIOUS_USER', "amelmaster");
   define('DELICIOUS_PASS', "lema1983");
   
   $requette= $_GET["requette"];
   $aTag=$_GET["tag"];
  
   $oDelicious = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASS);
	
   if($requette==GetAllBundles ){
	
   	$descFlux_Band="bundels";
	$niveauFlux_Band=0;
	$parentsFlux_Band="";
	$descFlux="tag";
	$niveauFlux=1;
	
	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
	$db->connect();
	
	
	if ($aPosts = $oDelicious->GetAllBundles()) {
		
		foreach ($aPosts as $aPost) { 
	           
			   $tags=$aPost['tags']." ";
			   $name =$aPost['name']." ";
               
               $Xpath=Xpath('Ieml_Onto_Flux2');
			   $Q=$objSite->XmlParam->GetElements($Xpath);
			   $where=str_replace("-tag-",$aPost['name'],$Q[0]->where);
			   $sql=$Q[0]->select.$Q[0]->from." ".$where;
			   $req = $db->query($sql);
			   
			   if(@mysql_num_rows($req)==0){
				    
	           	    $Xpath=Xpath('Ieml_Onto_Flux');
				  	$Q=$objSite->XmlParam->GetElements($Xpath);
	           	    $value = str_replace("-descFlux-",$descFlux_Band,$Q[0]->values);
					$value = str_replace("-codeFlux-",$aPost['name'],$value );
					$value = str_replace("-niveauFlux-",$niveauFlux_Band,$value );
					$value = str_replace("-parentsFlux-", $parentsFlux_Band,$value );
				    
					$sql = $Q[0]->insert.$value;
				    $req = $db->query($sql);
		  	        $idparentflux=mysql_insert_id();
				    
		  	       $Xpath=Xpath('Flux_Foret');
		           $Q=$objSite->XmlParam->GetElements($Xpath);
		           $values=str_replace("-idFlux-",$idparentflux,$Q[0]->values);
				   $values=str_replace("-idprentsFlux-",0,$values);
				   $sql = $Q[0]->insert.$values;
				   $req = $db->query($sql);
		       
			      $enfant=explode(" ",$tags);
			   
			     for($i=0;$i<sizeof($enfant)-1;$i++){				  
			       
			       $Xpath=Xpath('Ieml_Onto_Flux2');
	               $Q=$objSite->XmlParam->GetElements($Xpath);
	               
	               $where=str_replace("-tag-",$enfant[$i],$Q[0]->where);
				   $sql=$Q[0]->select.$Q[0]->from." ".$where;
		           
				   $res = $db->query($sql);
				   $result=mysql_fetch_array($res);
				   $parents=$result[0].$aPost['name'].";";
				   $id=$result[1];
				   
				   
				   if(@mysql_num_rows($res)==0){
					   
				   	    $Xpath=Xpath('Ieml_Onto_Flux');

				   	    $Q=$objSite->XmlParam->GetElements($Xpath);
					  
				   	    $value = str_replace("-descFlux-",$descFlux,$Q[0]->values);
						$value = str_replace("-codeFlux-",$enfant[$i],$value );
						$value = str_replace("-niveauFlux-",$niveauFlux,$value );
						$value = str_replace("-parentsFlux-",$aPost['name'].";",$value );
					    
						$Xpath=Xpath('Flux_Foret');
						$sql = $Q[0]->insert.$value;
						$req = $db->query($sql);
					    $idflux=mysql_insert_id();
				        
				        $Q=$objSite->XmlParam->GetElements($Xpath);
				        $values=str_replace("-idFlux-",$idflux,$Q[0]->values);
				        $values=str_replace("-idprentsFlux-",$idparentflux,$values);
				        $sql = $Q[0]->insert.$values;
				        $req = $db->query($sql);
			             
				   }else
			        	if(@mysql_num_rows($res)!=0){
			              $Xpath=Xpath('Ieml_Onto_Flux1');
			              $Q=$objSite->XmlParam->GetElements($Xpath);
			         
			               $where=str_replace("-enfant-",$enfant[$i],$Q[0]->where);
			               $update=str_replace("-parentsFlux-",$parents,$Q[0]->update);
			         
			               $sql=$update.$where;
			              
			               $req = $db->query($sql);
			               
			               $Xpath=Xpath('Flux_Foret');
			               $Q=$objSite->XmlParam->GetElements($Xpath);
				           $values=str_replace("-idFlux-",$id,$Q[0]->values);
				           $values=str_replace("-idprentsFlux-",$idparentflux,$values);
				           $sql = $Q[0]->insert.$values;
				           $req = $db->query($sql);
			        	
			        	
			        	}
			        
		          }
	           }
			 }
	$db->close();
	        	
	} 
	 else {
	        echo $oDelicious->LastErrorString();
	 }
	
	 echo $name.DELIM.$tags;
     
	 $codeActi='GetBundles';
	 $descActi='Recupperation des groupes de tags';
	 $Actinté= new Sem($codeAvti,$descActi);
	 $Actinté->AddActi($codeActi,$descActi);
   }
   
  if($requette==GetAllTags){
  	
  	$descFlux="tag";
	$niveauFlux=1;
	
	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
	$db->connect();
  	
	if ($aPosts = $oDelicious->GetAllTags()) {
  	  foreach ($aPosts as $aPost) { 
	           
			   $tags.=$aPost['tag']." ";
			   $count.=$aPost['count'].";";
  	           
	  }
	$Xpath=Xpath('Ieml_Onto_Flux2');
               
               
               $Q=$objSite->XmlParam->GetElements($Xpath);
               
               $where=str_replace("-tag-",$aPost['tag'],$Q[0]->where);
			   $sql=$Q[0]->select.$Q[0]->from." ".$where;
	           
			   //echo $Q[0]->select.$Q[0]->from." ".$where;
			   $req = $db->query($sql);
			   
			   if(@mysql_num_rows($req)==0){
				 
			       $tag=explode(" ",$tags);
			       echo "size=".sizeof($tag);
			       for($i=0;$i<sizeof($tag)-1;$i++){				  

				   	    $Xpath=Xpath('Ieml_Onto_Flux');
						$Q=$objSite->XmlParam->GetElements($Xpath);
					  	
				   	    $value = str_replace("-descFlux-",$descFlux,$Q[0]->values);
						$value = str_replace("-codeFlux-",$tag[$i],$value );
						$value = str_replace("-niveauFlux-",$niveauFlux,$value );
						$value = str_replace("-parentsFlux-",$parentsFlux,$value );
					    
						$sql = $Q[0]->insert.$value;
					    $req = $db->query($sql);
					 
				
			        }
  
 
 		} 
  	}else {
	        echo $oDelicious->LastErrorString();
	 }
  	echo $tags.DELIM.$count;
  }
  
  if($requette==GetAllPosts){
  	if ($aPosts = $oDelicious->GetAllPosts($aTag)){
  		
  		foreach ($aPosts as $aPost) { 
  			$aUrl.=$aPost['url']."; ";
  			$aDesc.=$aPost['desc']."; ";
  			$aNote.=$aPost['notes']."; ";
  			$aUdate.=$aPost['updated']."; ";
  			foreach($aPost['tags'] as $aTags){
  	    	$tag.=$aTags."; ";
  	    	}
  		
  		
  		}
  	    
  	    
  	}else {
	        echo $oDelicious->LastErrorString();
	 }
  echo $tag.DELIM.$aDesc.DELIM.$aNote.DELIM.$aUdate;
  }
  
 
  if($requette==GetPosts){
  	if ($aPosts = $oDelicious->GetPosts($aTag,$sUrl,$sDate)){
  	foreach ($aPosts as $aPost) { 
  			$aUrl.=$aPost['url']."; ";
  			$aDesc.=$aPost['desc']."; ";
  			$aNote.=$aPost['notes']."; ";
  			$aUdate.=$aPost['updated']."; ";
  			$sTag.=$aPost['tag']."; ";
  	    	}
  		
  		
  		
  	    
  	    
  	}else {
	        echo $oDelicious->LastErrorString();
	 }
  echo $tag.DELIM.$aDesc.DELIM.$aNote.DELIM.$aUdate;
  }
  
if($requette==GetRecentPosts){
  	if ($aPosts = $oDelicious->GetPosts($aTag,$iCount)){
  	foreach ($aPosts as $aPost) { 
  			$aUrl.=$aPost['url']."; ";
  			$aDesc.=$aPost['desc']."; ";
  			$aNote.=$aPost['notes']."; ";
  			$aUdate.=$aPost['updated']."; ";
  			$sTag.=$aPost['tag']."; ";
  	    	}
  	}else {
	        echo $oDelicious->LastErrorString();
	 }
  echo $tag.DELIM.$aDesc.DELIM.$aNote.DELIM.$aUdate;
  }
  	
 
function Xpath($fonction){
	 $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='".$fonction."']";
	 return $Xpath; 
}

function Existe($codeFlux){
	
	$Xpath=Xpath('Ieml_Onto_Flux2');
    $Q=$objSite->XmlParam->GetElements($Xpath);
    $where=str_replace("-tag-",$codeFlux,$Q[0]->where);
    $sql=$Q[0]->select.$Q[0]->from." ".$where;
	echo $Q[0]->select.$Q[0]->from." ".$where;
    $req = $db->query($sql);
}

 ?>