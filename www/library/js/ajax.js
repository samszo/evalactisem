//--------------------------------------------
// AJAX Functions
//--------------------------------------------

function AppendSVG(url,doc, InSvg) {
  try {
	document.documentElement.style.cursor = "wait";

	if(!InSvg){
		//vide le conteneur
		while(doc.hasChildNodes())
			doc.removeChild(doc.firstChild);
	}

	var p = new XMLHttpRequest();
	p.onload = null;
	p.open("GET", url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{

	    var response = p.responseText;
		var parser=new DOMParser();
		var resultDoc=parser.parseFromString(response,"text/xml");
		var nodeToImport = document.importNode(resultDoc.documentElement, true); 
		doc.appendChild(nodeToImport);

	}
   } catch(ex2){alert("AppendSVG::"+ex2+":"+url);}
	document.documentElement.style.cursor = "auto";

}

function GetResult(url) {
  try {
	document.documentElement.style.cursor = "wait";

    var response = "";
	var p = new XMLHttpRequest();
	p.onload = null;
	//p.open("GET", urlExeAjax+"?f=GetCurl&url="+url, false);
	
	//problème de mauvais encodage du code ieml
	p.open("GET", encodeURI(url), false);
	//p.open("GET", url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
	}
	document.documentElement.style.cursor = "auto";
	return response;
	dump("GetResult OUT \n");
   } catch(ex2){alert(ex2)}
	document.documentElement.style.cursor = "auto";
}


function AppendResult(url,doc,ajoute) {
  try {
	document.documentElement.style.cursor = "wait";

	dump("AppendResult IN "+url+"\n");
	p = new XMLHttpRequest();
	p.onload = null;
	p.open("GET", encodeURI(url), false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
	    //alert(response);
		xulData="<box id='dataBox' flex='1'  " +
	          "xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'>" +
	          response + "</box>";
		var parser=new DOMParser();
		var resultDoc=parser.parseFromString(xulData,"text/xml");
		if(!ajoute){
			//vide le conteneur
			while(doc.hasChildNodes())
				doc.removeChild(doc.firstChild);
		}
		//ajoute le résultat
		doc.appendChild(resultDoc.documentElement);
	}
   } catch(ex2){alert(ex2);}
	document.documentElement.style.cursor = "auto";
}


function AfficheSvg(response,params) {
   	alert(params+response);
	document.getElementById(params).firstChild.data = response;
	document.getElementById('proc-trace').value = response;
}


function AfficheResult(response,params) {
   	alert(params);
	document.getElementById(params).value = response;
}

function RefreshResult(response, params) {
   	//alert(url);
	arrP = params.split(",");
	document.getElementById(arrP[0]).value = response;
	AjaxRequest(arrP[1],"AfficheResult",arrP[2])
}
function processReqChange() {

	try {
	   	////console.log("state:"+this.req.readyState);
	} catch (e) {}

	if (this.req.readyState == 4) {		// quand le fichier est chargé
		

		if (this.req.status == 200) {			// detécter problèmes de format


			//eval(this.fonction_sortie+"(this.req.responseXML.documentElement)");
			
			eval(this.fonction_sortie+"(this.req.responseText)");
            
		} else {

			alert("Il y avait un probleme avec le XML: " + this.req.statusText);

		}
	}
}
function AjaxRequest(url,fonction_sortie,params,id) {
   
	try {
	document.documentElement.style.cursor = "wait";

 	this.url = encodeURI(url);
 	this.fonction_sortie = fonction_sortie;
 	this.params = params;
	this.id=id;

	var ajaxRequest = this;

    if (window.XMLHttpRequest) {

	    this.req = new XMLHttpRequest();										// XMLHttpRequest natif (Gecko, Safari, Opera, IE7)

		this.req.onreadystatechange = function () { processReqChange(); }
		this.req.open("GET", this.url,true);
		this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
        this.req.send(null);

	} else if (window.ActiveXObject) {

	    this.req = new ActiveXObject("Microsoft.XMLHTTP");						 // IE/Windows ActiveX

        if (this.req) {
            this.req.onreadystatechange = this.req.onreadystatechange = function () { processReqChange(); }
            this.req.open("POST", this.url,false);
			this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
            this.req.send(this.urlparams);
		}

    } else {

		alert("Votre navigateur ne connait pas l'objet XMLHttpRequest.");

	}
  	} catch (e) {alert("ajax:AjaxRequest:url:"+url+" :"+e)}

	document.documentElement.style.cursor = "auto";

}
function AjaxRequestPost(url,urlparams,fonction_sortie,params,id) {
   
	document.documentElement.style.cursor = "wait";

 	this.url = encodeURI(url);
 	this.fonction_sortie = fonction_sortie;
 	this.urlparams =encodeURI(urlparams);
 	this.params = params;
	this.id=id;
	//alert(params);
 
	var ajaxRequest = this;

    if (window.XMLHttpRequest) {

	    this.req = new XMLHttpRequest();										// XMLHttpRequest natif (Gecko, Safari, Opera, IE7)

		this.req.onreadystatechange = function () { processReqChange(); }
        
		this.req.open("POST", this.url,true);
		this.req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");  
        this.req.send(this.urlparams);

		try {
	    	//console.log("request: "+url);
	   	} catch (e) {}

	} else if (window.ActiveXObject) {

	    this.req = new ActiveXObject("Microsoft.XMLHTTP");						 // IE/Windows ActiveX

        if (this.req) {
            this.req.onreadystatechange = this.req.onreadystatechange = function () { processReqChange(); }
            this.req.open("POST", this.url,true);
			this.req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");  
            this.req.send(this.urlparams);
		}

    } else {

		alert("Votre navigateur ne connait pas l'objet XMLHttpRequest.");

	}
	document.documentElement.style.cursor = "auto";

}



function AppendResultPost(url,urlparams,doc,ajoute) {
  
  try {
	document.documentElement.style.cursor = "wait";
  
	dump("AppendResultPost IN "+url+"\n");
	p = new XMLHttpRequest();
	p.onload = null;
	p.open("POST",url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(urlparams);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
	    //alert(response);
		xulData="<box id='dataBox' flex='1'  " +
	          "xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'>" +
	          response + "</box>";
		var parser=new DOMParser();
		var resultDoc=parser.parseFromString(xulData,"text/xml");
		if(!ajoute){
			//vide le conteneur
			while(doc.hasChildNodes())
				doc.removeChild(doc.firstChild);
		}
		//ajoute le résultat
		doc.appendChild(resultDoc.documentElement);
		
	}
	dump("AppendResultPost OUT \n");
   } catch(ex2){alert(ex2);dump("::"+ex2);}
	document.documentElement.style.cursor = "auto";

}
