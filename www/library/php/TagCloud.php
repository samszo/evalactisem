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


  	public function SauveBookmarkNetwork($uti,$pwd)
	{
		$oDlcs = new PhpDelicious($uti,$pwd);
		$network = simplexml_load_string($oDlcs->GetNetworkMembers($uti));
		$oDlcs->GetUserTags($uti);
		
		//récupère les tags du network
		foreach($network->channel->item as  $NetUti)
		{
			$this->SauveBookmark($NetUti->title,$oDlcs);
		}
		
	}
  
  	public function SauveBookmark($uti,$oDlcs)
	{
		$oDlcs->GetNetworkMembers($uti);
		$oDlcs->GetUserTags($uti);
		$oDlcs->GetUserBookmark($uti);
	}
  
  
 	 public function GetSvg($login)
	{
		
		//Calcul les intervalles de taille
		$Max = 0;
		$Min = 1;
		$Tot = 1;
		$jsMaxMot="";
		$liste="";
		
		//récupère les tags
		$tags = true;//$this->GetTags($login);
		if($tags) {
        	if($this->trace)
		    	echo "TagCloud:GetSvg:book".print_r($tags)."<br/>";

			//calcul les intervales
			$nbTag=0;
			$arrTags=array();
			/*
			foreach($tags as  $tag)
			{
				$nbTag ++;
				$nb = $tag->description+0;//+0 : gérer des nombres
				$color = $this->rgb2hex(array(rand(0, 255),rand(0, 255),rand(0, 255)));
				$style = "fill:#".$color.";";
				array_push($arrTags, array("tag"=>$tag->title,"nb"=>$nb,"style"=>$style));
		    	$Tot += $nb;
				if($Max < $nb){
					$Max = $nb;
				}
			}
			$IntVals[0] = ($Max-$Min)/3;
			$IntVals[1] = ($Max-$Min)/1.5;
			*/

			//récupère les posts
			$Posts = $this->GetPosts($login);
			$nbPost = count($Posts);
			
			//calcul les dates et les max
			$arrPosts=array();
			$dbl=array();
			$startTime = time();
			foreach($Posts as  $post)
			{
				//http://fr2.php.net/manual/fr/book.datetime.php#84699
				$dPost = new DateTime($post->pubDate);
				//$dPost = date_parse($post->pubDate);
    			$dDiff  = $this->calc_tl($dPost->format('U'),$startTime);				
				array_push($arrPosts, array("post"=>$post,"date"=>$dPost,"diff"=>$dDiff,"tags"=>$post->category));
				$nb = count($post->category);
				foreach($post->category as $cat){
					//supprime les doublons
					if (!in_array($cat."", $dbl)) {
	  					array_push($dbl,$cat."");
						//calcul la couleur
						$color = $this->rgb2hex(array(rand(0, 255),rand(0, 255),rand(0, 255)));
						$style = "fill:#".$color.";";
						//conserve le tag
						array_push($arrTags, array("tag"=>$cat,"nb"=>1,"style"=>$style));
					}else{
						$style = "fill:#".$color.";";
					}
				}
								
				if($nbMaxTagPost < $nb){
					$nbMaxTagPost = $nb;
				}
			}
			
		  	//initialisation du svg
			$svg = new SvgDocument("100%", "100%","","","","SVGglobal","onload=\"\""); 	
		  	
		  	//ajoute les liens avec les scripts
		  	//$svg->addChild(new SvgScript(jsPathWeb."svgTagCloud.js"));
		  	$svg->addChild(new SvgScript(jsPathWeb."ajax.js"));
			
			//echo $Max.", ".$Min.", ".$Tot.", ".$nb.", ".$IntVals[0].", ".$IntVals[1];
		  	//construit la grille du tagcloud
		  	$hPost = 600/$nbPost;
			$wTag = 500/$nbMaxTagPost;
		  	$cxTC = 600;
		  	$yTC = 0;
			$xd = 1;
			$xg = 1;
			$this->width_lien = $wTag/20;
			//construit le groupe graphique
			$i = 1;
			foreach($arrPosts as  $post)
			{
				//ajoute le groupe au graphique global
			  	$svg->addChild($this->GetLigneTags($i,$arrTags,$post,$hPost,$wTag,$cxTC,$yTC,$xd,$xg));
				
				$i ++;
			}
		}
		
		//retourne le svg global
		$svg->printElement();
	}
	
	function GetLigneTags($i,$arrTags,$post,$hPost,$wTag,$cxTC,$yTC,$xd,$xg){
		//création de la ligne de post
		$g = new SvgGroup("","","post_".$i,"onclick=\"alert('this.id')\"");
		//création des emplacements de tag
		$j=1;
		foreach($arrTags as  $tag)
		{
			
			//calcul le placement haut et bas
			$y = $hPost*$i;
			
			//vérifie si on ajoute le texte
			//$key = array_search($tag["tag"], $post["tags"]);
			//$xp = "//item[category=\"".$tag["tag"]."\"]/category";
			//$TagIn = $post["post"]->xpath($xp);        
			$TagIn = false;
			foreach($post["post"]->category as $cat){
				if($cat.""==$tag["tag"].""){
					$TagIn=true;
				}
			}
			
			//calcul le rayon
			$r = $this->width_lien;
			
			//calcul le placement gauche et droite
			if ($j%2 == 1){
				//gauche
				$x= $cxTC-($this->width_lien*$j)-$this->width_lien-$xg;
				if($TagIn){
					$r = $wTag/2;
					$xT = $x-($r/2);
					$style = $tag["style"]."fill-opacity:0.3;";
				}else{
					$style = $tag["style"]."fill-opacity:0.1;";				
				}
			}else{
				//droite
				$x = $cxTC+($this->width_lien*$j)+$this->width_lien+$xd;
				//$x = $cxTC+$xd;
				//ajoute la taille du texte
				if($TagIn){
					$r = $wTag/2;
					$xT = $x-($r/2);
					$style = $tag["style"]."fill-opacity:0.3;";
				}else{
					$style = $tag["style"]."fill-opacity:0.1;";				
				}
			}
			
			//ajoute le cercle
		  	$g->addChild(new SvgCircle($x,$y,$r,$style));
			
		  	//ajoute la taille du bloc texte
			if($TagIn){
				$lib = $tag["tag"]." (".$i."_".$j.")";
		  		$g->addChild(new SvgText($xT,$y,$lib,"fill:black;font-size:10pt;"));
			}
		  	$j++;
		}
		return $g;
	}

	function GetTags($login) {

		//récupère le boobkmark
		$xml = simplexml_load_file(PathRoot."/tmp/tags/".$login.".xml");
		//recupére les tags
        $tags = $xml->xpath('/rss/channel/item');        
        return $tags;
		
	}

	function GetPosts($login) {

		//récupère le boobkmark
		$xml = simplexml_load_file(PathRoot."/tmp/bookmarks/".$login.".xml");
		//recupére les tags
        $posts = $xml->xpath('/rss/channel/item');        
        return $posts;
		
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

	//http://www.comscripts.com/sources/php.rgb-en-hexadecimal.56.html
	function rgb2hex($rgb){ 
	 if(!is_array($rgb)) { 
	  echo "Error : input must be an array"; 
	  return 0; 
	  } 
	  
	  $hex = ""; 
	  for($i=0; $i<3; $i++) { 
	  if( ($rgb[$i] > 255) || ($rgb[$i] < 0) ) { 
	  echo "Error : input must be between 0 and 255"; 
	  return 0; 
	  } 
	  $tmp = dechex($rgb[$i]); 
	  if(strlen($tmp) < 2) $hex .= "0". $tmp; 
	  else $hex .= $tmp; 
	  } 
	  
	  return $hex; 
	} 
	
//http://fr2.php.net/manual/fr/function.time.php#74766
	function calc_tl($t, $sT = 0, $sel = 'Y') {

        $sY = 31536000;
        $sW = 604800;
        $sD = 86400;
        $sH = 3600;
        $sM = 60;

        if($sT) {
            $t = ($sT - $t);
        }

        if($t <= 0) {
            $t = 0;
        }

        $bs[1] = ('1'^'9'); /* Backspace */

        switch(strtolower($sel)) {

            case 'y':
                $y = ((int)($t / $sY));
                $t = ($t - ($y * $sY));
                $r['string'] .= "{$y} years{$bs[$y]} ";
                $r['years'] = $y;
            case 'w':
                $w = ((int)($t / $sW));
                $t = ($t - ($w * $sW));
                $r['string'] .= "{$w} weeks{$bs[$w]} ";
                $r['weeks'] = $w;
            case 'd':
                $d = ((int)($t / $sD));
                $t = ($t - ($d * $sD));
                $r['string'] .= "{$d} days{$bs[$d]} ";
                $r['days'] = $d;
            case 'h':
                $h = ((int)($t / $sH));
                $t = ($t - ($h * $sH));
                $r['string'] .= "{$h} hours{$bs[$h]} ";
                $r['hours'] = $h;
            case 'm':
                $m = ((int)($t / $sM));
                $t = ($t - ($m * $sM));
                $r['string'] .= "{$m} minutes{$bs[$m]} ";
                $r['minutes'] = $m;
            case 's':
                $s = $t;
                $r['string'] .= "{$s} seconds{$bs[$s]} ";
                $r['seconds'] = $s;
            break;
            default:
                return calc_tl($t);
            break;
        }

        return $r;
    }
	
	
	
  }

?>