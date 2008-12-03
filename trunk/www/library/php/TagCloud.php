<?php
class TagCloud {
  private $site;
  private $trace;
  public $marge=100;
  public $xentre_page=64;
  public $yentre_page=128;
  public $width_page=200;
  public $heigth_page=200;
  public $font_size=10;
  public $width_lien=2;
  public $xTrans;
  public $yTrans;
  
  function __tostring() {
    return "Cette classe permet de définir et manipuler un TagCloud.<br/>";
    }

  function __construct() {
    $this->trace = TRACE;		
  }

	public function GetSvg()
	{
		
		//Calcul les intervalles de taille
		$Max = 0;
		$Min = 1;
		$Tot = 1;
		$jsMaxMot="";
		$liste="";

		//récupération des fan delicious
		//http://feeds.delicious.com/v2/rss/networkfans/luckysemiosis
		
        //récupère le bookmark delicious
		//$book = new PhpDelicious(LOGIN_IEML, MDP_IEML);
		/*
		$book = array(
          	array('tag' => 'YO','count' => 20)
          	,array('tag' => 'man','count' => 45)
          	,array('tag' => 'balayeur','count' => 14)
          	,array('tag' => 'bas layer','count' => 33)
          	,array('tag' => 'abat','count' => 24)
        	,array('tag' => 'les heurts','count' => 120)
        );
		*/
		//récupère le boobkmark
		$xml = simplexml_load_file("http://feeds.delicious.com/v2/rss/aeito");
		//recupére les tags
        $tags = $xml->xpath('/rss/channel/item/category');
        $book = array();
        //boucle sur tous les tags
        foreach($tags as $tag){
        	//récupère les post correspondant au tag
			$NbTag = simplexml_load_file("http://feeds.delicious.com/v2/rss/aeito/".$tag);
			//compte le nombre de post
	        $nb = count($NbTag->xpath('//item'));
	        //ajoute les valeurs au tableaux        	
        	array_push($book,array('tag' => $tag,'count' => $nb)); 
        }
		
       	if($book) {
        	if($this->trace)
		    	echo "TagCloud:GetSvg:book".print_r($book)."<br/>";

			//calcul les intervales 
			foreach($book as  $val)
			{
		    	$Tot += $val['count'];
				if($Max < $val['count']){
					$Max = $val['count'];
				}
			}
			$IntVals[0] = ($Max-$Min)/3;
			$IntVals[1] = ($Max-$Min)/1.5;
			
		    	
		  	//initialisation du svg
			$svg = new SvgDocument("100%", "100%","","","","SVGglobal","onload=\"\""); 	
		  	
		  	//ajoute les liens avec les scripts
		  	//$svg->addChild(new SvgScript(jsPathWeb."svgTagCloud.js"));
		  	$svg->addChild(new SvgScript(jsPathWeb."ajax.js"));
			
			//echo $Max.", ".$Min.", ".$Tot.", ".$nb.", ".$IntVals[0].", ".$IntVals[1];
			//création des liens
			$i = 1;
			//$prems = true;
			//construit le groupe graphique
			$g = new SvgGroup("","","tag_".$i,"onclick=\"alert('toto')\"");
			foreach($book as  $val)
			{
				if($this->trace)
					echo "TagCloud:GetSvg:val=".$val['tag']."<br/>";
											
				//récupère la classe du tag
				$class = $this->GetClass($val['count'],"",$Max,$Min,$IntVals);
			  	//construit les coordonnées du texte
			  	$x=$this->marge*$i;
			  	$y=$this->marge*$i;
			  	//ajoute le texte au groupe
			  	$g->addChild(new SvgText($x,$y,$val['tag'],"fill:black;font-size:".$val['count']."pt;"));
				
				$i ++;
			}
		}
	  	//ajoute le groupe au graphique global
	  	$svg->addChild($g);
		
		//retourne le svg global
		$svg->printElement();
	}

	function GetClass($nb,$groupe,$Max,$Min,$IntVals) {

		$class = "";
		
		if ($nb <= $Min) {
		   $class = "smallestTag".$groupe;
		} elseif ($nb > $Min and $nb <= $IntVals[0]) {
		   $class = "smallTag".$groupe;
		} elseif ($nb > $IntVals[0] and $nb <= $IntVals[1]) {
		   $class = "mediumTag".$groupe;
		} elseif ($nb > $IntVals[1] and $nb < $Max) {
		   $class = "largeTag".$groupe;
		} elseif ($nb >= $Max) {
		   $class = "largestTag".$groupe;
		}

		//non prise en compte de la pondération
		$class = "smallTag".$groupe;
		
		return $class;
	}
	  
  }

?>