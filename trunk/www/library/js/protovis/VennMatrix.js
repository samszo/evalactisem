var data = {"nodes":[],"links":[]};
var dataM = {"nodes":[],"links":[]};
var dataA = {"nodes":[],"links":[]};
var json;
var url = ajaxPathWeb+"?f=GetUsersTagLinks&tag="+aggTag+"&users="+arrUsers.join(",");

//récupération des valeurs de distribution
url = ajaxPathWeb+"?f=GetUsersTagsDistrib&tag="+aggTag+"&users="+arrUsers.join(",");
var distrib = eval('(' + GetResult(url) + ')');
//pour stocker les distribution qui posent des problème de rendu
var distribCheck = [];
var distribSelect;
var arrValCheck = {"matrix":[200],"arc":[600]};
var distribTooBig = false;

//mise à jour du lien vers les data
document.getElementById('distribData').innerHTML= '<a href="'+url+'" target="_blank" >data</a>'; 


/* définition des couleurs cf. http://www.w3schools.com/tags/ref_colormixer.asp*/
var cNmax = "#FF3300", cNmin = "#FFFF00";
	cLmax = "#9900CC", cLmin = "#FFFF00", 
	cFmax = "#006666", cFmin = "#CCFF33", 
	cDmax = "#40006f", cDmin = "#c9ffff";
var colorN;
var colorL;
var colorF;
var colorD = pv.Scale.log(distrib.data, function(d) d.nbtag).range(cDmin, cDmax);

var visA, visM, visZP;

//pour stocker les filtres
var filtreLeg = {"nbOcc":[],"nbLien":[],"nbUti":[]};

	function initSvg(){
		//affichage du svg
	    AppendSVG(PathWeb+"overlay/Venn5dyna.svg",document.getElementById('Venn'), false);
//console.log("initSvg",distrib.data);

		//mise à jour du svg
		var idSvg, nSvg, nbTag, lib, c;
		for(var i= 0; i < distrib.data.length; i++)
		{
			//construction de l'identifiant et du libelle de l'élément svg
			idSvg="v";	
			lib="";
			for (var j=0; j<arrUsers.length; j++) {
				if(distrib.data[i][arrUsers[j]]!=""){
					idSvg +="_"+(j+1);
					lib += arrUsers[j]+" et "; 	
				}
			}
			if(idSvg!="v"){
				nbTag = distrib.data[i].nbtag;
				lib = lib.substr(0,lib.length-3)+": "+nbTag+" TAG(s)";
//console.log("initSvg",idSvg,nbTag,lib);
				
				//récupération du noeud svg
				nSvg = document.getElementById(idSvg);
				//conserve le nombre de tag 
				nSvg.setAttribute("nbTag", nbTag);
	
				//vérifie s'il faut tester la distribution
				if(nbTag>arrValCheck["matrix"][0]){
					distribCheck.push(idSvg);
				}
				if(nbTag>arrValCheck["arc"][0]){
					distribCheck.push(idSvg);
				}
					
				//met à jour les couleurs
				setColorDistrib(nSvg);
							
				//ajout des événements
				nSvg.setAttribute("onmouseover", "document.getElementById('VennSelect').innerHTML='"+lib+"'");
				nSvg.setAttribute("onclick", "changeDistrib(this.id);initGraph(this.id,'"+lib+"');");
			} 
		}
	}

function changeDistrib(id){
	
	if(distribSelect){
		//redonne la couleur d'origine à la distribution
		distribSelect.setAttribute("fill", distribSelect.getAttribute("cDistrib"));
	}
	//stocke la nouvelle sélection
	distribSelect = document.getElementById(id);
	//stocke la couleur d'origine de la distibution
	distribSelect.setAttribute("cDistrib", distribSelect.getAttribute("fill"));
	//met la sélection en vert
	distribSelect.setAttribute("fill", "green");

}

function setColorDistrib(nSvg){

	var nbTag = nSvg.getAttribute("nbTag"); 		
	var typeVisu = document.getElementById('typeVisu').value;
//console.log("setColorDistrib","nbTag:",nbTag,"typeVisu:",typeVisu);

	//suppression du style par défaut s'il existe
	if(nSvg.hasAttribute("style"))nSvg.removeAttribute("style");
			
	//calcul de la couleur
	c = colorD(nbTag).color;		

	//modification de la couleur de l'élément suivant le nombre de lien
	nSvg.setAttribute("fill", c);

	//met un contour rouge si le rendu prend trop de temps
	//pour un type de visualisation
//console.log(nbTag,arrValCheck[typeVisu][0]);
	if(nbTag>arrValCheck[typeVisu][0]){
		nSvg.setAttribute("stroke-width", 10);
		nSvg.setAttribute("stroke", "red");
	}else{
		nSvg.setAttribute("stroke", "#ffffff");
		nSvg.setAttribute("stroke-width", 2);
	}

}

function calcQuery(id){
	var reg=new RegExp("[_]+", "g");
	var arrQ = id.split(reg);
	var Q="";
	//ATTENTION le premier élément du tableau = "v"
	//on commence avec i= 1
	for (var i=1; i<arrQ.length; i++) {
 		Q += arrUsers[arrQ[i]-1] + ",";
	}
	Q = Q.substr(0,Q.length-1);
	return Q;	 
}

function initGraph(id, lib){

document.body.setAttribute("style","cursor:wait");//marche pas

  //calcul de la requête
  var Q = calcQuery(id);
  
  //vérifie si la distribution est trop importante
  distribTooBig = document.getElementById(id).getAttribute("stroke")=="red";
//console.log("initGraph",distribTooBig);
    
  //requête pour les nouvelles valeurs
  var url = ajaxPathWeb+"?f=GetUsersTagLinks&tag=dhyp&users="+Q+"&TooBig="+distribTooBig;
  //conserve le json pour pouvoir filtrer
  json = GetResult(url);
  data = eval('(' + json + ')');

  //mise à jour du titre
  document.getElementById('figTitre').innerHTML="PERMUTATION SELECTIONNEE (<a href='"+url+"' target='_blank' >data</a>) :<br/>"+lib;

  //mise à jour des couleurs
  colorN = pv.Scale.log(data.nodes, function(d) d.group).range(cNmin, cNmax);
  colorL = pv.Scale.log(data.links, function(d) d.value).range(cLmin, cLmax);
  colorF = pv.Scale.log(data.nodes, function(d) d.LinkDegree).range(cFmin, cFmax);

  //mise à jour de l'échelle pour la taille des caractères
  NbOct = pv.Scale.linear(data.nodes, function(d) d.group).range(18, 64);

  //mise à jour des légendes	
  dataLegL = GetDataLegL();
  filtreLeg["nbOcc"]=dataLegL;
  legL.render();

  dataLegN = GetDataLegN();
  filtreLeg["nbUti"]=dataLegN;
  legN.render();

  dataLegF = GetDataLegF();
  filtreLeg["nbLien"]=dataLegF;
  legF.render();
//console.log(dataLegL,dataLegN,dataLegF);
//console.log(filtreLeg);

legFiltre.visible(true);
legFiltre.render();

  //vérifie le type de graphique à afficher
  var typeVisu = document.getElementById('typeVisu').value;

  //vérifie si la distribution n'est pas trop grande
  if(distribTooBig){
	//crée un filtre sur les trois premières valeure de chaque data
	filtreLeg["nbOcc"] = [];
	filtreLeg["nbLien"] = [];
	filtreLeg["nbUti"] = [];
	for (var i= 0; i < 3; i++){
		filtreLeg["nbOcc"].push(dataLegL[i]);
		filtreLeg["nbUti"].push(dataLegN[i]);
		filtreLeg["nbLien"].push(dataLegF[i]);
	}
  }
//console.log("initGraph",filtreLeg);  

  filtreData();

document.body.style.cursor = 'auto';

}

function ShowTypeVisu(typeVisu){
	  
	  if(typeVisu=="matrix"){
		visZP.visible(false)
			.width(function(){return 0})
	    	.height(function(){return 0})
			;
		visM.width(dataM.nodes.length * 10)
	    	.height(dataM.nodes.length * 10)
			.top(200)
			;
	  }else{
		visZP.visible(true)
			.width(w)
	    	.height(h)
			;
		visM.width(function(){return 0})
	    	.height(function(){return 0})
			.top(function(){return 0})
			;
	  }
}

function GetDataLegL(){
  var dt=[],val;
  if(data.links.length>0){
	dt = filtreDoublons(data.links.map(function(d) d.value));
	//vérifie si le tableau des couleurs est valide	
  	if(dt.length==1){
		val=parseInt(dt[0]);
		dt = [val+1,val];
		colorL = pv.Scale.log(dt).range(cLmin, cLmax);
  	}
  }else{
  	if(data.nodes.length>0){
		dt = [2,1];
		colorL = pv.Scale.log(dt).range(cLmin, cLmax);
  	}
  }
  return dt;
}

function GetDataLegN(){
  var dt=[],val;
  if(data.nodes.length>0){
	dt = filtreDoublons(data.nodes.map(function(d) d.group));
	//vérifie si le tableau des couleurs est valide	
  	if(dt.length==1){
		val=parseInt(dt[0]);
		dt = [val+1,val];
		colorN = pv.Scale.log(dt).range(cNmin, cNmax);
  	}
  }
  return dt;
}

function GetDataLegF(){
  var dt=[],val;
  if(data.nodes.length>0){
	dt = filtreDoublons(data.nodes.map(function(d) d.LinkDegree));
	//vérifie si le tableau des couleurs est valide	
  	if(dt.length==1){
		val=parseInt(dt[0]);
		dt = [val+1,val];
		colorF = pv.Scale.log(dt).range(cFmin, cFmax);
  	}
  }
  return dt;
}

function filtreDoublons(dt){
  //ordonne le tableau en ordre croissant
  dt.sort(function(a, b) b - a);

  //filtre les doublons
  dt = dt.filter(function(element, index, array) {  
	  	if(index==0) return element;
		else if(element != array[index-1])return element;
	});  

//console.log(dt);
  return dt;

}

function changeType(type){
//console.log("changeType:type:",type,distribCheck);
	for(var i= 0; i < distribCheck.length; i++){
		setColorDistrib(document.getElementById(distribCheck[i]));
	}
}

	
function filtreVis(event,typeFiltre,val){

	//la récupération de l'événement ne marche pas avec firefox
	var n = event.target;
	var arr=[];
//console.log(n,typeFiltre);
//console.log(filtreLeg[typeFiltre]);

	//change la couleur du bouton
	//ajoute ou supprime les valeurs au filtre
console.log(n.getAttribute("fill"));
	if(n.getAttribute("fill")=="red"){
		n.setAttribute("fill", "green");
		filtreLeg[typeFiltre].push(val);
	}else{
		n.setAttribute("fill", "red");
		for (var i= 0; i < filtreLeg[typeFiltre].length; i++){
			if(filtreLeg[typeFiltre][i]!=val)arr.push(filtreLeg[typeFiltre][i]);
		}
		filtreLeg[typeFiltre]=arr;		
	}
console.log(n.getAttribute("fill"));
//console.log(filtreLeg[typeFiltre]);
//console.log(dataA,dt);

	//calcule les nouvelles données
	filtreData();

}

function filtreData(){

	//filtre les noeuds
	var b1 = false, b2 = false, arrFiltreN = [], nodes = [], element;
    var dt = eval('(' + json + ')');
//console.log("nb nodes",dt.nodes.length);
	for (var index= 0; index < dt.nodes.length; index++){
		element = dt.nodes[index];
		//le LinkDegree
		b1=false;
		for (var i= 0; i < filtreLeg["nbLien"].length; i++){
			if(filtreLeg["nbLien"][i]==element.LinkDegree){
				b1 = true;
//console.log(filtreLeg["nbLien"][i],element.LinkDegree);
				break;
			} 
		}
		//le nb d'utilisateur 
		b2=false;
		for (var i= 0; i < filtreLeg["nbUti"].length; i++){
			if(filtreLeg["nbUti"][i]==element.group){
				b2 = true;
//console.log(filtreLeg["nbUti"][i],element.group);
				break;
			} 
		}
		if(b1 && b2){
			//conserve l'élément
			arrFiltreN.push(element);
			//enregistre le changement d'index
			nodes.push([index,arrFiltreN.length-1]); 
		}
	}

  	//filtre les liens
	var links = [], arrFiltreL = [];
  	var bS,bT;
//console.log("DEB TRI LIEN",links.length,arrFiltreL.length,dt.links.length);
	for (var index= 0; index < dt.links.length; index++){
		element = dt.links[index];
//console.log("liens",dt.links[index],element);
		bS=false;bT=false;
		//vérifie les liens sans noeud
		for (var i= 0; i < nodes.length; i++){
//console.log(nodes[i],element.source,element.target);
			if(nodes[i][0]==element.source)bS=true;
			if(nodes[i][0]==element.target)bT=true;
		}
		if(bS && bT){
			b=false;
			//filtre le nb d'occurence des liens 
//console.log("nbOcc",filtreLeg["nbOcc"]);
			for (var i= 0; i < filtreLeg["nbOcc"].length; i++){
				if(filtreLeg["nbOcc"][i]==element.value){
					links.push(element.source); 
					links.push(element.target); 
					b=true;
					break;
				}
			}
//console.log("occu",b,filtreLeg["nbOcc"][i],element.value);
			if(b)arrFiltreL.push(element);
		}
	} 
//console.log("FIN TRI",links.length,arrFiltreL.length,dt.links.length);

	//supprime les doublons de référence au noeud
	links = filtreDoublons(links);	
//console.log("FIN TRI",nodes,links);

	//reindex les liens
//console.log(nodes,links);
	for (var i= 0; i < arrFiltreL.length; i++){
		for (var j= 0; j < nodes.length; j++){
			if(arrFiltreL[i].source==nodes[j][0])arrFiltreL[i].source=nodes[j][1];
			if(arrFiltreL[i].target==nodes[j][0])arrFiltreL[i].target=nodes[j][1];
		}
	}

  	//met à jour les filtres de la légende 

	var arr = [arrFiltreN, arrFiltreL];

	//vérifie le type de graphique à afficher
	var typeVisu = document.getElementById('typeVisu').value;
	if(typeVisu=="matrix"){
		//met à jour les data
		dataM.nodes = arr[0];  
		dataM.links = arr[1];  

		visZP.visible(false)
			.width(function(){return 0})
	    	.height(function(){return 0})
			;
		visM.width(dataM.nodes.length * 10)
	    	.height(dataM.nodes.length * 10)
			.top(200)
			;

	}else{
	
		visZP.visible(true)
			.width(w)
	    	.height(h)
			;
		visM.width(function(){return 0})
	    	.height(function(){return 0})
			.top(function(){return 0})
			;
	
	  	//met à jour les data
		dataA.nodes = arr[0];  
		dataA.links = arr[1];  
	}

	//recalcule la visualisation
    matrix.reset();
    visM.render();

	//recalcule la visualisation
    arc.reset();
    visA.render();

//console.log(data,dataM,dataA);

}

