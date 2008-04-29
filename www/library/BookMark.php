<?php
class BookMark{
	private $bookmark;
	private $trace;
	public $id;
	public $Posts;
	public $login;
	public $titre;
	public $url;
	public $xml;
	
	
	function __tostring() {
    	return "Cette classe permet de définir des BookMarks.<br/>";
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
			$i=0;
			foreach($this->xml->xpath("/bookmark") as $idbook){
				$id=$idbook['id'] ;
				$this->titre=$idbook['id'];
			}
			
			foreach($this->xml->xpath("//post") as $post){ 
				$ids[$i]=$post['id'];
				$i++;
				
			}
			$i=0;
			foreach($this->xml->xpath("//post/url") as $post){
				
				$urls[$i]=$post;
				$i++;
				
			}
		 	
			for($i=0;$i<sizeof($ids);$i++){
				$this->Posts[$i]= new Post($this->bookmark,$ids[$i],$urls[$i]);
			    $this->Posts[$i]->GetInfos($ids[$i]);
				
			}
				
			
	}
	function GetNbPost(){
		
		foreach($this->xml->xpath("//post") as $post){
			$nb++;
		}
		return $nb;
	}
	
}

?>