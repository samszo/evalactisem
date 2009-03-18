<?php
class Exagramme {
  private $site;
  private $trace;
  public $marge=100;
  public $x_exa = 100;
  public $y_exa = 200;
  public $x_entre_exa=128;
  public $y_entre_trait=10;
  public $width_trait=200;
  public $heigth_trait=20;
  public $font_size=10;
  public $width_lien=2;
    
  function __tostring() {
    return "Cette classe permet de définir et manipuler un Exagramme.<br/>";
    }

  function __construct() {
    $this->trace = TRACE;
    date_default_timezone_set('UTC');		
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

	public function GetSequence($arrExa)
	{
		//initialisation du svg
		$svg = new SvgDocument("600","600","","","","SVGglobal",$js); 	
		$i=0;
		foreach($arrExa as $exa){
			$svg->addChild($this->GetExa($exa,false));
			//construction des flêches
			if($i<count($arrExa)-1){
				$svg->addChild($this->GetFleches($exa,$arrExa[$i+1]));
			}
			$this->x_exa+=$this->x_entre_exa+$this->width_trait;
			$i++;
		}
		//redimensionne
		$svg->mPreserveAspectRatio="xMinYMin meet";
		$svg->mViewBox = "0 0 ".($this->x_exa)." ".($this->y_exa)."";

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