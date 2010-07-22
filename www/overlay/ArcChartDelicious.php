<?php
require('../param/ParamPage.php');

	if($complet){
		$oTG = new TagCloud($objSite,$oDelicious,"",$login);
   		$oTG->GetTagLinks($objUti,$tag);
   		$js = '<script type="text/javascript" src="'.PathWeb.'tmp/json/TagLinks_'.$objUti->login.'_'.$tag.'.js"></script>';		
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
		$js = '<script type="text/javascript"> var data = '.json_encode($arrTL).';</script>';		
		
	}		
?>
<html>
  <head>
    <title>Tag Links</title>
    <script type="text/javascript" src="<?php echo PathWeb;?>library/js/protovis-3.2/protovis-r3.2.js"></script>
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
  width: <?php echo $width; ?>;
  height: <?php echo $height; ?>;
}

    </style>
  </head>
  <body><div id="center"><div id="fig">
    <script type="text/javascript+protovis">

var vis = new pv.Panel()
    .width(<?php echo $width; ?>)
    .height(<?php echo $height; ?>)
	.bottom(100);

var arc = vis.add(pv.Layout.Arc)
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

arc.label.add(pv.Label)

vis.render();

    </script>
  </div></div></body>
</html>
