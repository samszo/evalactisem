<?php

class BookMark{
	private $bookmark;
	private $trace;
	public $id;
	public $Posts;
	public $NbPost;
	public $Tags = array();
	public $login;
	public $titre;
	public $url;
	public $xml;
	
	
	function __tostring() {
    	$s = "Cette classe permet de définir des BookMarks.<br/>";
    	$s .= $this->id."<br/>";
    	$s .= $this->bookmark."<br/>";
    	return $s;
    }
	
    function __construct(){
		$this->trace=TRACE;
	   
	}
	
	function construct($bookmark){
		$this->trace=TRACE;
		$this->bookmark=$bookmark;
		
		$this->xml = simplexml_load_string($this->bookmark);
		foreach($this->xml->xpath("/bookmark") as $idbook){
				$this->id=$idbook['id'] ;
		}
		
	}
	
	function GetInfos(){

			//récupère les infos du bookmark
			$idbook = $this->xml->xpath("/bookmark");
			$this->id=$idbook[0]['id'] ;
			$this->titre=$idbook[0]['id'];

			//récupère les post du bookmark
			$i=0;
			foreach($this->xml->xpath("//post") as $post){ 
				$idP = $post['id'];
				$url = $post->url;
				$this->Posts[$i]= new Post($this->bookmark,$idP,$url);
				$j=0;
				//récupère les tags du bookmark
				foreach($post->Tags->tag as $tag){
					$this->Posts[$i]->Tags[$j]=$tag;
					//ajoute les tag distinct au niveau du bookmark
					if(!in_array($tag,$this->Tags))
						array_push($this->Tags,$tag."");
					$j++;	 
				}				
			    $i++;
			}
			$this->NbPost = $i;
			
	}
    
	function MajPostIeml( $objSite,$oDelicious){
     	 
        $oIeml = new PhpDelicious(LOGIN_IEML, MDP_IEML);
         // Recupération des tarductions des tags
         
	 	 $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
		 $db->connect();         	
         $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='GetTradTag']";
         $Q = $objSite->XmlParam->GetElements($Xpath);
         $from = str_replace("-iduti-", $_SESSION['iduti'],$Q[0]->from);
         $from = str_replace("-poster-", 0,$from);
         $sql = $Q[0]->select.$from;
         $result = $db->query($sql);
         $db->close();
        //boucle sur les tag traduit 
   		// pour chaque tag  il faut recupper l'url correspondante
    	$postMAJ = "";
    	while($reponse=mysql_fetch_assoc($result)){     
    		$Posts=$oDelicious->GetAllPosts($reponse['onto_flux_code'],true);
    		if($this->trace)
					echo "BookMark.php:MajPostIeml:Posts".print_r($Posts)."<br/>";
			if($Posts){
	    		foreach($Posts as $Post){
	    			$PostIeml=$oIeml->GetPosts('','',$Post['url'],true);
	    			if($this->trace)
						echo "BookMark.php:MajPostIeml:Imel".print_r($PostIeml)."<br/>";
	    			
					// Si le login ni pas dans la description de post ou le tag n exite pas dans le post on ajoute le post
						
					if(!eregi($_SESSION['loginSess'],$PostIeml['notes'])||!in_array($reponse['ieml_code'],$PostIeml['tags'])){
	    				$notes=$PostIeml['notes'].$_SESSION['loginSess'].";";
	    				$AddPost=$oIeml->AddPost($Post['url'],$Post['desc'],$notes,$reponse['ieml_code'],true);
	    				if($this->trace)
	    			    	echo "BookMark.php:MajPostIeml:AddPost".print_r($AddPost)."<br/>";
	    			} 
	    			
	    			
	    			
						$postMAJ.= $Post['url']." ";
	            }
	        	
	            //Mise a jour de la table onto_trad( Mettre 1 trad_post pour les traduction posté)
	            
	            $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
			 	$db->connect();  
	    		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='update_posted_tag']";
	        	$Q = $objSite->XmlParam->GetElements($Xpath);
	        	$where=str_replace("-IdIeml-",$reponse['ieml_id'],$Q[0]->where);
	        	$where=str_replace("-IdFlux-",$reponse['onto_flux_id'],$where);
	        	$sql = $Q[0]->update.$where;
	        	$res = $db->query($sql);
	         	$db->close();
			}         	
    	}
         $Activite= new Acti();
    	 $Activite->AddActi("MajCptIeml",$_SESSION['iduti']);
    	echo $postMAJ;     
   }
   
    function DeletCompteDelicious($objSite,$oDelicious,$iduti,$login){
   	 	 
   		 //Suppression des tags de l'utilisateur de la table ieml_uti_onto_flux
   		 
   	     $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		 $db->connect();         	
         $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='Delete_Ieml_uti_Onto_Flux']";
         $Q = $objSite->XmlParam->GetElements($Xpath);
         $where = str_replace("-iduti-", $iduti ,$Q[0]->where);
         if($this->trace)
         	echo"BookMark.php:Delet_Compte_Delicious:SQL:".$sql;
         $sql = $Q[0]->delete.$Q[0]->from.$where;
         $result = $db->query($sql);
         
         //Suppression des traductions de l'utilisateur
              	
         $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='Delete_Ieml_uti_Onto']";
         $Q = $objSite->XmlParam->GetElements($Xpath);
         $where = str_replace("-iduti-", $iduti ,$Q[0]->where);
         $sql = $Q[0]->delete.$Q[0]->from."".$where;
         if($this->trace)
         	echo"BookMark.php:Delet_Compte_Delicious:SQL:".$sql;
         $result = $db->query($sql);
         //Suppression de l'utilisateur 
         
         $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='Delete_Ieml_uti']";
         $Q = $objSite->XmlParam->GetElements($Xpath);
         $where = str_replace("-iduti-", $iduti ,$Q[0]->where);
         $sql = $Q[0]->delete.$Q[0]->from."".$where;
         if($this->trace)
         	echo"BookMark.php:Delet_Compte_Delicious:SQL:".$sql;
         $result = $db->query($sql);
         $db->close();
         //Suppression des Fichiers de l'utilisateur Flux_login,Primitives_login,Events_login
         
   		$this->Suppression_Fichier(Flux_PATH.md5(XmlFlux).".xml");
   		$this->Suppression_Fichier(Flux_PATH.md5('Events_'.XmlGraphIeml).".xml");
   		$this->Suppression_Fichier(Flux_PATH.md5('Primitives_'.XmlGraphIeml).".xml");
   		
   		//Purrage du cache delicoius
   		$oDelicious->DeleteCache($login."posts/all".''.''.-1);
   		$oDelicious->DeleteCache($login.'tags/bundles/all');
   		$oDelicious->DeleteCache($login."tags/get");
   		$Activite= new Acti();
   		$Activite->AddActi("DelCpt",$iduti);	
   		return 'Votre  compte a ete supprime avec succe';
 
   	}

   	function Suppression_Fichier($fichier){
   	  if(file_exists($fichier)){
				unlink($fichier);
	  }
   }
}

?>