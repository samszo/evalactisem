<?php
/*
/////////////////////
Nom du fichier : Trad.php

Version : 1.0
Auteur : samszo
Date de modification : 07/12/2007

////////////////////
*/

Class Evalu{
	public $User;
	public $Page;
	public $Eval;
	public $Sem;
	public $JS;

	function __construct($FicXml) {
	    $this->FicXml = $FicXml;
		//echo "On charge les paramètres : ".$FicXml."<br/>\n";
		if ($xml = simplexml_load_file($FicXml))
			$this->xml = $xml;
		
	}
	
	public function GetScript($Acte){
		//echo 'On cherche le xpath '.$Xpath.'<br/>';
		return $this->xml->xpath($Xpath);
	}
	
	public function GetFlux()
	{
	    return "";
	}
	
}
?>