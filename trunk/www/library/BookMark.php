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
        
	 	 $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		 $db->connect();         	
         $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='GetTradTag']";
         $Q = $objSite->XmlParam->GetElements($Xpath);
         $from = str_replace("-iduti-", $_SESSION['iduti'],$Q[0]->from);
         $from = str_replace("-poster-", 0,$from);
         $sql = $Q[0]->select.$from;
         $result = $db->query($sql);
         $db->close();
        //boucle sur les tag traduit 
   		// pour chaque tag  il faut recupper l'url correspondant
    	
    	while($reponse=mysql_fetch_assoc($result)){     
    		$Posts=$oDelicious->GetPosts($reponse['onto_flux_code'],'','', false);
    		if($this->trace)
					echo "BookMark.php:MajPostIeml:Posts".print_r($Posts)."<br/>";
    		foreach($Posts as $Post){
    			$PostIeml=$oIeml->GetPosts('','',$Post['url'],true);
    			if(TRACE)
					echo "BookMark.php:MajPostIeml:Imel".print_r($PostIeml)."<br/>";
    			if(!eregi($_SESSION['loginSess'],$PostIeml['notes'])){
    				$notes=$PostIeml['notes'].$_SESSION['loginSess'].";";
    			} 
    			$AddPost=$oIeml->AddPost($Post['url'],$Post['desc'],$notes,$reponse['ieml_code'],true);
    			if($this->trace)
					echo "BookMark.php:MajPostIeml:AddPost".print_r($AddPost)."<br/>";
    			
					$postMAJ.= $Post['url']." ";
            }
        	
            $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		 	$db->connect();  
    		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='update_posted_tag']";
        	$Q = $objSite->XmlParam->GetElements($Xpath);
        	$where=str_replace("-IdIeml-",$reponse['ieml_id'],$Q[0]->where);
        	$where=str_replace("-IdFlux-",$reponse['onto_flux_id'],$where);
        	$sql = $Q[0]->update.$where;
        	$res = $db->query($sql);
         	$db->close();
         	
    	}
   echo $postMAJ;     
   }
}

?>