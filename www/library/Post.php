<?php
class Post{
	public $id;
	public $url;
	public $Tags;
	public $Bundles;
	public $bookmark;
	public $xml;
	public $titre;
	
	
	function __tostring() {
    	
		return "Cette classe permet de définir des posts.<br/>";
    }
	function __construct($bookmark,$id,$url){
		$this->id=$id;
		$this->bookmark=$bookmark;
		$this->url=$url;
		$this->xml=simplexml_load_string($this->bookmark);
	
		
	}
	
	function GetInfos($id){
		foreach($this->xml->xpath('//post') as $post){
			if($id=$post['id']){
				$url=$this->url;
				foreach($this->xml->xpath('//tag') as $tag){
					$Tags[$i]=$tag;
				}
			}
		}
	}
}
?>