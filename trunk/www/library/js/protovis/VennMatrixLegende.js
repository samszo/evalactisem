//récupère les données pour la légende
var distribLeg = filtreDoublons(distrib.data.map(function(d) d.nbtag));

var leg = new pv.Panel()
    .width(320)
    .height(100)
;

leg.add(pv.Label)
    .top(20)
    .text("Poids des permutations (en nb. de tag)")
	.font("16px sans-serif")
;

//calcul une distribution lineaire 
//pour espacer les bars par rapport au nompbre de valeur
var x = pv.Scale.linear(0, distribLeg.length).range(0, 300);

leg.add(pv.Bar)
    .data(distribLeg)
    .height(42)
    .top(24)
    .width(16)
    .left(function() x(this.index)+10)
	.fillStyle(function(d) colorD(d))
  .anchor("bottom").add(pv.Label)
    .textAlign("left")
    .textBaseline("middle")
    .textAngle(-Math.PI / 2)
	.textStyle(function() (this.index < distribLeg.length/2) ? "white" : "black")
	.font("14px sans-serif")
;

leg.add(pv.Dot)
    .size(64)
    .top(80)
    .left(20)
	.strokeStyle ("red")
	.lineWidth(6) 
  .anchor("right").add(pv.Label)
    .textBaseline("middle")
	.textStyle("black")
    .left(40)
    .text("rendu difficile voir impossible")
	.font("14px sans-serif")
;

leg.render();