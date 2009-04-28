<?php
class TagCloud {
  private $site;
  private $trace;
  public $marge=40;
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
  public $LargMax;
  private $oDlcs;
  public $lang;
  public $login;
  
  function __tostring() {
    return "Cette classe permet de d�finir et manipuler un TagCloud.<br/>";
    }

  function __construct($objSite,$oDlcs,$lang,$login) {
    $this->trace = TRACE;
    $this->oDlcs = $oDlcs;
    $this->lang=$lang;
    $this->login=$login;
    $this->site=$objSite;
    date_default_timezone_set('UTC');		
  }


  	public function SauveBookmarkNetwork($uti,$pwd)
	{
		$oDlcs = new PhpDelicious($uti,$pwd);
		$network = simplexml_load_string($oDlcs->GetNetworkMembers($uti));
		$oDlcs->GetUserTags($uti);
		
		//r�cup�re les tags du network
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
		//r�cup�re les Posts
		$Posts = $this->GetPosts($login);
		if($Posts) {
        	if($this->trace)
		    	echo "TagCloud:GetSvgPost:book".print_r($Posts)."<br/>";

		   	$this->TagDateFin=new DateTime($DateFin);				
			$this->TagDateDeb=new DateTime($DateDeb);				
		    	
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
		  	
			//v�rifie s'il y a des posts
		  	if($this->LargMax>0){
				//echo $Max.", ".$Min.", ".$Tot.", ".$nb.", ".$IntVals[0].", ".$IntVals[1];
			  	//construit la grille du tagcloud
			  	$hPost = 32; 
			  	//d�fini la taille du graphique
				$this->width = $this->LargMax+$this->marge;
				//d�fini le milieu du graphique = racine centrale
			  	$cxTC = $this->width/2;
				//d�fini la place de la premi�re couche
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
	
	public function RedimSvg($ShowAll,$svg,$svgWidth,$type="post"){
				
		if($ShowAll){
			if($type=="post"){
				$svg->mPreserveAspectRatio="xMinYMin meet";
				$svg->mViewBox = "0 0 ".($this->xTagD)." ".($this->yTC)."";
			}
			if($type=="tag"){
				$svg->mHeight="600";
				$svg->mWidth=$svgWidth;
				$svg->mPreserveAspectRatio="xMinYMin meet";
				//$svg->mViewBox = $this->xTagG." 0 ".($this->xTagD)." ".($this->yTC)."";				
				$svg->mViewBox = "0 0 ".($this->xTC)." ".($this->yTC)."";				
			}
		}else{
			$svg->mHeight=$this->yTC;
			$svg->mWidth=$svgWidth;
		}
		return $svg;
	}
	
 	 public function GetSvgTag($login,$ShowAll,$NbDeb,$NbFin)
	{
				
		//r�cup�re les tags
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
		  	
			//v�rifie s'il y a des tags
		  	if($this->nbTag>0){
				//d�fini la taille de la bulle minimum
				$this->TagCircleRay = $this->PostCarMax/2;
				//d�fini la place de la premi�re bulle
			  	$this->yTC = $this->marge;
			  	$this->xTC = $this->marge+$this->TagCircleRay*$this->GetClass($this->TagNbMax,"",100,2,$this->IntVals,true); //le bord du cercle le plus gros est � 0
				
				//construction des lignes de tag
			  	$ligneTag = $this->GetLigneTags();

			  	//ajoute les groupes graphiques
				$svg->addChild($ligneTag);
				
				//redimensionne le svg
				$svgWidth = $this->LargMax;
			  	$svg = $this->RedimSvg($ShowAll,$svg,$svgWidth,"tag");
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
		        //met � jour la profondeur
		        $this->yTC += $height;
			}						
		}
	}
	
	function CalculStyle($ieml){
		//vérifie la langue
		if($this->lang=="ieml"){
			if($ieml!=""){
				$style = "fill:crimson;fill-opacity:0.3;";
				//boucle sur les traductions disponibles
				foreach($ieml as $login=>$entry){
					//vérifie si il y a une traduction pour le login
					if($login==$this->login){
						$style = "fill:forestgreen;fill-opacity:0.3;";
						return $style;						
					}
					$style = "fill:darkorange;fill-opacity:0.3;";
				}
			}
		}else{
			$color = $this->rgb2hex(array(rand(0, 255),rand(0, 255),rand(0, 255)));
			$style = "fill:#".$color.";fill-opacity:0.3;";					
		}
		
		return $style;
	}

	function CalculScript($ieml){
		if($ieml!=""){
			$script = "onclick=\"";
			$trad = "no";
			//boucle sur les traductions disponibles
			foreach($ieml as $login=>$entry){
				//vérifie qu'on traite des logins
				if($login!="idFlux"){
					//vérifie si il y a une traduction pour le login
					if($login==$this->login){
						$script .= "VoirExagramme(this.parentNode.id,'".str_replace("'","\'",$entry["usl"])."',this.getAttribute('r'));";
						$trad = "uti";
					}else{
						$script .= "VoirLogin('".str_replace("'","\'",$login)."');";
						$trad = "net";
					}
				}
			}
			$script.="\"";
		}else{
			$trad = "no";
			$script = "onclick=\"alert('".$lib." (".$nb.")')\"";
		}
		//ajoute le type de traduction
		$trad = " trad='".$trad."' ";
		return $script.$trad;
	}
	
	function CalculTags($tags,$NbDeb,$NbFin){
		
		$sem = new Sem($this->site);
		
		//calcul les intervales
		$this->nbTag=0;
		$this->arrTags=array();
		foreach($tags as  $tag)
		{
			$nb = $tag->description+0;//+0 : g�rer des nombres
			//v�rifie que le nb est dans l'interval
			if($nb>=$NbDeb && $nb<=$NbFin){
				$this->nbTag ++;
				
				//récupère toute les traductions ieml
				$ieml = $sem->GetIemlTrad($tag->title); 
				
				//calcul le style
				$style = $this->CalculStyle($ieml);
				
				array_push($this->arrTags, array("tag"=>$tag->title,"nb"=>$nb,"style"=>$style,"ieml"=>$ieml));
		    	$this->TagNbTot += $nb;
				//enregistre les intervalles d'occurence
		    	if($this->TagNbMax < $nb)
					$this->TagNbMax = $nb;
				if($this->TagNbMin > $nb  || !$this->TagNbMin)
					$this->TagNbMin=$nb;				
				//calcul le nb de caract�re maximum d'une ligne
				$nbCar = $this->GetLargeurBoiteTexte($tag->title);	
				if($this->PostCarMax < $nbCar){
					$this->PostCarMax = $nbCar;
				}
			}
		}
		$this->IntVals[0] = ($this->TagNbMax-$this->TagNbMin)/3;
		$this->IntVals[1] = ($this->TagNbMax-$this->TagNbMin)/1.5;		
	}
	
	function CalculPosts($Posts,$DateDeb,$DateFin,$NbDeb,$NbFin){

		$sem = new Sem($this->site);
		
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
			//v�rifie les dates
			if($this->VerifInDate($dPost,$dDeb,$dFin)){
				//incr�mente le nombre de post
				$this->PostNb++;
				//calcul l'interval de temps entre les posts
	    		$dDiff  = $this->calc_tl($dPost->format('U'),$startTime);
	    		//pour calculer le nouvel interval
	    		$startTime = $dPost->format('U');
	    		//enregitre la premi�re date comme date minimum
	    		if($this->PostNb==1)
					$this->TagDateDeb = $dPost;
	    		
				array_push($arrPosts, array("post"=>$post,"date"=>$dPost,"diff"=>$dDiff,"tags"=>$post->category));
				foreach($post->category as $cat){
										
					//construction de la clef
					$keyTag = $this->strtokey($cat."");
					//v�rifie si le tag est d�j� conserv�
					if (!$arrTags[$keyTag]) {
						//récupère la traduction ieml
						$ieml = $sem->GetIemlTrad($cat[0]); 
						
						//calcul le style
						$style = $this->CalculStyle($ieml);

						//conserve le tag
						$arrTags[$keyTag]= array("tag"=>$cat,"nb"=>1,"style"=>$style,"ieml"=>$ieml);
					}else{
						//incr�mente le nombre de tag 
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
		foreach($arrPosts as $post)
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
				//met � jour le tableau des posts
				array_push($this->arrPosts, $post);				
				//enregistre les intervalles de date
				if($post["date"]>$this->TagDateFin)
					$this->TagDateFin=$post["date"];				
				if($post["date"]<$this->TagDateDeb)
					$this->TagDateDeb=$post["date"];				
				//calcul la largeur maximal
				if($this->LargMax < $PostLargMax){
					$this->LargMax = $PostLargMax;
				}
			}
		}
		$this->arrPosts = $arrPosts;
	}

	function VerifInDate($dPost,$dDeb,$dFin){
		//v�rifie si on a des dates
		if(!$dDeb && !$dFin)
			return true;
		//v�rifie si on a une date de d�but
		if($dDeb && !$dFin && $dDeb<=$dPost)
				return true;
		//v�rifie si on a une date de fin
		if(!$dDeb && $dFin && $dFin>=$dPost)
			return true;
		//v�rifie si on a une date de d�but et de fin
		if($dDeb && $dFin && $dDeb<=$dPost && $dFin>=$dPost)
			return true;
		//si aucun cas n'est remplie
		return false;
	}
	
	function GetLigneTags(){
		//cr�ation de la ligne de post
		$g = new SvgGroup("","","lignetag_"."");
		//cr�ation des emplacements de tag
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
		//cr�ation de la ligne de post
		$g = new SvgGroup("","","post_".$post["post"]->guid,"onclick=\"alert('".$this->SVG_entities($post["post"]->title)."')\"");
		//cr�ation des emplacements de tag
		$j=1;
		//mise � jour des valeurs de position droite gauche
		$this->xTagG = $cxTC - 10;
		$this->xTagD = $cxTC + 10;
		
		foreach($this->arrTags as  $tag)
		{
						
			//v�rifie si on ajoute le texte
			
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
		//met � jour la profondeur
		$this->yTC += $this->TagRectHaut;
		
		return $g;
	}
	
	function AddTagCircle($j, $tag, $interval=true) {


		$g = new SvgGroup("","","TagCircle_".$j,"");
		
		//calcul le rayon
		if($interval){
			$nb = $this->GetClass($tag["nb"],"",$this->TagNbMax,$this->TagNbMin,$this->IntVals,true);
			$r = $this->TagCircleRay*$nb;
		}else{
			$r = $this->TagCircleRay*$tag["nb"];
			$nb = $tag["nb"];
		}
  		//met � jour le placement horizontal
		//$this->xTC += $r;
  		//met � jour le placement vertical
		$this->yTC += $r;
		//et la taille de la police
		$fontsize = ($nb*$this->font_size*2);
		
		//calcul le style du texte et son placement
		$xT = $this->xTC-($r)+$fontsize;
		$style = $tag["style"];
		$lib = $this->SVG_entities($tag["tag"]);
		
		//calcul le script
		$script = $this->CalculScript($tag["ieml"]);
		//$script = "onclick=\"alert('".$lib." (".$nb.") ".str_replace("'","\'",$tag["ieml"])." ')\"";
		//$script = " onmouseover=\"GrossiMaigriTag(evt)\"";
		//$script .= " onmouseleave=\"MaigriTag(evt)\"";
		//$script .= " grossi='non'";
		
		//ajoute le cercle
		$g->addChild(new SvgCircle($this->xTC,$this->yTC,$r,$style,"",$script,$tag["ieml"]["idFlux"]));
		
	  	//ajoute le texte
		$s = "fill:black;font-size:".$fontsize."px;";
  		//$g->addChild(new SvgText($xT,$this->yTC,$lib,$s,"scale:2;"));
  		$g->addChild(new SvgText($xT,$this->yTC,$lib,$s));
  		
  		
		//ajoute si la langue est ieml
  		if($this->lang=="ieml"){
  			//les traduction proposées
	  		$g->addChild($this->AddTagTrad($j, $tag["ieml"], $r, $fontsize));
  			//l'exagramme 
  			$g->addChild($this->AddTagExa($j, $tag["ieml"], $r));
  		}
  		
		//enregistre la largeur maximale
		$maxLarg = $this->xTC+($r*2);
		if($this->LargMax<$maxLarg)	$this->LargMax=$maxLarg;
  		
  		//met � jour le placement horizontal
		//$this->xTC += $r;
  		//met � jour le placement vertical
		$this->yTC += $r;
		
		return $g;
		
	}


	function AddTagExa($j, $ieml, $r){

		$g = new SvgGroup("","","TagExa_".$j,"");
		$exaHeight = 0;
		
  		//seulement pour l'utilisateur loguer
  		$usl = $ieml[$this->login]["usl"];

  		if($usl){
			$exa = new Exagramme($this->site,$usl,true,$r,false,$this->xTC,$this->yTC+$r); 	
			$exa->marge=0;
			$svgExa = $exa->GetSequence();
			$exaHeight = $svgExa->mHeight;
			//enregistre la largeur maximale
			if($this->LargMax<$svgExa->mWidth)
				$this->LargMax=$svgExa->mWidth;
			$g->addChild($svgExa); 
	  		//met � jour le placement vertical
			$this->yTC += $exaHeight;
  		}
			
		return $g;
	}

	
	function AddTagTrad($j, $ieml, $r, $fontsize){

		$g = new SvgGroup("","","TagTrad_".$j,"");

		//le titre du bloc
		$style = "fill:black;font-size:".$fontsize."px;";
		$g->addChild(new SvgText($this->xTC+$r,$this->yTC,"Auteur(s) de traduction",$style));
  		//met � jour le placement vertical
		$this->yTC += $fontsize;
  		
		//boucle sur les traductions disponibles
		$i=0;
		foreach($ieml as $login=>$entry){
			//vérifie qu'on traite des logins
			if($login!="idFlux"){
	  			$usl = $entry["usl"];
				
		  		//vérifie si il y a une traduction pour le login
		  		if($usl){
	  				//affiche le login avec un lien pour montrer la traduction
					$script .= "VoirLogin('".str_replace("'","\'",$login)."');";
					$g->addChild(new SvgText($this->xTC+$r,$this->yTC,$login,$style));
  					//met � jour le placement vertical
					$this->yTC += $fontsize;
  					$i++;		
		  		}
			}
		}
  		//met � jour le placement vertical
		$this->yTC -= $fontsize;

		//vérifie s'il y a au moins une traduction
		if($i==0){
			$g = new SvgGroup("","","TagTrad_".$j,"");			
		}
		
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

		//calcul le script
		$script = $this->CalculScript($tag["ieml"]);
		
		//ajoute le rectangle
	  	$g->addChild(new SvgRect($x,$this->yTC,$larg,$this->TagRectHaut,$style,"",$script));
		
	  	//ajoute la taille du bloc texte
		if($TagIn){
			$lib = $this->SVG_entities($tag["tag"]);
	  		$g->addChild(new SvgText($xT,$this->yTC+($this->font_size*2),$lib,"fill:black;font-size:".$this->font_size.";"));
		}
		
		return $g;
		
	}
	
	
	function GetTags($login) {

		//r�cup�re le boobkmark
    	$xml = simplexml_load_string($this->oDlcs->GetUserTags($login));
    	//recup�re les tags
        $tags = $xml->xpath('/rss/channel/item');        
        return $tags;
		
	}

	function GetPosts($login) {

    	$xml = simplexml_load_string($this->oDlcs->GetUserBookmark($login));
		//recup�re les tags
        $posts = $xml->xpath('/rss/channel/item');        
        return $posts;
		
	}
	
	function GetClass($nb,$groupe,$Max,$Min,$IntVals,$num=false) {

		$class = "";
		
		if ($nb <= $Min) {
			if($num)
				$class = 0.5;
			else
			   $class = "smallestTag".$groupe;
		} elseif ($nb > $Min and $nb <= $IntVals[0]) {
			if($num)
				$class = 1;
			else
				$class = "smallTag".$groupe;
		} elseif ($nb > $IntVals[0] and $nb <= $IntVals[1]) {
			if($num)
				$class = 3/2;
			else
				$class = "mediumTag".$groupe;
		} elseif ($nb > $IntVals[1] and $nb < $Max) {
			if($num)
				$class = 4/2;
			else
				$class = "largeTag".$groupe;
		} elseif ($nb >= $Max) {
			if($num)
				$class = 5/2;
			else
				$class = "largestTag".$groupe;
		}

		//non prise en compte de la pond�ration
		//$class = "smallTag".$groupe;
		
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
    return strtr($string,'���������������������������������������������������',
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
    $key = str_replace("�", "", $key);
    $key = str_replace("�", "", $key);
    $key = str_replace("/", "", $key);
    $key = str_replace("�", "", $key);
    $key = str_replace("�", "", $key);
        
    $key = strtolower($key);
    return $this->stripAccents($key);
  }
		
  }

?>