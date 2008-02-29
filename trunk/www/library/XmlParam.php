<?php
/*
/////////////////////
Nom du fichier : XmlParam.php

Version : 1.0
Auteur : samszo
Date de modification : 21/11/2007

////////////////////
*/


Class XmlParam{
	public $FicXml;
	private $xml;

	function __construct($FicXml) {
	    $this->FicXml = $FicXml;
		//echo "On charge les param�tres : ".$FicXml."<br/>\n";
		if ($xml = simplexml_load_file($FicXml))
			$this->xml = $xml;
		
	}
	
	public function GetElements($Xpath){
		//echo 'On cherche le xpath '.$Xpath.'<br/>';
		return $this->xml->xpath($Xpath);
	}
	
	public function XML_entities($str)
	{
	    return preg_replace(array("'&'", "'\"'", "'<'"), array('&#38;', '&#34;','&lt;'), $str);
	}
	
}
?>