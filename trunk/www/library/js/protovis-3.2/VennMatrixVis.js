
//construction du cadre de zoom et de pad
var w = 800,
    h = 600,
    t = pv.Transform.identity, // the inverted transform
    x = pv.Scale.linear(0, w).range(0, w),
    y = pv.Scale.linear(0, h).range(0, h);
 
visZP = new pv.Panel()
    .width(w)
    .height(h)
    .top(10)
    .left(40)
    .right(20)
    .bottom(20)
    .strokeStyle("#ccc")
	.visible(false);
 
visZP.add(pv.Rule)
    .data(function() x.domain(t.x, w * t.k + t.x).ticks())
    .strokeStyle("#ccc")
    .left(x)
  .anchor("bottom").add(pv.Label);
 
visZP.add(pv.Rule)
    .data(function() y.domain(t.y, h * t.k + t.y).ticks())
    .strokeStyle("#ccc")
    .top(y)
  .anchor("left").add(pv.Label);


//construction de la visualisation ARC
visA = visZP.add(pv.Panel)
    .overflow("hidden")
    .fillStyle("rgba(0,0,0,.001)") // pointer-events = "all"
    .event("mousedown", pv.Behavior.pan())
    .event("mousewheel", pv.Behavior.zoom(1));//préciser la vitesse de la roulette


var arc = visA.add(pv.Layout.Arc)
    .nodes(function(){return dataA.nodes;})   
    .links(function(){return dataA.links;})
    .sort(function(a, b) a.group == b.group
        ? b.linkDegree - a.linkDegree
        : b.group - a.group);

arc.link.add(pv.Line)
    .strokeStyle(function(d, p){ var c=colorL(p.linkValue); c.opacity=0.4; return c;}) 
;

arc.link.add(pv.Line) 
    .strokeStyle(function(d, p)colorL(p.linkValue)) 
    .visible(function(d, p) p.sourceNode.active || p.targetNode.active)
; 

arc.node.add(pv.Dot)
	//en cas de filtre la valeur linkDegree est recalculé
	//elle ne correspond plus à la légende
	//il faut prendre LinkDegree la valeur originale venant du json
    //.size(function(d) d.linkDegree * 6)
    //.fillStyle(function(d) colorF(d.linkDegree))
    .size(function(d) d.LinkDegree * 6)
    .fillStyle(function(d) colorF(d.LinkDegree))
    .strokeStyle(function() this.fillStyle().darker())
      .shape("circle") 
    .event("mouseover", function(d) (d.active = true, arc)) 
    .event("mouseout", function(d) (d.active = false, arc)) 
;


arc.label.add(pv.Label)
	.font(function(d) Math.sqrt(d.group) * 20 + "px sans-serif")
	.textStyle(function(d) colorN(d.group))
;


/* a way get the transform as it is set. */
visA.transform = function() {
  var f = visA.transform;
  return function(v) {
    if (v) t = v.invert();
    return f.apply(this, arguments);
  };
}();
 
/* way to control which panel gets rendered with pan & zoom! */
visA.render = function() {
  visZP.render();
};


//construction de la visualisation MATRIX
//le zoom et padding ne marche pas
visM = new pv.Panel()
    .top(200)
    .left(200)
	.def("lnk", [-1,-1])
;

var matrix = visM.add(pv.Layout.Matrix)
    .directed(true) 
    .nodes(function(){return dataM.nodes;})   
    .links(function(){return dataM.links;})
    .sort(function(a, b) b.group - a.group)
	;

matrix.link.add(pv.Bar)
	.antialias(false)
	.strokeStyle ("rgba(0,0,0,.1)")
    .fillStyle(function(l) l.linkValue
        ? colorL(l.linkValue) : "#FFF")
	.title(function(l) l.linkValue
        ? l.targetNode.nodeName+" + "+l.sourceNode.nodeName+" "+l.linkValue+" fois par "+l.targetNode.group+" utilisateur(s)" : "pas de co-occurrence")
    .event("mouseover",function(l)visM.lnk([l.sourceNode.nodeName,l.targetNode.nodeName])) 
    .event("mouseout", function()visM.lnk([-1,-1])) 
;

matrix.label.add(pv.Label)
	.font("16px sans-serif")
    .textStyle(function(n)
		(this.textAngle()==0) ?
			visM.lnk()[0] == n.nodeName ? colorN(n.group) : colorF(n.LinkDegree)
		: visM.lnk()[1] == n.nodeName ? colorN(n.group) : colorF(n.LinkDegree)
	)
;

visM.render();

visZP.render();