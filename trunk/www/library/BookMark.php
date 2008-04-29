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
	function __construct($bookmark){
		$this->trace=TRACE;
		$this->bookmark=$bookmark;
		$this->xml = simplexml_load_string($this->bookmark);
		foreach($this->xml->xpath("/bookmark") as $idbook){
				$id=$idbook['id'] ;
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
	
}

?>