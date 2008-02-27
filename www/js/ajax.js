//--------------------------------------------
// AJAX Functions
//--------------------------------------------
var urlExeAjax = "/evalactisem";

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

function AjaxRequest(url,fonction_sortie,params) {
   
 	this.url = encodeURI(url);
 	this.fonction_sortie = fonction_sortie;
 	this.params = params;
	//alert(params);

	var ajaxRequest = this;

    if (window.XMLHttpRequest) {

	    this.req = new XMLHttpRequest();										// XMLHttpRequest natif (Gecko, Safari, Opera, IE7)

		try {
	    	netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");		// Mozilla Security
	   	} catch (e) {}

		this.req.onreadystatechange = function () { processReqChange(); }

		this.req.open("GET", this.url, true);
		this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
        this.req.send(null);

		try {
	    	//console.log("request: "+url);
	   	} catch (e) {}

	} else if (window.ActiveXObject) {

	    this.req = new ActiveXObject("Microsoft.XMLHTTP");						 // IE/Windows ActiveX

        if (this.req) {
            this.req.onreadystatechange = this.req.onreadystatechange = function () { processReqChange(); }
            this.req.open("GET", this.url, false);
			this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
            this.req.send();
		}

    } else {

		alert("Votre navigateur ne connait pas l'objet XMLHttpRequest.");

	}

}

function processReqChange() {

	try {
	   	////console.log("state:"+this.req.readyState);
	} catch (e) {}

	if (this.req.readyState == 4) {		// quand le fichier est chargé
		

		if (this.req.status == 200) {			// detécter problèmes de format

			try {
    			netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
   			} catch (e) {}

			try {
	   			////console.log(this.req.responseText);
			} catch (e) {}

			//eval(this.fonction_sortie+"(this.req.responseXML.documentElement)");
			eval(this.fonction_sortie+"(this.req.responseText,'"+this.params+"')");

		} else {

			alert("Il y avait un probleme avec le XML: " + this.req.statusText);

		}
	}
}

