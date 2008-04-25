<?php
class AgentSite {
  private $site;
  private $DB;
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
  
  function __tostring() {
    return "Cette classe permet de d�finir et manipuler une page.<br/>";
    }

  function __construct($site) {
  	$this->site = $site;
    $this->trace = TRACE;		
  }



  function svgSite($id) {

	$this->site->GetInfos($id);
  	
	//calcul la taille
	$nbPage = $this->site->GetNbPage();
	$xMaxSvg = $nbPage*($this->width_page+$this->xentre_page);
	$yMaxSvg = 800;
	$xSite = ($this->marge+$xMaxSvg)/2;
		
  	//$svg = new SvgDocument("", "","","0 0 ".$xMaxSvg." ".$yMaxSvg,"xMidYMin meet","SVGglobal");
  	$svg = new SvgDocument("100%", "100%","","","","SVGglobal","onzoom=\"handleZoom(evt);\" onscroll=\"handlePan(evt);\" onload=\"handleLoad(evt);\""); 	
  	
  	//ajoute les liens avec les scripts
  	$svg->addChild(new SvgScript(jsPathRoot."svgAgentSite.js"));
  	$svg->addChild(new SvgScript(xulPathRoot."js/ajax.js"));
  	

  	//ajoute un svg global
  	//$svg = new SvgFragment("100%", "100%","","","","","","GroupeGlobal","onzoom=\"handleZoom(evt);\" onscroll=\"handlePan(evt);\" onload=\"handleLoad(evt);\""); 	
  	
	$site = new SvgRect($xSite, $this->marge, $this->width_page+($nbPage*8), $this->heigth_page+($nbPage*4)
  		,"stroke:black;stroke-width:".$this->stroke_width_lien.";fill:red;"
  		,""
  		, "onclick=\"VoirSelectPage('".$this->site->idSite."');\"");
  	$svg->addChild($site);
  	//ajoute le nom du site 
  	$svg->addChild(new SvgText($site->mX+10,$site->mY+20,$this->site->titre,"fill:black;font-size:".$this->font_size."pt;"));
	//construction du tronc
	$xDebTronc = $site->mX+($site->mWidth/2);
	$yDebTronc = $site->mY+($site->mHeight);
  	
  	//r�cup�re les pages du site
  	$i=0;
  	$j=0;
  	while($this->site->pages[$i])
	{
		$page = $this->site->pages[$i];
		if($page->idParent==0){		
			//ajoute la page et calcul la translation
	  		$svg->addChild($this->svgPage($page,true,1,$j+1));
	  		//ajoute le lien entre le site et la page
	  		$svg->addChild(new SvgLine(
	  			$xDebTronc
	  			,$yDebTronc
	  			,$this->xTrans+($this->width_page/2)
	  			,$this->yTrans
	  			,"stroke:black;stroke-width:".$this->stroke_width_lien.";fill:black;"
	  			,""
	  			,"SVGLien_site_".$id."_page_".$page->id
	  			)
	  		);
	  		$j++;
		}
  		$i++;
	}
  	$tronc = $this->svgTronc("_site_".$id,$site);
	$svg->addChild($tronc);
  	
  	//ajoute la navigation zoom pan
  	$svg = $this->svgZoomPan($svg);

	//pour le scroll
	//$svgG->addChild($svg);
	//$svgG->addChild(new SvgGroup("","","scrollbar1"));
	$svg->printElement();
	
/*
	<!-- cercle  fin tronc
		cx_fin_tronc_int = cx_agent
		cy_fin_tronc_int = y_tronc
		r_fin_tronc_int = (nbLien+1) x stroke-width_lien
	-->
	<circle id="fin_tronc_int" cx="200" cy="250" r="6" stroke="yellow" stroke-width="2" fill="black"/>
  	
  	
	<!-- ligne branche ancre 1 gauche de la périphérie au centre 
		strocke_lien1g = statut
		x1_lien1g= x2_lien1d = cx_agent - (stroke-width_lien + 1)
		y1_lien1g= agent_cy + agent_r 
		y2_lien1g= agent_cy - tronc_height
	-->
	<line id="lien1g" x1="197" y1="300" x2="197" y2="250" stroke="yellow" stroke-width="2"/>
	<!-- A FAIRE ligne branche ancre 1 droite de la périphérie au centre 
		strocke_lien1d = statut
		x1_lien1d= x2_lien1d = cx_agent + (stroke-width_lien + 1)
		y1_lien1d= agent_cy + agent_r 
		y2_lien1d= agent_cy - tronc_height
	-->
	<line id="lien1gho" x1="197" y1="300" x2="0" y2="300" stroke="green" stroke-width="6"/>
	<!-- ligne branche ancre 1 droite de la périphérie au centre 
		strocke_lien1d = statut
		x1_lien1d= x2_lien1d = cx_agent + (stroke-width_lien + 1)
		y1_lien1d= agent_cy + agent_r 
		y2_lien1d= agent_cy - tronc_height
	-->
	<line id="lien1d" x1="203" y1="300" x2="203" y2="250" stroke="yellow" stroke-width="2"/>
	<!-- A FAIRE ligne branche ancre 1 droite de la périphérie au centre 
		strocke_lien1d = statut
		x1_lien1d= x2_lien1d = cx_agent + (stroke-width_lien + 1)
		y1_lien1d= agent_cy + agent_r 
		y2_lien1d= agent_cy - tronc_height
	-->
	<line id="lien1dho" x1="203" y1="300" x2="400" y2="300" stroke="green" stroke-width="6"/>
	";
	*/
      }

  function svgPage($page, $doc=false, $niv=1, $ordre=1) {

  	if(!is_object($page))
  		$page = new Page($this->site,$page);
  	//calcul la place du svg
  	$this->xTrans = (($this->width_page+$this->xentre_page)*$ordre)+$this->marge-$this->heigth_page;
  	$this->yTrans = ($this->heigth_page*$niv)+$this->yentre_page;
  	$trans = "translate(".$this->xTrans.",".$this->yTrans." )";
  	if($doc){
  		$svg = new SvgGroup("",$trans,"SVGpage_".$page->id);
   	}else{
  		$svg = new SvgDocument("", "","","","","SVGpage_".$page->id);
   	}
  	$svgP = new SvgRect(0, 0, $this->width_page,$this->heigth_page 
  		,"stroke:black;stroke-width:".$this->width_lien.";fill:red;"
  		,""
  		, "onclick=\"VoirSelectPage('".$page->id."');\""
  		,"SVGpagerect_".$page->id
  		);
  	$svg->addChild($svgP);
  	//ajoute le nom du site 
  	$svg->addChild(new SvgText($svgP->mX+10,$svgP->mY+20,$page->id." - ".$page->titre,"fill:black;font-size:".$this->font_size."pt;"));
	//construction du tronc		
	$svg->addChild($this->svgTronc("_page_".$page->id,$svgP,$page));
  	
	//entr�e de la page
  	$svg->addChild(new SvgCircle(
  		$svgP->mX+($svgP->mWidth/2)
  		,$svgP->mY
  		,$this->width_lien*2
  		,"stroke:yellow;stroke-width:".$this->width_lien.";fill:black;"
  		,""
  		,""
  		,"SVGpageentree_".$page->id
  		)
  	);
	//cr�ation des pages enfants
	$xDebTronc = $svgP->mX+($svgP->mWidth/2);
	$yDebTronc = $svgP->mY+($svgP->mHeight);
  	$i=0;
  	while($page->PageEnfs[$i])
	{
		$svg->addChild($this->svgPage($page->PageEnfs[$i],true,$niv*0.8,$i));

  		//ajoute le lien entre le site et la page
  		$svg->addChild(new SvgLine(
  			$xDebTronc
  			,$yDebTronc
  			,$this->xTrans+($this->width_page/2)
  			,$this->yTrans
  			,"stroke:black;stroke-width:".$this->stroke_width_lien.";fill:black;"
  			,""
  			,"SVGLien_site_".$id."_page_".$page->id
  			)
  		);
		
		$i++;
	}
  	
	//recalcul la place du svg pour le parent
  	$this->xTrans = (($this->width_page+$this->xentre_page)*$ordre)+$this->marge-$this->heigth_page;
  	$this->yTrans = ($this->heigth_page*$niv)+$this->yentre_page;
	
  	if($doc)
  		return $svg;
  	else
  		$svg->printElement();
  }

  function svgTronc($id, $svgP,$page=-1) {
  	
  	//v�rifie si on dessine le tronc d'une page ou d'un site
  	if($page=="-1"){
  		$arrLiens = $this->site->pages;
		$nbPage = $this->site->GetNbPage();
   	}else{
  		$arrLiens = $page->PageEnfs;
		$nbPage = count($arrLiens);
   	}
  	
  	$xDebTronc = $svgP->mX+($svgP->mWidth/2);
	$yDebTronc = $svgP->mY+($svgP->mHeight);
	$xFinTronc = $xDebTronc;
	$yFinTronc = $svgP->mY+($svgP->mHeight/1.5);
  	$wTronc = $this->width_lien*$nbPage;
	
  	$svg = new SvgGroup("","","SVGtronc".$id);
	  	
  	//ajoute les liens d'ancre
  	$i=0;
  	while($arrLiens[$i])
	{
		
		//calcul le placement gauche et droite
		if ($i%2 == 1)
			$xLienAncre = $xDebTronc-($i*$this->width_lien)-$this->width_lien;
		else
			$xLienAncre = $xDebTronc+($i*$this->width_lien)+$this->width_lien;

	  	//v�rifie si on dessine le tronc d'une page ou d'un site
	  	$verif = true;
	  	if($page=="-1"){
	  		if($this->site->pages[$i]->idParent!=0)
		  		$verif = false;
	  	}
	  	
	  	if($verif)
		  	//ajoute le lien entre les ancres et les pages
	  		$svg->addChild(new SvgLine(
	  			$xLienAncre
	  			,$yDebTronc
	  			,$xLienAncre
	  			,$yFinTronc
	  			,"stroke:black;stroke-width:".$this->width_lien.";fill:black;"
	  			,""
	  			,"SVGLienTronc".$id."_page_".$arrLiens[$i]->id
	  			)
	  		);
  		
  		$i++;
	}
	
	if($i>0){
		//ajoute les �l�ments en arc
	  	$svg->addChild($this->svgOnArcElements($id,$xFinTronc,$yFinTronc,6,180,180,false,true,$page));
	
	  	//d�but du tronc
	  	$svg->addChild(new SvgCircle(
	  		$xDebTronc
	  		,$yDebTronc
	  		,$wTronc
	  		,"stroke:yellow;stroke-width:".$this->width_lien.";fill:black;"
	  		,""
	  		,""
  			,"SVGDebTronc".$id
	  		)
	  	);
	  	//fin du tronc
	  	$svg->addChild(new SvgCircle(
	  		$xFinTronc
	  		,$yFinTronc
	  		,$wTronc
	  		,"stroke:yellow;stroke-width:".$this->width_lien.";fill:black;"
	  		,""
	  		,""
  			,"SVGFinTronc".$id
	  		)
	  	);
	}
  	
  	return $svg;
  	
  }
  
  
  function svgOnArcElements ($id, $center_x, $center_y, $radius, $display_angle, $start_angle, $coords_on, $spikes_on, $page=-1) {
	

	  	//v�rifie si on dessine l'arc d'une page ou d'un site
	  	if($page=="-1"){
	  		$arrEl = $this->site->pages;
			$n_els = $this->site->GetNbPage();
	  	}else{
	  		$arrEl = $page->PageEnfs;
			$n_els = count($arrEl);
	  	}
	  		
  		$radius        = $n_els * $radius * 2;
  		$start_angle   = $start_angle / 180 * M_PI; // start angle must be in radians, user gives degrees
  		$display_angle = $display_angle / 180 * M_PI; // display angle must be in radians, user gives degrees
		
		// draw the center as big dot
		$svg = new SvgGroup("","","SVGarc".$id);
  		if($this->trace)
			$svg->addChild(new SvgText(10,10,"$id, $center_x, $center_y, $n_els, $radius, $display_angle, $start_angle, $coords_on, $spikes_on","font-size:16pt;"));
		
		$j=0;
	  for ($i=0; $i<$n_els; $i++) {
	

	  	//v�rifie si on dessine le tronc d'une page ou d'un site
	  	$verif = true;
	  	if($page=="-1"){
	  		if($this->site->pages[$i]->idParent!=0)
		  		$verif = false;
	  	}
	  	
	  	if($verif){
	  		
		  	//calcul l'identifiant
	  		$idEl = "SVGarc".$id."_page_".$arrEl[$i]->id;
		  		  	
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
	  			$svg->addChild(new SvgText($pos_x2,$pos_y2,"Nom ancre","font-size:6pt;"));
	  			$svg->addChild(new SvgText($pos_x2,$pos_y3,"Nom ancre","font-size:6pt;"));
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
		  		,"onclick=\"AncreSelection('".$idEl."');\""
		  		)
		  	);
		  		
	  		$j++;
		  	
	  	}
	    
	    
	  }
	  
	  return $svg;
	
	}

  
  function svgZoomPan($svg){
  	
  	$svg->addChild(new SvgScript(jsPathRoot."svgZoomPan.js"));  	
	//ajoute la forme fl�che
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