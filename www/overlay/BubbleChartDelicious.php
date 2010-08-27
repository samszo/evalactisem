<?php
require('../param/ParamPage.php');

if(isset($_GET['json'])){
	$params = json_decode(urldecode($_GET['json']));
	//print_r($params);
	$user = $params->user; 
	$NbDeb = $params->NbDeb; 
	$NbFin = $params->NbFin; 
}
//echo $user." - ".$NbDeb." - ".$NbFin;

//récupère les tags de l'utilisateur
$jsTags = json_decode($objSite->GetCurl("http://feeds.delicious.com/v2/json/tags/".$user));
//filtre les valeurs en dehors de la plage
$jsTagsFiltre = array();
foreach($jsTags as $k=>$val){
	if($val > $NbDeb && $val < $NbFin){
		$jsTagsFiltre[$k]=$val;
	}
}

?>
<html>
  <head>
    <title>Bubble Chart</title>
    <script type="text/javascript" src="<?php echo PathWeb;?>/library/js/protovis/protovis-r3.2.js"></script>
    <style type="text/css">

body {
  margin: 0;
}

    </style>
  </head>
  <body>
    <script type="text/javascript">
    var datas = <?php echo json_encode($jsTagsFiltre); ?>;
    var TagNbMin = <?php echo $NbDeb; ?>;
    var TagNbMax = <?php echo $NbFin; ?>;
    var TagDateDeb = "2009-11-05";
    var TagDateDeb = "2010-07-07";
    </script>

    <script type="text/javascript+protovis">

/* Produce a flat hierarchy of the json. */
var classes = pv.nodes(pv.flatten(datas).leaf(Number).array());
classes.slice(1).forEach(function(d) {
	d.nodeName = "<?php echo $user; ?>." +  d.nodeValue.keys.join(".");
	var i = d.nodeName.lastIndexOf(".");
	d.className = d.nodeName.substring(i + 1);
	d.packageName = d.className.substring(0, 1);
	d.nodeValue = d.nodeValue.value;
});

/* Sizing parameters and scales. */
var w = 800,
    h = 500,
    kx = w / h,
    ky = 1,
    x = pv.Scale.linear(-kx, kx).range(0, w),
    y = pv.Scale.linear(-ky, ky).range(0, h),
    t = pv.Transform.identity;
 
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


/* For pretty number formatting. */
var format = pv.Format.number();

var view = vis.add(pv.Panel)
    .overflow("hidden")
    .fillStyle("rgba(0,0,0,.001)") // pointer-events = "all"
    .event("mousedown", pv.Behavior.pan())
    .event("mousewheel", pv.Behavior.zoom(2));//préciser la vitesse de la roulette

view.add(pv.Layout.Pack)
    .nodes(classes)
    .size(function(d) d.nodeValue)
    .spacing(0)
    .order(null)
	.node.add(pv.Dot)
    .fillStyle(pv.Colors.category20().by(function(d) d.packageName))
    .strokeStyle(function() this.fillStyle().darker())
    .visible(function(d) d.parentNode)
    .title(function(d) d.nodeName + ": " + format(d.nodeValue))
  	.anchor("center").add(pv.Label)
    .text("");

//view.render();

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
  </body>
</html>
