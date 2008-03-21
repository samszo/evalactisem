
<script src="js/TradTagIeml.js">var TradIeml= new Traduction(); </script>



function pf_couleur(num, color){
    document.getElementById("colorpicker" + num).hidePopup();
    document.getElementById("tb_0" + num).value =color;
    document.getElementById("tb_0" + num).inputField.style.backgroundColor=color; 
}

//ajout samszo
function SetDonnees(result,param){
	
	var parser = new DOMParser();
	xmlFlux = parser.parseFromString(result, "text/xml");
    alert(result);
	iterSec = xmlFlux.evaluate("/marque", xmlFlux, null, XPathResult.ANY_TYPE, null );
  	
  	nSec = iterSec.iterateNext();
	
	
	for (var j = 0; j < nSec.childNodes.length; j++) {
		if(nSec.childNodes[j].tagName=="nom"){
			document.getElementById("noms").value = nSec.childNodes[j].textContent;
		    tag=nSec.childNodes[j].textContent;alert(nSec.childNodes[j].textContent);
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
	}
    
   
}

//fin ajout samszo
function Requette(query){
	if((query=="GetAllTags")||(query=="GetAllBundles")||(query=="GetAllPosts")||(query=="tagsFbundles")){
		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query,'SetDonnees','');
    }else
    	if(query=="GetRecentPosts"){
    		tag=document.getElementById("id-tag").value;
    		count=document.getElementById("id-count").value;
    		//alert(tag);
    		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query+"&tag="+tag+"&count="+count,'SetDonnees','');
    }else
        if(query=="GetPosts"){
    		tag=document.getElementById("id-tag").value;
    		url=document.getElementById("id-url").value;
    		date=document.getElementById("id-date").value;
    		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query+"&tag="+tag+"&url="+url+"&date="+date,'SetDonnees','');
    }
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

function StartSelectMenu(query){
	
	//query=document.getElementById(id).selectedItem.value;
	//alert(query);
	Requette(query);


}
function DelIiciousTree(){
	
	query=document.getElementById("requette").selectedItem.value;
	
	if((query=="GetAllBundles")||(query=="GetAllTags")){
		
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","overlay/tree.php?box=box2&ParaNom=GetOntoTree&type=flux");
	}else
	if((query=="GetAllPosts")||(query=="GetRecentPosts")||(query=="GetPosts")){
	
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","overlay/tableFlux.php?tag="+tag+"&desc="+desc+"&url="+url+"&date="+date);
	}
	
}
function parser(result,param){
alert(result);
}
function Trad_Pars_Ieml(){
var trad;

trad=TradIeml.recherchez('parler');
alert(trad);
ieml=trad.split(";");
AjaxRequest("http://localhost/evalactisem/library/ExeAjax.php?f=Parse&code="+ieml[0],'parser','');
}
