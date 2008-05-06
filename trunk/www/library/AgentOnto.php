<?php
class AgentOnto {
  private $site;
  private $trace;
  public $marge=10;
  public $xentre_page=64;
  public $yentre_page=128;
  public $width_page=200;
  public $heigth_page=200;
  public $font_size=10;
  public $width_lien=2;
  public $xTrans;
  public $yTrans;
  public $bookmark;
  
  function __tostring() {
    return "Cette classe permet de définir et manipuler une page.<br/>";
    }

  function __construct($bookmark) {
  	$this->bookmark = $bookmark;
    $this->trace = TRACE;		
  }



  function svgBookmark() {

	$this->bookmark->GetInfos();
    if($this->trace)
    	echo "ExeAgentOnto:svgBoomark:bookmark".print_r($this->bookmark)."<br/>";
	//site sera remplacé par bookmark
	//calcul la taille
	//$nbPage = $this->bookmark->GetNbPost();//GetNbPage sera remplacé PAR GetNbPosts;
	//$xMaxSvg = $nbPage*($this->width_page+$this->xentre_page);
	//$yMaxSvg = 800;
	//$xBookmark = ($this->marge+$xMaxSvg)/2;
		
  	$svg = new SvgDocument("100%", "100%","","","","SVGglobal","onzoom=\"handleZoom(evt);\" onscroll=\"handlePan(evt);\" onload=\"handleLoad(evt);\""); 	
  	
  	//ajoute les liens avec les scripts
  	$svg->addChild(new SvgScript(jsPathWeb."svgAgentSite.js"));
  	$svg->addChild(new SvgScript(jsPathWeb."ajax.js"));
  	

	//construction de l'enveloppe essence
	$rEssence = count($this->bookmark->Tags)*40;
  	$essence = new SvgCircle(
  		400
  		,200
  		,$rEssence
  		,"stroke:yellow;stroke-width:".$this->width_lien.";fill:black;fill-opacity:0.3"
  		,""
  		,"onclick=\"alert('".$this->bookmark->id."');\""
  		,"SVGessence_".$this->bookmark->id
  		);	
  	$svg->addChild($essence);
  	
  	//ajoute un svg global
  	/*Déssiner l'exagone de bookmark = graine
  	$bookmark = new SvgPolygon("958,262.5 850,325 742,262.6 742,137.5 850,75 958,137.5 "
  		, "stroke:black;stroke-width:".$this->stroke_width_lien.";fill:red;"
  		, " translate(100,200) scale(0.2)"
  		, "onclick=\"alert('".$this->bookmark->id."');\""
  		, "SVGbookmark".$this->bookmark->id
  		);
	*/
  	$graine = new SvgCircle(
  		$essence->mCx
  		,$essence->mCy-$rEssence
  		,6*count($this->bookmark->Tags)
  		,"stroke:yellow;stroke-width:".$this->width_lien.";fill:black;"
  		,""
  		,"onclick=\"alert('".$this->bookmark->id."');\""
  		,"SVGgraine_".$this->bookmark->id
  		);	
  	$svg->addChild($graine);

  	//ajoute le login de l'utilisateur
  	$svg->addChild(new SvgText($graine->mCx+$graine->mR+$this->marge,$graine->mCy,$this->bookmark->titre,"fill:black;font-size:".$this->font_size."pt;"));

  	//construction du tronc
  	$tronc = $this->svgTronc($graine);
	$svg->addChild($tronc);
  				
	//ajoute la navigation zoom pan
  	$svg = $this->svgZoomPan($svg);

	//retourne le svg global
	$svg->printElement();
	
  }


  function svgTronc($graine) {
  	
	//récupération des posts
	$nbTag = count($this->bookmark->Tags);
	
  	$xDebTronc = $graine->mCx;
	$yDebTronc = $graine->mCy;
	$xFinTronc = $xDebTronc;
	$yFinTronc = $graine->mCy-(30*$nbTag);
  	$wTronc = $graine->mR;                                       
	
  	$svg = new SvgGroup("","","SVGtronc_".$this->bookmark->id);
	  	
	//ajoute les post en arc
  	$svg->addChild($this->svgOnArcElements($this->bookmark->id
  		,$xFinTronc
  		,$yFinTronc,20,180,180,false,true
  		,$this->bookmark->Posts));
  	
  	//création des liens à partir des Post vers les tags
  	$i=0;
  	foreach($this->bookmark->Posts as $post)
	{
		
		//calcul le placement gauche et droite
		if ($i%2 != 1)
			$xLienAncre = $xDebTronc-($i*$this->width_lien)-$this->width_lien;
		else
			$xLienAncre = $xDebTronc+($i*$this->width_lien)+$this->width_lien;
		
		//ajoute le lien entre le post et les tags
		$j =0;
		foreach($post->Tags as $tag){
			$xLienTag = $xLienAncre+($this->marge*$j);
	  		$svg->addChild(new SvgLine(
	  			$xLienTag 
	  			,$yDebTronc
	  			,$xLienTag 
	  			,$yFinTronc
	  			,"stroke:black;stroke-width:".$this->width_lien.";fill:black;"
	  			,""
	  			,"SVGLienTronc_".$this->bookmark->id."_post_".$post->id."_tag_".$j
	  			)
	  		);
	  		$j++;
		}
			  		
  		$i++;
	}
	
	//ajoute les tags en arc
  	$svg->addChild($this->svgOnArcElements($this->bookmark->id
  		,$xDebTronc
  		,$yDebTronc,20,-160,-220,false,true
  		,$this->bookmark->Tags));
	
  	//fin du tronc
  	$svg->addChild(new SvgCircle(
  		$xFinTronc
  		,$yFinTronc
  		,$wTronc
  		,"stroke:yellow;stroke-width:".$this->width_lien.";fill:black;"
  		,""
  		,""
  		,"SVGFinTronc".$this->bookmark->id
  		)
  	);
  	
  	return $svg;
  }
  
  
  function svgSemCouches($arrSem){
  	
  	$svg = new SvgDocument("100%", "100%","","","","SVGcouchesem","onzoom=\"handleZoom(evt);\" onscroll=\"handlePan(evt);\" onload=\"handleLoad(evt);\""); 	
  	
	foreach ($genOps->children() as $tag=>$val) {
		if(array_key_exists($tag,$arrTag)){
			$arrTag[$tag]=$arrTag[$tag]+1;
		}else{
			$arrTag[$tag]=1;
			$i ++;
		}
		//if($this->trace)
			echo "Sem.php:GetSvgPie:tag=".$tag."<br/>";
	}
  	
	//ajoute la navigation zoom pan
  	$svg = $this->svgZoomPan($svg);

	//retourne le svg global
	$svg->printElement();
  }
  
  function svgOnArcElements ($id, $center_x, $center_y, $radius, $display_angle, $start_angle, $coords_on, $spikes_on, $posts) {
	

  		$arrEl = $posts;
		$n_els = count($posts);
	  		
  		$radius        = $n_els * $radius * 2;
  		$start_angle   = $start_angle / 180 * M_PI; // start angle must be in radians, user gives degrees
  		$display_angle = $display_angle / 180 * M_PI; // display angle must be in radians, user gives degrees
		
		// draw the center as big dot
		$svg = new SvgGroup("","","SVGarc".$id);
  		if($this->trace)
			$svg->addChild(new SvgText(10,10,"$id, $center_x, $center_y, $n_els, $radius, $display_angle, $start_angle, $coords_on, $spikes_on","font-size:16pt;"));
		
		$j=0;
	  for ($i=0; $i<$n_els; $i++) {
	

	  		
		  	//calcul l'identifiant
		  	if($arrEl[$i]->id)
		  		$id = $arrEl[$i]->id;
		  	else
		  		$id = $i;
		  	if($arrEl[$i]->titre)
		  		$titre = $arrEl[$i]->titre;
		  	else
		  		$titre = $i;
		  	$idEl = "SVGarc".$id."_ele_".$id;
		  		  	
		  	//$svgEl = new SvgGroup("","","SVGarc".$id."_el_".$i);
		  	$pos_x = $center_x + round (cos($display_angle / $n_els * ($j % $n_els) + $start_angle) * $radius ); // ($i % $n_els) = roughly $i ;)
		    $pos_y = $center_y + round (sin($display_angle / $n_els * ($j % $n_els) + $start_angle) * $radius );	      
		
	  		if ($spikes_on) {
		  		$svg->addChild(new SvgLine(
		  			$center_x
		  			,$center_y
		  			,$pos_x
		  			,$pos_y
		  			,"stroke:black;stroke-width:".$this->width_lien.";fill:black;"
		  			,""
		  			,$idEl
		  			)
		  		);
		    }
	  		
		    if ($coords_on) {
		    	// this is the el number
	  			$svg->addChild(new SvgText($pos_x,$pos_y,$i,"font-size:8pt;"));
		    	// add some text to show coordinates
		      $line_h = 10;
		      $pos_x2 = $pos_x+4;
		      $pos_y2 = $pos_y+$line_h;
		      $pos_y3 = $pos_y+2*$line_h;
	  			$svg->addChild(new SvgText($pos_x2,$pos_y2,$titre,"font-size:6pt;"));
	  			$svg->addChild(new SvgText($pos_x2,$pos_y3,$titre,"font-size:6pt;"));
		      // yellow rectangle, origin is upper left corner of rectangle
			  	$svg->addChild(new SvgRect($pos_x, $pos_y,"24", "35"
			  		,"fill:#FFFF00;fill-opacity:0.2;stroke:#000099;"
			  		,""
			  		, "")
			  	);
		    }
			
		    // this is the precise coordinate
		  	$svg->addChild(new SvgCircle(
		  		$pos_x
		  		,$pos_y
		  		,$this->width_lien*2
		  		,"stroke:yellow;stroke-width:".$this->width_lien.";fill:black;"
		  		,""
		  		,"onclick=\"EleSelection('".$idEl."');\""
		  		)
		  	);
		  		
	  		$j++;
		  	
	    
	    
	  }
	  
	  return $svg;
	
	}

  
  function svgZoomPan($svg){
  	
  	$svg->addChild(new SvgScript(jsPathWeb."svgZoomPan.js"));  	
  	
	//ajoute la forme fléche
  	$def = new SvgDefs("","","FlechePan");
  	$def->addChild(new SvgPath("M0,10 h20 v-10 l20,30 l-20,30 v-10 h-20 z","fill:green","","","arrow"));
  	$svg->addChild($def);
  	//ajoute les blocs de navigation
  	$svgZP = new SvgGroup("","scale(1) translate(0,0)","zoomControls");
  	//ajoute un groupe pour resizer le zoomcontrols
  	$svgGR = new SvgGroup("","scale(0.5) translate(0,0)");
  	$svgGR->addChild(new SvgRect(20,20,160,160,"fill:#ffff00;stroke:none"));
  	$svgGR->addChild(new SvgCircle(100,100,20,"fill:red","","onclick=\"zoom('in');\""));
  	$svgGR->addChild(new SvgCircle(100,100,9,"fill:green","","onclick=\"zoom('out');\""));
  	$svgGR->addChild(new SvgUse(120,70,"","#arrow","","onclick=\"pan('left');\""));
  	$svgGR->addChild(new SvgUse(120,70,"rotate(90,100,100)","#arrow","","onclick=\"pan('up');\""));
  	$svgGR->addChild(new SvgUse(120,70,"rotate(180,100,100)","#arrow","","onclick=\"pan('right');\""));
  	$svgGR->addChild(new SvgUse(120,70,"rotate(270,100,100)","#arrow","","onclick=\"pan('down');\""));
  	$svgZP->addChild($svgGR);
  	$svg->addChild($svgZP);
  	
  	return $svg;
  	
  }
  
  }

?>