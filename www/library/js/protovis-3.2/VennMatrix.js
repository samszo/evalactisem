var data = {"nodes":[],"links":[]};
var dataM = {"nodes":[],"links":[]};
var dataA = {"nodes":[],"links":[]};
var json;
var url = ajaxPathWeb+"?f=GetUsersTagLinks&tag="+aggTag+"&users="+arrUsers.join(",");

//r�cup�ration des valeurs de distribution
url = ajaxPathWeb+"?f=GetUsersTagsDistrib&tag="+aggTag+"&users="+arrUsers.join(",");
var distrib = eval('(' + GetResult(url) + ')');
//pour stocker les distribution qui posent des probl�me de rendu
var distribCheck = [];
var valCheck = 200;

//mise � jour du lien vers les data
document.getElementById('distribData').innerHTML= '<a href="'+url+'" target="_blank" >data</a>'; 


/* d�finition des couleurs cf. http://www.w3schools.com/tags/ref_colormixer.asp*/
var cNmax = "#CC0066", cNmin = "#FF9966",
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

		//mise � jour du svg
		var idSvg, nSvg, nbTag, lib, c;
		for(var i= 0; i < distrib.data.length; i++)
		{
			//construction de l'identifiant et du libelle de l'�l�ment svg
			idSvg="v";	
			lib="";
			for (var j=0; j<arrUsers.length; j++) {
				if(distrib.data[i][arrUsers[j]]!=""){
					idSvg +="_"+(j+1);
					lib += arrUsers[j]+" et "; 	
				}
			}
			nbTag = distrib.data[i].nbtag;
			lib = lib.substr(0,lib.length-3)+": "+nbTag+" TAG(s)";
			
			//r�cup�ration du noeud svg
			nSvg = document.getElementById(idSvg);

			//conserve le nombre de tag 
			nSvg.setAttribute("nbTag", nbTag);

			//v�rifie s'il faut tester la distribution
			if(nbTag>valCheck){
				distribCheck.push(idSvg);
			}

			//met � jour les couleurs
			setColorDistrib(nSvg);
						
			//ajout des �v�nements
			nSvg.setAttribute("onmouseover", "document.getElementById('VennSelect').innerHTML='"+lib+"'");
			nSvg.setAttribute("onclick", "initGraph(this.id,'"+lib+"');");
 
		}
	}

function setColorDistrib(nSvg){

	var nbTag = nSvg.getAttribute("nbTag"); 		
	var typeVisu = document.getElementById('typeVisu').value;
//console.log("setColorDistrib","nbTag:",nbTag,"typeVisu:",typeVisu);

	//suppression du style par d�faut s'il existe
	if(nSvg.hasAttribute("style"))nSvg.removeAttribute("style");
			
	//calcul de la couleur
	c = colorD(nbTag).color;		

	//modification de la couleur de l'�l�ment suivant le nombre de lien
	nSvg.setAttribute("fill", c);

	//met un contour rouge si le rendu prend trop de temps
	//pour un type de visualisation
	if(nbTag>valCheck && typeVisu=="matrix"){
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
	//ATTENTION le premier �l�ment du tableau = "v"
	//on commence avec i= 1
	for (var i=1; i<arrQ.length; i++) {
 		Q += arrUsers[arrQ[i]-1] + ",";
	}
	Q = Q.substr(0,Q.length-1);
	return Q;	 
}

function initGraph(id, lib){

document.body.setAttribute("style","cursor:wait");//marche pas

  //calcul de la requ�te
  var Q = calcQuery(id);
  //r�cup�ration des nouvelles valeurs
  var url = ajaxPathWeb+"?f=GetUsersTagLinks&tag=dhyp&users="+Q;
  //conserve le json pour pouvoir filtrer
  json = GetResult(url);
  data = eval('(' + json + ')');

  //mise � jour du titre
  document.getElementById('figTitre').innerHTML="PERMUTATION SELECTIONNEE (<a href='"+url+"' target='_blank' >data</a>) :<br/>"+lib;


  //mise � jour des couleurs
  colorN = pv.Scale.log(data.nodes, function(d) d.group).range(cNmin, cNmax);
  colorL = pv.Scale.log(data.links, function(d) d.value).range(cLmin, cLmax);
  colorF = pv.Scale.log(data.nodes, function(d) d.LinkDegree).range(cFmin, cFmax);

  //mise � jour des l�gendes	
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

  //v�rifie le type de graphique � afficher
  var typeVisu = document.getElementById('typeVisu').value;

  //mise � jour des visualisations
  if(typeVisu=="matrix"){
    dataM = data;
    dataA = {"nodes":[],"links":[]};
	visZP.visible(false)
		.width(function(){return 0})
    	.height(function(){return 0})
		;
	visM.width(dataM.nodes.length * 10)
    	.height(dataM.nodes.length * 10)
		.top(200)
		;
  }else{
    dataM = {"nodes":[],"links":[]};
    dataA = data;
	visZP.visible(true)
		.width(w)
    	.height(h)
		;
	visM.width(function(){return 0})
    	.height(function(){return 0})
		.top(function(){return 0})
		;
  }
//console.log(data,dataM,dataA);
  
  //vide le cache des visualisation
  matrix.reset();
  arc.reset();

  //rendu des visualisations 
  visM.render();
  visA.render();

document.body.style.cursor = 'auto';

}

function GetDataLegL(){
  var dt=[],val;
  if(data.links.length>0){
	dt = filtreDoublons(data.links.map(function(d) d.value));
	//v�rifie si le tableau des couleurs est valide	
  	if(dt.length==1){
		val=parseInt(dt[0]);
		dt = [val+1,val];
		colorL = pv.Scale.log(dt).range(cLmin, cLmax);
  	}
  }
  return dt;
}

function GetDataLegN(){
  var dt=[],val;
  if(data.nodes.length>0){
	dt = filtreDoublons(data.nodes.map(function(d) d.group));
	//v�rifie si le tableau des couleurs est valide	
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
	//v�rifie si le tableau des couleurs est valide	
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
//console.log("changeType:type:",type);
	for(var i= 0; i < distribCheck.length; i++){
		setColorDistrib(document.getElementById(distribCheck[i]));
	}
}

function filtreVis(event,typeFiltre,val){
	
	var n = event.target;
	var arr=[];
//console.log(n,typeFiltre);
//console.log(filtreLeg[typeFiltre]);

	//change la couleur du bouton
	//ajoute ou supprime les valeurs au filtre
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
//console.log(filtreLeg[typeFiltre]);
//console.log(dataA,dt);

	//calcule les nouvelles donn�es
	arr = filtreData();

	//v�rifie le type de graphique � afficher
	var typeVisu = document.getElementById('typeVisu').value;
	if(typeVisu=="matrix"){
		//met � jour les data
		dataM.nodes = arr[0];  
		dataM.links = arr[1];  
		//recalcule la visualisation
	    matrix.reset();
	    visM.render();
	}else{
	  	//met � jour les data
		dataA.nodes = arr[0];  
		dataA.links = arr[1];  
		//recalcule la visualisation
	    arc.reset();
	    visA.render();
	}
//console.log(data,dataM,dataA);

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
			//conserve l'�l�ment
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
		//v�rifie les liens sans noeud
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

	//supprime les doublons de r�f�rence au noeud
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

  	//met � jour les filtres de la l�gende 

	return [arrFiltreN, arrFiltreL];

}

