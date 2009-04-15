<?php
class Exagramme {
  private $site;
  private $trace;
  public $marge=100;
  public $x_exa = 100;
  public $y_exa = 200;
  public $x_entre_exa=128;
  public $y_entre_trait=10;
  public $y_entre_texte=100;
  public $width_trait=200;
  public $heigth_trait=20;
  public $font_size=10;
  public $width_lien=2;
  public $styleTexte="font-size:64px;font-style:normal;font-weight:normal;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;font-family:Bitstream Vera Sans";
  public $StarParam; 
  
  function __tostring() {
    return "Cette classe permet de d�finir et manipuler un Exagramme.<br/>";
    }

  function __construct($StarParam) {
    $this->trace = TRACE;
    date_default_timezone_set('UTC');		
    $this->StarParam = $StarParam;
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
		if($doc)
			$svg = new SvgDocument("600","600","","","","SVGglobal",$js);
		else
			$svg = new SvgGroup("","","SVGExa_");
		$yTrait=0;
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

	public function ShowSequence($arrExa,$redim=true)
	{
		//initialisation du svg
		$js="";
		if($redim)
			$svg = new SvgDocument("1600","300","","","","SVGglobal",$js); 	
		else
			$svg = new SvgDocument("100%","100%","","","","SVGglobal",$js); 	
		$i=0;
		$j=0;
		$layer = "";
		foreach($arrExa as $exa){
			//vérifie si on traite le dernier exa
			if(count($arrExa)==$i+1){
				//finalise la séquence
				if($arrExa[0]["layer"]!="L1"){
					$l = "L2";
					$svg->addChild(new SvgText($this->x_exa, $this->y_exa+$this->y_entre_texte, $this->StarParam["closing"][$l],$this->styleTexte));				
					$this->x_exa+=$this->x_entre_exa;
				}
				$layer = substr($arrExa[0]["layer"],1);
				$layer = $this->StarParam["closing"]["L".($layer+1)];
				$svg->addChild(new SvgText($this->x_exa, $this->y_exa+$this->y_entre_texte, $layer,$this->styleTexte));			
				//pour voir l'affichage complet
				$this->x_exa+=$this->x_entre_exa;
			}else{
				$j++;
				$layer = $exa["layer"]."";
				//construction de l'exagramme
				$svg->addChild($this->GetExa($exa["exa"],false));
				//construction de la légende
				if(substr($exa["tag"],0,5)!="empty"){
					$svg->addChild(new SvgText($this->x_exa, $this->y_exa+$this->y_entre_texte, $exa["tag"],$this->styleTexte));
				}
				//finalise la séquence de primitive
				if($j==3){
					$svg->addChild(new SvgText($this->x_exa+$this->width_trait, $this->y_exa+$this->y_entre_texte, $this->StarParam["closing"]["L1"],$this->styleTexte));
					$j=0;
				}
				//finalise la couche si le layer suivant est différent
				if($layer!=$arrExa[$i+1]["layer"].""){
					//vérifie s'il faut aussi finaliser la séquence primitive
					if($j!=3){
						$svg->addChild(new SvgText($this->x_exa+$this->width_trait, $this->y_exa+$this->y_entre_texte, $this->StarParam["closing"]["L1"],$this->styleTexte));
					}
					$l = "L2";
					$svg->addChild(new SvgText($this->x_exa+$this->width_trait+($this->x_entre_exa/2), $this->y_exa+$this->y_entre_texte, $this->StarParam["closing"][$l],$this->styleTexte));
					$j=0;
				}
				//construction des flêches
				if($i<count($arrExa)-2){
					$svg->addChild($this->GetFleches($exa["exa"],$arrExa[$i+1]["exa"]));
				}
				$this->x_exa+=$this->x_entre_exa+$this->width_trait;
			}
			$i++;
		}
		if($redim){
			//redimensionne
			$svg->mPreserveAspectRatio="xMinYMin meet";
			$svg->mViewBox = "0 0 ".($this->x_exa)." ".($this->y_exa+$this->y_entre_trait)."";
		}else{
			$svg->mWidth = $this->x_exa+$this->width_trait+$this->x_entre_exa+$this->x_entre_exa;
		}
		//retourne le svg global
		$svg->printElement();
	}

	public function GetFleches($ExaSrc,$ExaDst)
	{
		$yTraitSrc=0;
		$svg = new SvgGroup("","","SVGExaFleche_");
		foreach($ExaSrc as $traitSrc){
			if($traitSrc){
				$xSrc = $this->x_exa+$this->width_trait;
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