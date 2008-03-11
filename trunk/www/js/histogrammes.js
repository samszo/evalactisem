function pf_couleur(num, color){
    document.getElementById("colorpicker" + num).hidePopup();
    document.getElementById("tb_0" + num).value =color;
    document.getElementById("tb_0" + num).inputField.style.backgroundColor=color; 
}

//ajout samszo
function SetDonnees(result,param){
	
	arr = result.split("*");
	var parser = new DOMParser();
	xmlFlux = parser.parseFromString(result, "text/xml");

	iterSec = xmlFlux.evaluate("/marque", xmlFlux, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );
  	nSec = iterSec.iterateNext();
	for (var j = 0; j < nSec.childNodes.length; j++) {
		if(nSec.childNodes[j].tagName=="nom")
			document.getElementById("noms").value = nSec.childNodes[j].textContent;
		if(nSec.childNodes[j].tagName=="nombre")
			document.getElementById("donnees").value = nSec.childNodes[j].textContent;			
	}
   
}

//fin ajout samszo
function Requette(){

    var req= document.getElementById("selctreq").value;
	
	AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+req,'SetDonnees','');
}

function pf_dessin(dom_doc)
{




lien='stats.php?large='+escape("400");
lien=lien+'&haut='+escape('300');
lien=lien+'&titre='+escape(document.getElementById("titre").value);

//deb ajout samszo

//fin ajout samszo
lien=lien+'&donnees='+escape(document.getElementById("donnees").value);
lien=lien+'&noms='+escape(document.getElementById("noms").value);
lien=lien+'&type=histo';
lien=lien+'&col1='+escape('#FFCC33');
lien=lien+'&col2='+escape('#33FFFF');
lien=lien+'&col3='+escape('#000066');
lien=lien+'&col4='+escape('#000000');
document.getElementById("webFrame").setAttribute("src",lien);
alert(lien);
}


function show_tooltip(evt)
{
	var matrix = evt.target.ownerDocument.getElementById("root").getScreenCTM()
	var  decale_x = matrix.e 
	var  decale_y = matrix.f
	var values = document.getElementById("donnees").value.split(";")
	var barre = evt.target.getAttributeNS(null , "id")
	var numero = parseInt(barre.substring(4 , barre.length)) - 1
	if (numero >= 0)
	{	
		evt.target.ownerDocument.getElementById("tooltip").setAttributeNS(null , "transform", "translate(" + (evt.clientX - decale_x - 70) + "," + (evt.clientY - decale_y - 20))
		evt.target.ownerDocument.getElementById("tooltip_text").firstChild.data = values[numero]
		evt.target.ownerDocument.getElementById("tooltip").setAttributeNS(null , "visibility", "visible")
	}
}

function hide_tooltip(evt)
{
	evt.target.ownerDocument.getElementById("tooltip").setAttributeNS(null , "visibility", "hidden")
}

function StartSelectMenu(id){
 menu= document.getElementById(id);
 selc=menu.selectedItem.value;

var req=document.getElementById("selctreq");
req.value=selc;
Requette();


}
function DelIiciousTree(){
	 menu= document.getElementById("requette");
	 selct=menu.selectedItem.value;
	
	
	flux = res.split("*");
	
	if((selct=="GetAllBundles")||(selct=="GetAllTags")){
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","overlay/tree.php?box=box2&ParaNom=GetOntoTree&type=flux");
	}else
	if((selct=="GetAllPosts")||(selct=="GetRecentPosts")||(selct=="GetPosts")){
	
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","overlay/tableFlux.php?tag="+flux[0]+"&desc="+flux[1]+"&url="+flux[2]+"&date="+flux[3]+"&note="+flux[4]);
	}else{
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","http://www.msn.fr");
	}
}
