//cosntruction de la légende du nb de co-occurrence
var dataLegL = GetDataLegL();

var legL = new pv.Panel()
    .width(function(){var w=dataLegL.length*16 + 20; if(w<180)w=180; return w;})
    .height(80)
;

legL.add(pv.Label)
    .top(20)
    .text("Nb des co-occurences")
	.visible(function(){return dataLegL.length>0;})
	.font("16px sans-serif")
;

legL.add(pv.Bar)
    .data(function(){return dataLegL;})
    .height(42)
    .top(24)
    .width(16)
    .left(function() this.index*16)
	.fillStyle(function(l) colorL(l))
  .anchor("bottom").add(pv.Label)
    .textAlign("left")
    .textBaseline("middle")
    .textAngle(-Math.PI / 2)
	.font("14px sans-serif")
	.textStyle(function() (this.index < dataLegL.length/2) ? "white" : "black")
  .anchor("bottom").add(pv.Dot)
     .size(32)
     .top(72)
   	 .fillStyle("green")
 	 .cursor("pointer")
     .event("click",function(d) filtreVis(event,"nbOcc",d)) 
;

legL.render();

//cosntruction de la légende pour la force du lien
var dataLegF = GetDataLegF();

var legF = new pv.Panel()
    .width(function(){var w=dataLegF.length*16 + 20; if(w<140)w=140; return w;})
    .height(80)
;

legF.add(pv.Label)
    .top(20)
    .text("Force de liaison")
	.visible(function(){return dataLegF.length>0;})
	.font("16px sans-serif")
;

legF.add(pv.Bar)
    .data(function(){return dataLegF;})
    .height(42)
    .top(24)
    .width(16)
    .left(function() this.index*16)
	.fillStyle(function(l) colorF(l))
  .anchor("bottom").add(pv.Label)
    .textAlign("left")
    .textBaseline("middle")
    .textAngle(-Math.PI / 2)
	.font("14px sans-serif")
	.textStyle(function() (this.index < dataLegF.length/2) ? "white" : "black")
  .anchor("bottom").add(pv.Dot)
     .size(32)
     .top(72)
   	 .fillStyle("green")
 	 .cursor("pointer")
     .event("click",function(d) filtreVis(event,"nbLien",d)) 
;
legF.render();


//cosntruction de la légende pour le nb d'utilisateur
var dataLegN = GetDataLegN();

var legN = new pv.Panel()
    .width(function(){var w=dataLegN.length*16 + 20; if(w<120)w=120; return w;})
    .height(80)
;

legN.add(pv.Label)
    .top(20)
    .text("Nb d'utilisateurs")
	.visible(function(){return dataLegN.length>0;})
	.font("16px sans-serif")
;

legN.add(pv.Bar)
    .data(function(){return dataLegN;})
    .height(42)
    .top(24)
    .width(16)
    .left(function() this.index*16)
	.fillStyle(function(l) colorN(l))
  .anchor("bottom").add(pv.Label)
    .textAlign("left")
    .textBaseline("middle")
    .textAngle(-Math.PI / 2)
	.font("14px sans-serif")
	.textStyle(function() (this.index < dataLegN.length/2) ? "white" : "black")
  .anchor("bottom").add(pv.Dot)
     .size(32)
     .top(72)
   	 .fillStyle("green")
 	 .cursor("pointer")
     .event("click",function(d) filtreVis(event,"nbUti",d)) 
;
legN.render();

var legFiltre = new pv.Panel()
    .width(160)
    .height(80)
	.visible(false)
;

legFiltre.add(pv.Label)
    .top(20)
    .text("Filtrage des données")
	.font("16px sans-serif")
;

legFiltre.add(pv.Dot)
    .size(32)
    .top(30)
    .left(10)
   	.fillStyle("green")
  .anchor("right").add(pv.Label)
	.textStyle("black")
    .left(20)
    .text("données visibles")
	.font("12px sans-serif")
;

legFiltre.add(pv.Dot)
    .size(32)
    .top(50)
    .left(10)
   	.fillStyle("red")
  .anchor("right").add(pv.Label)
	.textStyle("black")
    .left(20)
    .text("données filtrées")
	.font("12px sans-serif")
;

legFiltre.render();