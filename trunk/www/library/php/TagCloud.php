<?php
class TagCloud {
  private $site;
  private $trace;
  public $marge=100;
  public $xentre_page=64;
  public $yentre_page=128;
  public $width=200;
  public $heigth=200;
  public $font_size=10;
  public $width_lien=2;
  public $xTrans;
  public $yTrans;
  public $xTC;
  public $yTC;
  public $xTagG;
  public $xTagD;
  public $arrTags;
  public $arrPosts;
  public $IntVals;
  public $TagNb;
  public $TagNbMax;
  public $TagNbMin;
  public $TagDateMax;
  public $TagDateDeb;
  public $TagRectHaut=30;
  public $TagCircleRay=30;
  public $TagNbTot;
  public $PostNb;
  public $PostCarMax;
  public $PostLargMax;
  private $oDlcs;
    
  function __tostring() {
    return "Cette classe permet de définir et manipuler un TagCloud.<br/>";
    }

  function __construct($oDlcs) {
    $this->trace = TRACE;
    $this->oDlcs = $oDlcs;
    date_default_timezone_set('UTC');		
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
  
	
 	 public function GetSvgPost($login,$ShowAll,$TempsVide,$DateDeb,$DateFin,$NbDeb,$NbFin)
	{
		//récupère les Posts
		$Posts = $this->GetPosts($login);
		if($Posts) {
        	if($this->trace)
		    	echo "TagCloud:GetSvgPost:book".print_r($Posts)."<br/>";

			//calcul les posts
			$this->CalculPosts($Posts,$DateDeb,$DateFin,$NbDeb,$NbFin);

			//calcul du javascript d'initialisation
			//le onload ne marche pas quand on charge le svg dans du xul
			//$js = "onload=\"InitOutilsParams(".$this->TagNbMin.",".$this->TagNbMax.",'".date_format($this->TagDateDeb, 'Y-m-d')."','".date_format($this->TagDateFin, 'Y-m-d')."');\"";
			$js = " TagNbMin='".$this->TagNbMin."' TagNbMax='".$this->TagNbMax."' TagDateDeb='".date_format($this->TagDateDeb, 'Y-m-d')."' TagDateFin='".date_format($this->TagDateFin, 'Y-m-d')."' ";
			
			//initialisation du svg
			$svg = new SvgDocument("600","600","","","","SVGglobal",$js); 	
		  	
		  	//ajoute les liens avec les scripts
		  	$svg->addChild(new SvgScript(jsPathWeb."ajax.js"));
		  	$svg->addChild(new SvgScript(jsPathWeb."tagcloud.js"));
		  	
			//vérifie s'il y a des posts
		  	if($this->PostLargMax>0){
				//echo $Max.", ".$Min.", ".$Tot.", ".$nb.", ".$IntVals[0].", ".$IntVals[1];
			  	//construit la grille du tagcloud
			  	$hPost = 32; 
			  	//défini la taille du graphique
				$this->width = $this->PostLargMax;
				//défini le milieu du graphique = racine centrale
			  	$cxTC = $this->width/2;
				//défini la place de la première couche
			  	$this->yTC = 10;
	
				//initialisation des groupe de graphique
				$couches = new SvgGroup("","","SVGCouche_".$login);
				$lignePost = new SvgGroup("","","SVGLignePost_".$login);
				
				//construction des lignes de tag
				$i = 1;
				foreach($this->arrPosts as  $post)
				{
					if($TempsVide){
					  	//construction de la grille de temps sans tag
						$this->GetGrilleTemps($couches,$post["diff"]);			  	
					}
					
				  	//ajoute une ligne de tag
				  	$lignePost->addChild($this->GetLignePost($post,$cxTC));
					
					$i ++;
				}
				//ajoute les groupes graphiques
				$svg->addChild($couches);
				$svg->addChild($lignePost);
				
				//redimensionne le svg
				$svg = $this->RedimSvg($ShowAll,$svg,$this->width);
		  	}else{
		  		$svg->addChild(new SvgText(30,30,"AUCUN POST","fill:black;font-size:30;"));
		  	}
			//retourne le svg global
			$svg->printElement();
		}

	}
	
	public function RedimSvg($ShowAll,$svg,$svgWidth){
				
		if($ShowAll){
			$svg->mPreserveAspectRatio="xMinYMin meet";
			//$svg->mViewBox = $this->xTagG." 0 ".($this->xTagD)." ".($this->yTC)."";
			$svg->mViewBox = "0 0 ".($this->xTagD)." ".($this->yTC)."";
		}else{
			$svg->mHeight=$this->yTC;
			$svg->mWidth=$svgWidth;
		}
		return $svg;
	}
	
 	 public function GetSvgTag($login,$ShowAll,$NbDeb,$NbFin)
	{
				
		//récupère les tags
		$tags = $this->GetTags($login);
		if($tags) {
        	if($this->trace)
		    	echo "TagCloud:GetSvgTag:book".print_r($tags)."<br/>";

			//calcul les tags
			$this->CalculTags($tags,$NbDeb,$NbFin);
			
			//calcul les interval du tagcloud
			$js = " TagNbMin='".$this->TagNbMin."' TagNbMax='".$this->TagNbMax."' TagDateDeb='".date_format(new DateTime(), 'Y-m-d')."' TagDateFin='".date_format(new DateTime(), 'Y-m-d')."' ";
			
		  	//initialisation du svg
			$svg = new SvgDocument("800","100","","","","SVGglobal",$js); 	
		  	
		  	//ajoute les liens avec les scripts
		  	//$svg->addChild(new SvgScript(jsPathWeb."svgTagCloud.js"));
		  	$svg->addChild(new SvgScript(jsPathWeb."TagCloud.js"));
		  	
			//vérifie s'il y a des tags
		  	if($this->nbTag>0){
				//défini la taille de la bulle minimum
				$this->TagCircleRay = $this->PostCarMax/2;
				//défini la place de la première bulle
			  	$this->yTC = $this->TagCircleRay*$this->TagNbMax;
			  	$this->xTC = 20;
				
				//construction des lignes de tag
			  	$ligneTag = $this->GetLigneTags();

			  	//ajoute les groupes graphiques
				$svg->addChild($ligneTag);
				
				
				//redimensionne le svg
			  	$this->yTC += $this->TagCircleRay*$this->TagNbMax;
			  	//défini la taille du graphique
				$svgWidth = $this->xTC;
			  	$this->xTagG = 10;
			  	$this->xTagD = $this->xTC;
			  	$svg = $this->RedimSvg($ShowAll,$svg,$svgWidth);
		  	}else{
		  		$svg->addChild(new SvgText(30,30,"AUCUN TAG","fill:black;font-size:30;"));
		  	}
			//retourne le svg global
			$svg->printElement();
		}

	}
			
	function GetGrilleTemps($svg,$arrDiff){

		$totNb = 0;
		$hTemps = 5;
		foreach($arrDiff as  $d=>$nb)
		{
			//ajoute les interval de temps qui ne sont pas vide
			$style = "fill-opacity:1;";
			if($nb>0){
				//calcul l'identifiant
				$id = $d."_".$nb;
				switch($d) {	
		            case 'years':
		            	$style .=  "fill:black;";
		            	$height = $nb+$hTemps;//*60*60*24/10000;
		            	$svg->addChild(new SvgRect(0,$this->yTC,$this->width,$height,$style,"","",$id));
		                break;
		            case 'weeks':
		                break;
		            case 'days':
		            	$height = $nb/$hTemps;//*60*60*24/10000;
		            	$style .=  "fill:dimgrey;";
					  	$svg->addChild(new SvgRect(0,$this->yTC,$this->width,$height,$style,"","",$id));
		                break;
		            case 'hours':
		            	$height = $nb/$hTemps;//*60*60/1000;
		            	$style .=  "fill:grey;";
					  	$svg->addChild(new SvgRect(0,$this->yTC,$this->width,$height,$style,"","",$id));
		                break;
		            case 'minutes':
		            	$height = $nb/$hTemps;//*60/100;
		            	$style .=  "fill:darkgrey;";
					  	$svg->addChild(new SvgRect(0,$this->yTC,$this->width,$height,$style,"","",$id));
		                break;
		            case 'seconds':
		            	$height = $nb/$hTemps;//*10;
		            	$style .=  "fill:lightgrey;";
					  	$svg->addChild(new SvgRect(0,$this->yTC,$this->width,$height,$style,"","",$id));
		                break;
		        }
		        //met à jour la profondeur
		        $this->yTC += $height;
			}						
		}
	}
	
	function CalculTags($tags,$NbDeb,$NbFin){
			//calcul les intervales
			$this->nbTag=0;
			$this->arrTags=array();
			foreach($tags as  $tag)
			{
				$nb = $tag->description+0;//+0 : gérer des nombres
				//vérifie que le nb est dans l'interval
				if($nb>=$NbDeb && $nb<=$NbFin){
					$this->nbTag ++;
					$color = $this->rgb2hex(array(rand(0, 255),rand(0, 255),rand(0, 255)));
					$style = "fill:#".$color.";";
					array_push($this->arrTags, array("tag"=>$tag->title,"nb"=>$nb,"style"=>$style));
			    	$this->TagNbTot += $nb;
					//enregistre les intervalles d'occurence
			    	if($this->TagNbMax < $nb)
						$this->TagNbMax = $nb;
					if($this->TagNbMin > $nb  || !$this->TagNbMin)
						$this->TagNbMin=$nb;				
					//calcul le nb de caractère maximum d'une ligne
					$nbCar = $this->GetLargeurBoiteTexte($tag->title);	
					if($this->PostCarMax < $nbCar){
						$this->PostCarMax = $nbCar;
					}				
				}
			}
			$this->IntVals[0] = ($this->TagNbMax-$Min)/3;
			$this->IntVals[1] = ($this->TagNbMax-$Min)/1.5;		
	}
	
	function CalculPosts($Posts,$DateDeb,$DateFin,$NbDeb,$NbFin){
		
		//calcul les dates et les max
		$this->PostNb=0;
		$this->arrPosts=array();
		$this->arrTags=array();
		$arrTags=array();
		$arrPosts=array();
		$startTime = time();
		//prise en compte des intervalles de date
		if($DateDeb)
			$dDeb = new DateTime($DateDeb);
		if($DateFin)
			$dFin = new DateTime($DateFin);
		foreach($Posts as  $post)
		{
			//http://fr2.php.net/manual/fr/book.datetime.php#84699
			$dPost = new DateTime($post->pubDate);
			//vérifie les dates
			if($this->VerifInDate($dPost,$dDeb,$dFin)){
				//incrémente le nombre de post
				$this->PostNb++;
				//calcul l'interval de temps entre les posts
	    		$dDiff  = $this->calc_tl($dPost->format('U'),$startTime);
	    		//pour calculer le nouvel interval
	    		$startTime = $dPost->format('U');
	    		//enregitre la première date comme date minimum
	    		if($this->PostNb==1)
					$this->TagDateDeb = $dPost;
	    		
				array_push($this->arrPosts, array("post"=>$post,"date"=>$dPost,"diff"=>$dDiff,"tags"=>$post->category));
				foreach($post->category as $cat){
					//construction de la clef
					$keyTag = $this->strtokey($cat."");
					//vérifie si le tag est déjà conservé
					if (!$arrTags[$keyTag]) {
						//calcul la couleur
						$color = $this->rgb2hex(array(rand(0, 255),rand(0, 255),rand(0, 255)));
						$style = "fill:#".$color.";";
						//conserve le tag
						$arrTags[$keyTag]= array("tag"=>$cat,"nb"=>1,"style"=>$style);
					}else{
						//incrémente le nombre de tag 
						$arrTags[$keyTag]["nb"]++;
					}
				}
			}
		}
		//suprime les tag qui ne sont pas dans l'interval
		foreach($arrTags as  $tag)
		{
			if($tag["nb"]>=$NbDeb && $tag["nb"]<=$NbFin){
				array_push($this->arrTags, $tag);
				//enregistre les intervalles d'occurence
				if($tag["nb"]>$this->TagNbMax)
					$this->TagNbMax=$tag["nb"];				
				if($tag["nb"]<$this->TagNbMin || !$this->TagNbMin)
					$this->TagNbMin=$tag["nb"];				
			}
		}
		//suprime les posts qui ne sont pas dans l'interval
		foreach($this->arrPosts as $post)
		{
			$TagIn = false;
			$nbCar = 0;
			$nb = 0;
			$PostLargMax=0;
			foreach($post["post"]->category as $cat){
				foreach($arrTags as  $tag)
				{
					if($cat.""==$tag["tag"].""){
						if($tag["nb"]>=$NbDeb && $tag["nb"]<=$NbFin){
							$TagIn = true;
							//calcul la taille max de la ligne
							$PostLargMax += $this->GetLargeurBoiteTexte($cat);	
						}
					}else{
						//ajoute un bloc pour chaque tag vide
						$PostLargMax += $this->font_size/4;
					}	
				}
			}
			if($TagIn){
				//met à jour le tableau des posts
				array_push($arrPosts, $post);				
				//enregistre les intervalles de date
				if($post["date"]>$this->TagDateFin)
					$this->TagDateFin=$post["date"];				
				if($post["date"]<$this->TagDateDeb)
					$this->TagDateDeb=$post["date"];				
			}
			if($this->PostLargMax < $PostLargMax){
				$this->PostLargMax = $PostLargMax;
			}
		}
		$this->arrPosts = $arrPosts;
	}

	function VerifInDate($dPost,$dDeb,$dFin){
		//vérifie si on a des dates
		if(!$dDeb && !$dFin)
			return true;
		//vérifie si on a une date de début
		if($dDeb && !$dFin && $dDeb<=$dPost)
				return true;
		//vérifie si on a une date de fin
		if(!$dDeb && $dFin && $dFin>=$dPost)
			return true;
		//vérifie si on a une date de début et de fin
		if($dDeb && $dFin && $dDeb<=$dPost && $dFin>=$dPost)
			return true;
		//si aucun cas n'est remplie
		return false;
	}
	
	function GetLigneTags(){
		//création de la ligne de post
		$g = new SvgGroup("","","lignetag_"."");
		//création des emplacements de tag
		$j=1;
		
		foreach($this->arrTags as  $tag)
		{
								
			//ajoute le cercle
		  	$g->addChild($this->AddTagCircle($j, $tag));
		  	
			$j++;
		}
		
		return $g;
	}

	function GetLignePost($post,$cxTC){
		//création de la ligne de post
		$g = new SvgGroup("","","post_".$post["post"]->guid,"onclick=\"alert('".$this->SVG_entities($post["post"]->title)."')\"");
		//création des emplacements de tag
		$j=1;
		//mise à jour des valeurs de position droite gauche
		$this->xTagG = $cxTC - 10;
		$this->xTagD = $cxTC + 10;
		
		foreach($this->arrTags as  $tag)
		{
						
			//vérifie si on ajoute le texte
			
			$TagIn = false;
			foreach($post["post"]->category as $cat){
				if($cat.""==$tag["tag"].""){
					$TagIn=true;
				}
			}			
			
			//ajoute le rectangle
		  	$g->addChild($this->AddTagRect($j, $tag, $TagIn));
		  	
		  	$j++;
		}
		//met à jour la profondeur
		$this->yTC += $this->TagRectHaut;
		
		return $g;
	}
	
	function AddTagCircle($j, $tag) {


		$g = new SvgGroup("","","TagCircle_".$j,"");
		
		//calcul le rayon
		$r = $this->TagCircleRay*$tag["nb"];
  		//met à jour le placement
		$this->xTC += $r;
		//et la taille de la police
		$fontsize = ($tag["nb"]*$this->font_size*2);
		
		//calcul le style du texte et son placement
		$xT = $this->xTC-($r)+$fontsize;
		$style = $tag["style"]."fill-opacity:0.3;";
		$lib = $this->SVG_entities($tag["tag"]);
		
		//ajoute le cercle
		$script = "onclick=\"alert('".$lib." (".$tag["nb"].") ')\"";
		$script = " onmouseover=\"GrossiMaigriTag(evt)\"";
		$script .= " onmouseleave=\"MaigriTag(evt)\"";
		$script .= " grossi='non'";
		$g->addChild(new SvgCircle($this->xTC,$this->yTC,$r,$style,"",$script));
		
	  	//ajoute le texte
		$s = "fill:black;font-size:".$fontsize."px;";
  		//$g->addChild(new SvgText($xT,$this->yTC,$lib,$s,"scale:2;"));
  		$g->addChild(new SvgText($xT,$this->yTC,$lib,$s));
  		
  		//met à jour le placement
		$this->xTC += $r;
			 
		return $g;
		
	}

	function GetLargeurBoiteTexte($str){
		return (strlen($str)*$this->font_size)+20;
	}
	
	function AddTagRect($j, $tag, $TagIn) {


		$g = new SvgGroup("","","TagRect_".$j,"");
		
		//calcul la taille du rectangle
		if($TagIn){
			$larg = $this->GetLargeurBoiteTexte($tag["tag"]);
		}else{
			$larg = $this->font_size;
		}
				
		//calcul le placement gauche et droite
		if ($j%2 == 1){
			//gauche
			$x = $this->xTagG - $larg;
			$this->xTagG = $x;
			if($TagIn){
				$xT = $x + $this->font_size;
				$style = $tag["style"]."fill-opacity:0.3;";
			}else{
				$style = $tag["style"]."fill-opacity:0.1;";				
			}
		}else{
			//droite
			$x = $this->xTagD;
			$this->xTagD = $x + $larg;
			if($TagIn){
				$xT = $x + $this->font_size;
				$style = $tag["style"]."fill-opacity:0.3;";
			}else{
				$style = $tag["style"]."fill-opacity:0.1;";				
			}
		}
				
		//ajoute le rectangle
	  	$g->addChild(new SvgRect($x,$this->yTC,$larg,$this->TagRectHaut,$style));
		
	  	//ajoute la taille du bloc texte
		if($TagIn){
			$lib = $this->SVG_entities($tag["tag"]);
	  		$g->addChild(new SvgText($xT,$this->yTC+($this->font_size*2),$lib,"fill:black;font-size:".$this->font_size.";"));
		}
		
		return $g;
		
	}
	
	
	function GetTags($login) {

		//récupère le boobkmark
		//$xml = simplexml_load_file(PathRoot."/tmp/tags/".$login.".xml");
    	$xml = simplexml_load_string($this->oDlcs->GetUserTags($login));
		//recupére les tags
        $tags = $xml->xpath('/rss/channel/item');        
        return $tags;
		
	}

	function GetPosts($login) {

    	$xml = simplexml_load_string($this->oDlcs->GetUserBookmark($login));
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
	
	public function SVG_entities($str)
	{
		//$str = htmlentities($str, ENT_QUOTES);
		$str = str_replace("'", "\'", $str);   
		$str = utf8_decode($str);
	    return preg_replace(array("'&'", "'\"'", "'<'"), array('&#38;', '&#34;','&lt;'), $str);
	}
    
  function stripAccents($string)
  {
    return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
		 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
  }

  function strtokey($str)
  {
    for ($iii = 0; $iii < strlen($str); $iii++)
      if (ord($str[$iii]) == 146 || ord($str[$iii]) == 156)
	$str[$iii] = '-';
    $key = str_replace("_", "-", $str);
    $key = str_replace("'", "-", $key);
    $key = str_replace("`", "-", $key);
    $key = str_replace(".", "-", $key);
    $key = str_replace(" ", "-", $key);
    $key = str_replace(",", "-", $key);
    $key = str_replace("{}", "_", $key);
    $key = str_replace("(", "_", $key);
    $key = str_replace(")", "_", $key);
    $key = str_replace("--", "-", $key);
    $key = str_replace("- -", "-", $key);
    $key = str_replace("<i>", "", $key);
    $key = str_replace("</i>", "", $key);
    $key = str_replace(":", "", $key);
    $key = str_replace("«", "", $key);
    $key = str_replace("»", "", $key);
    $key = str_replace("/", "", $key);
    $key = str_replace("“", "", $key);
    $key = str_replace("”", "", $key);
        
    $key = strtolower($key);
    return $this->stripAccents($key);
  }
		
  }

?>