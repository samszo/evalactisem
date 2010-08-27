<?php
require('../param/ParamPage.php');

	if($complet){
		$oTG = new TagCloud($objSite,$oDelicious,"",$login);
   		$arrTL = $oTG->GetTagLinks($objUti,$tag);
   		//$js = '<script type="text/javascript" src="'.PathWeb.'tmp/json/TagLinks_'.$objUti->login.'_'.$tag.'.js"></script>';		
	}else{
		//récupère les tags liés à un tag pour un utilisateur
		$TagLinks = json_decode($objSite->GetCurl("http://feeds.delicious.com/v2/json/tags/".$user."/".$tag));
	
		//construction des données Json
		$arrTL = array("nodes"=>array(), "links"=>array());
	
		//ajout du tag initial 
		$arrTL["nodes"][] = array("nodeName"=>$tag, "group"=>ord(substr($tag,0,1)));	
	
		//ajout des tag liés
		$i=1;
		foreach($TagLinks as $k=>$v){
			//prise en compte de la plage des occurrence
			if($v > $NbDeb && $v < $NbFin){				
				//ajout dans le tableau des noeuds 
				$arrTL["nodes"][] = array("nodeName"=>$k, "group"=>ord(substr($k,0,1)));	
				//création des liens
				$arrTL["links"][] = array("source"=>0, "target"=>$i, "value"=>$v);
				$i ++;
			}
		}	
	}		
	$js = '<script type="text/javascript"> var data = '.json_encode($arrTL).';</script>';		
	
	?>
<html>
  <head>
    <title>Tag Links</title>
    <script type="text/javascript" src="<?php echo PathWeb;?>library/js/protovis/protovis-r3.2.js"></script>
	<?php echo $js;?>
    <style type="text/css">

body {
  margin: 0;
  display: table;
  height: 100%;
  width: 100%;
  font: 14px/134% Helvetica Neue, sans-serif;
}

#center {
  display: table-cell;
  vertical-align: middle;
}

#fig {
  position: relative;
  margin: auto;
}

    </style>
  </head>
  <body><div id="center">
 
  <div id="fig">
    <script type="text/javascript+protovis">

var w = 800,
    h = 600,
    t = pv.Transform.identity, // the inverted transform
    x = pv.Scale.linear(0, w).range(0, w),
    y = pv.Scale.linear(0, h).range(0, h);
 
var vis = new pv.Panel()
    .width(w)
    .height(h)
    .top(30)
    .left(40)
    .right(20)
    .bottom(20)
    .strokeStyle("#ccc");
 
vis.add(pv.Rule)
    .data(function() x.domain(t.x, w * t.k + t.x).ticks())
    .strokeStyle("#ccc")
    .left(x)
  .anchor("bottom").add(pv.Label);
 
vis.add(pv.Rule)
    .data(function() y.domain(t.y, h * t.k + t.y).ticks())
    .strokeStyle("#ccc")
    .top(y)
  .anchor("left").add(pv.Label);

var view = vis.add(pv.Panel)
    .overflow("hidden")
    .fillStyle("rgba(0,0,0,.001)") // pointer-events = "all"
    .event("mousedown", pv.Behavior.pan())
    .event("mousewheel", pv.Behavior.zoom(2));//préciser la vitesse de la roulette


var arc = view.add(pv.Layout.Arc)
    .nodes(data.nodes)
    .links(data.links)
    .sort(function(a, b) a.group == b.group
        ? b.linkDegree - a.linkDegree
        : b.group - a.group);
arc.link.add(pv.Line);

arc.node.add(pv.Dot)
    .size(function(d) d.linkDegree + 4)
    .fillStyle(pv.Colors.category19().by(function(d) d.group))
    .strokeStyle(function() this.fillStyle().darker());

arc.label.add(pv.Label);

 
/* Hack! Need a way get the transform as it is set. */
view.transform = function() {
  var f = view.transform;
  return function(v) {
    if (v) t = v.invert();
    return f.apply(this, arguments);
  };
}();
 
/* Hack! Need a way to control which panel gets rendered with pan & zoom! */
view.render = function() {
  vis.render();
};
 
vis.render();

    </script>
  </div></div></body>
</html>
