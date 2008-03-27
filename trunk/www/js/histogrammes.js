<script src="js/TradTagIeml.js">var TradIeml= new Traduction(); </script>

function pf_couleur(num, color){
    document.getElementById("colorpicker" + num).hidePopup();
    document.getElementById("tb_0" + num).value =color;
    document.getElementById("tb_0" + num).inputField.style.backgroundColor=color; 
}


function RecupDeliciousFlux(){
	
		query_flux=document.getElementById("requette").selectedItem.value;
		query_graph=document.getElementById("type").selectedItem.value;
	    
	
	if((query_flux=="")){
		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette=GetAllTags"+"&req="+document.getElementById("type").selectedItem.value ,'DelIiciousTreeGraph','');
	}
	if(query_graph=""){
		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&req=GetAllTags"+"&requette="+query_flux,'DelIiciousTreeGraph','');
	}
	if((query_flux=="GetAllTags")||(query_flux=="GetAllBundles")||(query_flux=="GetAllPosts")){
		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query_flux+"&req="+document.getElementById("type").selectedItem.value ,'DelIiciousTreeGraph','');
    }else
    	if(query_flux=="GetRecentPosts"){
    		tag=document.getElementById("id-tag").value;
    		count=document.getElementById("id-count").value;
    		//alert(tag);
    		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query_flux+"&tag="+tag+"&count="+count+"&req="+document.getElementById("type").selectedItem.value ,'DelIiciousTreeGraph','');
    }else
        if(query_flux=="GetPosts"){
    		tag=document.getElementById("id-tag").value;
    		url=document.getElementById("id-url").value;
    		date=document.getElementById("id-date").value;
    		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query_flux+"&tag="+tag+"&url="+url+"&date="+date+"&req="+document.getElementById("type").selectedItem.value,'DelIiciousTreeGraph','');
    }
}

function pf_dessin(noms, donnees)
{
	lien='library/stats.php?large='+escape("400");
	lien=lien+'&haut='+escape('300');
	lien=lien+'&titre='+escape(document.getElementById("titre").value);
	lien=lien+'&donnees='+escape(donnees);
	lien=lien+'&noms='+escape(noms);
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


function DelIiciousTreeGraph(result,param){
	
	query=document.getElementById("requette").selectedItem.value;
	
	var parser = new DOMParser();
	xmlFlux = parser.parseFromString(result, "text/xml");
    alert(result);
	iterSec = xmlFlux.evaluate("/marque", xmlFlux, null, XPathResult.ANY_TYPE, null );
  	
  	nSec = iterSec.iterateNext();
	
	
	for (var j = 0; j < nSec.childNodes.length; j++) {
		if(nSec.childNodes[j].tagName=="nom"){
			document.getElementById("noms").value = nSec.childNodes[j].textContent;
		    tag=nSec.childNodes[j].textContent;
		}
		if(nSec.childNodes[j].tagName=="nombre"){
			document.getElementById("donnees").value = nSec.childNodes[j].textContent;	
		    	
		}
		if(nSec.childNodes[j].tagName=="description"){
			desc = nSec.childNodes[j].textContent;
		}	
		if(nSec.childNodes[j].tagName=="url"){
			url= nSec.childNodes[j].textContent;
		}
		if(nSec.childNodes[j].tagName=="date"){
			date = nSec.childNodes[j].textContent;	
		}
		if(nSec.childNodes[j].tagName=="noms"){
			document.getElementById("noms").value = nSec.childNodes[j].textContent;
		    abscises=nSec.childNodes[j].textContent;
		}
		if(nSec.childNodes[j].tagName=="donnees"){
			ordonnees = nSec.childNodes[j].textContent;	
		    	
		}
			
	
	}

    
	if((query=="GetAllBundles")||(query=="GetAllTags")||(query=="")){
		
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","overlay/tree.php?box=box2&ParaNom=GetOntoTree&type=flux");
	}else
	if((query=="GetAllPosts")||(query=="GetRecentPosts")||(query=="GetPosts")){
	    //alert(query);
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","overlay/tableFlux.php?tag="+tag+"&desc="+desc+"&url="+url+"&date="+date);
	}
	pf_dessin(abscises, ordonnees);
   
}

function parser(result,param){
document.getElementById("iemlhisto").setAttribute("src",result);
}
function Trad_Pars_Ieml(){
var trad;

trad=TradIeml.recherchez('parler');
alert(trad);
ieml=trad.split(";");
AjaxRequest("http://localhost/evalactisem/library/ExeAjax.php?f=GetGraph&code="+ieml[3],'parser','');
}

function Trad(){
	//alert("bonjour");
	var box =document.getElementById("RepGraph");
	childbox=box.firstChild;
	while(childbox){
		childbox=box.firstChild;
		box.removeChild(childbox);
		childbox=box.firstChild;
    }
    Tradframe=document.createElement("iframe");
    Tradframe.setAttribute("flex",1);
    Tradframe.setAttribute("src","Traduction.xul");
    box.appendChild(Tradframe);
}

function AddTrad1()
{
	aTrad=document.getElementById("code-trad-flux").value;
	rTrad=TradIeml.recherchez(aTrad);
	if(rTrad==""){
		document.getElementById("trad-message").value="Il n'exite pas une  traduction qui correspond a cet mot veuillez une traduire a partir de la table ieml "
	}
	else{
	 	
	 	Trad=rTrad.split("*");
	    CarIeml=Trad[0].split(";");
	 	DiscIeml=Trad[1].split(";");
	 	if(CarIeml.length >2){
	 		alert("il existe plusieurs possibilité veuillez choisir la plus pertiente");
	 		ChoixTrad(CarIeml,DiscIeml);
	 		
	 	}
	}
	//construction de la requete
	//url = urlExeAjax+"?f=AddTrad&idIeml="+idIeml.value+"&idflux="+idflux.value;
    //AjaxRequest("http://localhost/evalactisem/library/ExeAjax.php?f=AddTrad1",'ServDictio','');
}
function ChoixTrad(CarIeml,DiscIeml){
	var box =document.getElementById("box");
	if(box.hasChildNodes()){
		dernier=box.lastChild;
		box.removeChild(dernier);
	}
		boxTrad=document.createElement("listbox");
		boxTrad.setAttribute("id","Tradbox");
		boxTrad.setAttribute("onclick","StartSelecTrad()");
		listhead=document.createElement("listhead");
		listheader1=document.createElement("listheader");
		listheader1.setAttribute("label","Mot ieml");
		listheader2=document.createElement("listheader");
		listheader2.setAttribute("label","descripiton");
		listhead.appendChild(listheader1);
		listhead.appendChild(listheader2);
		boxTrad.appendChild(listhead);
		listcols=document.createElement("listcols");
		listcol1=document.createElement("listcol");
		listcol2=document.createElement("listcol");
		listcols.appendChild(listcol1);
		listcols.appendChild(listcol2);
		boxTrad.appendChild(listcols);
		
		alert(CarIeml.length);
		for(i=0;i<CarIeml.length;i++){
			listitem=document.createElement("listitem");
			cellcar=document.createElement("listcell");
			cellcar.setAttribute("label",CarIeml[i]);
			celldisc=document.createElement("listcell");
			celldisc.setAttribute("label",DiscIeml[i]);
			listitem.appendChild(cellcar);
			listitem.appendChild(celldisc);
			listitem.appendChild(cellcar);
			listitem.appendChild(celldisc);
			boxTrad.appendChild(listitem);
		}
		
		
		box.appendChild(boxTrad);
	
}
function StartSelecTrad(){
	var box=document.getElementById("Tradbox");
	cellC=box.selectedItem.childNodes[0];
	cellD=box.selectedItem.childNodes[1];
	Carieml=cellC.getAttribute('label');
	Discieml=cellD.getAttribute('label');
	txtCode = document.getElementById("code-trad-ieml");
	txtCode.value=cellC.getAttribute('label');
	txtDescp= document.getElementById("lib-trad-ieml");
	txtDescp.value = cellD.getAttribute('label');
	
	//alert(Carieml+";"+Discieml);
	//return Carieml+";"+Discieml;
}
