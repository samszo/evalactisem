
<script src="js/TradTagIeml.js">var TradIeml= new Traduction(); </script>

function pf_couleur(num, color){
    document.getElementById("colorpicker" + num).hidePopup();
    document.getElementById("tb_0" + num).value =color;
    document.getElementById("tb_0" + num).inputField.style.backgroundColor=color; 
}


function RecupDeliciusFlux(){
	
		query=document.getElementById("requette").selectedItem.value;
	
	if(query==""){
		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette=GetAllTags",'DelIiciousTree','');
	}
	if((query=="GetAllTags")||(query=="GetAllBundles")||(query=="GetAllPosts")){
		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query,'DelIiciousTree','');
    }else
    	if(query=="GetRecentPosts"){
    		tag=document.getElementById("id-tag").value;
    		count=document.getElementById("id-count").value;
    		//alert(tag);
    		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query+"&tag="+tag+"&count="+count,'DelIiciousTree','');
    }else
        if(query=="GetPosts"){
    		tag=document.getElementById("id-tag").value;
    		url=document.getElementById("id-url").value;
    		date=document.getElementById("id-date").value;
    		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query+"&tag="+tag+"&url="+url+"&date="+date,'DelIiciousTree','');
    }
}

function pf_dessin(result, param)
{
var parser = new DOMParser();
	xmlFlux = parser.parseFromString(result, "text/xml");
    //alert(result);
	iterSec = xmlFlux.evaluate("/marque", xmlFlux, null, XPathResult.ANY_TYPE, null );
  	
  	nSec = iterSec.iterateNext();
	for (var j = 0; j < nSec.childNodes.length; j++) {
		if(nSec.childNodes[j].tagName=="nom"){
			document.getElementById("noms").value = nSec.childNodes[j].textContent;
		    noms=nSec.childNodes[j].textContent;
		}
		if(nSec.childNodes[j].tagName=="nombre"){
			donnees = nSec.childNodes[j].textContent;	
		    	
		}
		
	}
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
//alert(lien);
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

function RepresGraphFlux(){
	
		query=document.getElementById("type").selectedItem.value;
	if(query==" "){
		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette=GetAllTags",'pf_dessin','');
	}else
		AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+query,'pf_dessin','');
	
}
function DelIiciousTree(result,param){
	
	query=document.getElementById("requette").selectedItem.value;
	var parser = new DOMParser();
	xmlFlux = parser.parseFromString(result, "text/xml");
    //alert(result);
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
	
}

function parser(result,param){
document.getElementById("iemlhisto").setAttribute("src",result);
}
function Trad_Pars_Ieml(){
var trad;

trad=TradIeml.recherchez('parler');
//alert(trad);
ieml=trad.split(";");
AjaxRequest("http://localhost/evalactisem/library/ExeAjax.php?f=GetGraph&code="+ieml[3],'parser','');
}
