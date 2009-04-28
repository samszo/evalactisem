<?php
class Exagramme {
  private $trace;
  public $marge=50;
  public $x_exa = 100;
  public $y_exa = 230;
  public $x_entre_exa=128;
  public $y_entre_trait=10;
  public $y_entre_texte=100;
  public $width_trait=200;
  public $heigth_trait=20;
  public $font_size=10;
  public $width_lien=2;
  public $styleTexte="font-size:64px;font-style:normal;font-weight:normal;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;font-family:Bitstream Vera Sans";
  public $sem; 
  public $svg; 
  public $arrPrevTrait;
  public $showAll=true; 
  public $r; 
  public $doc; 
  public $site; 
  public $x; 
  public $y; 
  
  function __tostring() {
    return "Cette classe permet de d�finir et manipuler un Exagramme.<br/>";
    }

  function __construct($site,$code,$showAll=true,$r=-1,$doc=true,$x=0,$y=0) {
    $this->trace = TRACE;
    $this->site=$site;
    date_default_timezone_set('UTC');		
    $this->sem = new Sem($site);
    $this->sem->Parse($code);
    
    $this->showAll = $showAll;
    $this->r = $r;
    $this->doc = $doc;
    $this->x = $x;
    $this->y = $y; 
  }

  
	public function GetTrait($symbol){

	  	$posi = $this->sem->ExaParam[$symbol];
		for($i=1; $i<=6; $i++) {
			if($posi==$i){
				$arrExa[]=true;		
			}else{
				$arrExa[]=false;						
			}
		}
  		return $arrExa;
  	}

  	public function GetYang($x,$y)
	{
		$yin = new SvgRect($x,$y,$this->width_trait,$this->heigth_trait);		
		return $yin;
	}
  
  	public function GetYin($x,$y)
	{
		$yang = new SvgGroup();
		$yang->addChild(new SvgRect($x,$y,$this->width_trait/3,$this->heigth_trait));		
		$yang->addChild(new SvgRect($x+($this->width_trait/3*2),$y,$this->width_trait/3,$this->heigth_trait));		
		return $yang;
	}
	
	public function GetExa($arrTrait, $doc = true)
	{
		//initialisation du svg
		$yTrait=0;
		if($doc)
			$svg = new SvgDocument("600","600","","","","SVGExa_",$js);
		else
			$svg = new SvgGroup("","","SVGExa_".$this->x_exa."_".$yTrait);
		foreach($arrTrait as $trait){
			if($trait){
				$svg->addChild($this->GetYang($this->x_exa,$this->y_exa-$yTrait));
			}else{
				$svg->addChild($this->GetYin($this->x_exa,$this->y_exa-$yTrait));
			}
			$yTrait += $this->heigth_trait+$this->y_entre_trait;
		}
		//retourne le svg global
		if($doc)
			$svg->printElement();
		else
			return $svg;
	}


	//pour calculer à partir du parser
	public function GetSequence($arrGenOp="",$niv=0)
	{
		if($niv==0){
			//initialisation du svg
			$id= "SVGexa_".$this->sem->StarParse["expression"];
			if($this->doc)
				$this->svg = new SvgDocument("100%","100%","","","",$id,""); 	
			else
				$this->svg = new SvgFragment("100%","100%",$this->x,$this->y,"","","",$id);
			//ajoute le titre de la séquence
			$this->svg->addChild(new SvgText($this->x_exa, $this->marge, $this->sem->StarParse["expression"],$this->styleTexte));			
			//récupère les enfants de la séquence
			$arrGenOp=$this->sem->StarParse;
		}
		$i=0;
		foreach($arrGenOp as $couche=>$GenOp){
			
			//vérifie si on traite un complex
			if(eregi('complex(.*)',$couche)){
				//calcul la translation
				$this->x_exa = 200;
				if($i>0){
					$this->y_exa += $this->y_entre_texte + (6*($this->y_entre_trait+$this->heigth_trait));
				}
			}
			
			//vérifie si on traite le niveau 0
			if($couche=="genOpAtL0"){
				//vérifie si on n'est pas en bout de séquence
				if($GenOp["layerMark"]){
					$tag = $GenOp["symbol"]."";
					$arrTrait = $this->GetTrait($tag);
					//construction de l'exagramme
					$this->svg->addChild($this->GetExa($arrTrait,false));
					//construction de la légende
					$this->svg->addChild(new SvgText($this->x_exa, $this->y_exa+$this->y_entre_texte, $tag,$this->styleTexte));
					//ajoute la ponctuation
					$this->svg->addChild(new SvgText($this->x_exa+$this->font_size+$this->width_trait, $this->y_exa+$this->y_entre_texte, $GenOp["layerMark"],$this->styleTexte));
					
					
					//construction des fl�ches
					if($i==0){
						if($this->arrPrevTrait){
							//met à jour la translation
							$x=$this->x_exa-$this->x_entre_exa-$this->width_trait;
							//construit la flèche entre les deux séquences
							$this->svg->addChild($this->GetFleches($this->arrPrevTrait,$arrTrait,$x));
						}
					}
					//récupère l'exagramme suivant
					$arrNextTrait = $this->GetTrait($arrGenOp->genOpAtL0[$i+1]["symbol"]."");
					if($i<2){
						//ajoute une fleche entre les deux exagramme
						$this->svg->addChild($this->GetFleches($arrTrait,$arrNextTrait));
					}else{
						//conserve l'exagramme pour la couche suivante
						$this->arrPrevTrait=$arrTrait;												
					}
					
					//calcul la translation du graphique
					$this->x_exa+=$this->x_entre_exa+$this->width_trait;
					
				}
			}else{
				//traite les couches enfants
				$this->svg->addChild($this->GetSequence($GenOp,$niv+1));
				//vérifie s'il faut ajouter un symbol
				/*
				if($GenOp["symbol"]){
					$y = $this->y_exa+($this->y_entre_texte*($GenOp["layerNumber"]+1));
					$x = $this->x_exa-$this->x_entre_exa-$this->width_trait;
					$this->svg->addChild(new SvgText($x, $y, $GenOp["symbol"],$this->styleTexte));
					//ajoute la ponctuation de la couche symbolique
					$x += $this->width_trait+$this->font_size;
					$this->svg->addChild(new SvgText($x, $y, $GenOp["layerMark"],$this->styleTexte));
				}
				*/
				if($GenOp["layerMark"]){
					//ajoute la ponctuation de la couche
					$y = $this->y_exa+$this->y_entre_texte;
					$x = $this->x_exa-$this->x_entre_exa+($this->font_size*$GenOp["layerNumber"]*2);
					$this->svg->addChild(new SvgText($x, $y, $GenOp["layerMark"],$this->styleTexte,"","",$niv."_".$i."_".$GenOp["layerNumber"]));
				}
			}

			$i++;
		}
		
		if($niv==0){
			//redimensionne
			$Width = $this->x_exa+$this->width_trait+$this->x_entre_exa+$this->x_entre_exa;
			$Height = $this->y_exa+$this->y_entre_texte + (6*($this->y_entre_trait+$this->heigth_trait));
			
			if(!$this->showAll){
				//affiche taille réelle
				$this->svg->mWidth = $Width;
				$this->svg->mHeight = $Height;
			}else{
				//affiche tout
				$this->svg->mPreserveAspectRatio="xMinYMin meet";
				$this->svg->mViewBox = "0 0 ".$Width." ".$Height."";
				$this->svg->mWidth = 1000;
				if($this->r==-1)
					$this->svg->mHeight = $Height;
				else
					$this->svg->mHeight = $this->r*3;
			}
					
			//retourne le svg global
			if($this->doc)
				$this->svg->printElement();
			else
				return $this->svg;
		}
	}
	
	
	//pour calculer à partir d'un tableau associatif
	public function ShowSequence($arrExa)
	{
		//initialisation du svg
		$svg = new SvgDocument("100%","100%","","","","SVGglobal",$js); 	
		$i=0;
		foreach($arrExa as $exa){
			//v�rifie si on traite le dernier exa
			if(count($arrExa)==$i+1){
				//finalise la s�quence
				$layer = substr($exa["layer"],1);
				$layer = $this->StarParam["closing"]["L".($layer+2)];
				$svg->addChild(new SvgText($this->x_exa+$this->x_entre_exa, $this->y_exa+$this->y_entre_texte, $layer,$this->styleTexte));			
			}else{
				//construction de l'exagramme
				$svg->addChild($this->GetExa($exa["exa"],false));
				//construction de la l�gende
				if(substr($exa["tag"],0,5)!="empty"){
					$svg->addChild(new SvgText($this->x_exa, $this->y_exa+$this->y_entre_texte, $exa["tag"],$this->styleTexte));
				}
				//finalise la s�quence
				if($exa["role"]=="role3"){
					$layer = substr($exa["layer"],1);
					$layer = "L".($layer+1);
					$svg->addChild(new SvgText($this->x_exa+$this->width_trait, $this->y_exa+$this->y_entre_texte, $this->StarParam["closing"][$layer],$this->styleTexte));
				}
				//construction des fl�ches
				if($i<count($arrExa)-2){
					$svg->addChild($this->GetFleches($exa["exa"],$arrExa[$i+1]["exa"]));
				}
				$this->x_exa+=$this->x_entre_exa+$this->width_trait;
			}
			$i++;
		}
		//redimensionne
		/*
		$svg->mPreserveAspectRatio="xMinYMin meet";
		$svg->mViewBox = "0 0 ".($this->x_exa)." ".($this->y_exa+$this->y_entre_trait)."";
		*/
		$svg->mWidth = $this->x_exa+$this->width_trait+$this->x_entre_exa+$this->x_entre_exa;
		//retourne le svg global
		$svg->printElement();
	}
	
	
	public function GetFleches($ExaSrc,$ExaDst,$x=-1)
	{
		//prend en compte le passage d'une séquence à l'autre
		if($x==-1){
			$x=$this->x_exa;
		}
		$yTraitSrc=0;
		$svg = new SvgGroup("","","SVGExaFleche_");
		foreach($ExaSrc as $traitSrc){
			if($traitSrc){
				$xSrc = $x+$this->width_trait;
				$ySrc = $this->y_exa-$yTraitSrc;
				//recherche les destination
				$yTraitDst=0;
				foreach($ExaDst as $traitDst){
					if($traitDst){
						$xDst = $xSrc+$this->x_entre_exa;
						$yDst = $this->y_exa-$yTraitDst;
						$svg->addChild(new SvgLine($xSrc,$ySrc,$xDst,$yDst,"fill:black;stroke:red;stroke-width:3px;"));							
					}
					$yTraitDst += $this->heigth_trait+$this->y_entre_trait;
				}
			}
			$yTraitSrc += $this->heigth_trait+$this->y_entre_trait;
		}
		return $svg;
	}
	
	
	
  }

?>