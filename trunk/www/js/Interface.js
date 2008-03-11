var urlExeAjax = "http://localhost/evalactisem/library/ExeAjax.php";
var xmlFlux;

function AddTrad()
{
	//récupération des valeurs
	var idIeml = document.getElementById("id-trad-ieml");
	var idflux = document.getElementById("id-trad-flux");
	
	//construction de la requete
	url = urlExeAjax+"?f=AddTrad&idIeml="+idIeml.value+"&idflux="+idflux.value;
    
	//vérification des valeurs
	if(idIeml.value=="" || idflux.value=="")
		document.getElementById("trad-message").value = "Veuillez sélectionner une valeur pour chaque langage";
	else
		makeRequest(url,"trad-message");
		
}
function SupTrad()
{
	//récupération des valeurs
	var idIeml = document.getElementById("id-trad-ieml");
	var idflux = document.getElementById("id-trad-flux");
    alert(idIeml.value);
    var listbox=document.getElementById("boxlist");
    var select=listbox.selectedIndex;
	url = urlExeAjax+"?f=SupTrad&idIeml="+idIeml.value+"&idflux="+idflux.value;
   
	//vérification des valeursboxlistJ
	if(idIeml.value=="" || idflux.value=="")
		document.getElementById("trad-message").value = "Veuillez sélectionner une traduction";
	else
		makeRequest(url,"trad-message");
		listbox.removeItemAt(select);
}

function ChargeBrower(id,url)
{
	/* bug sur le chargement de l'overlay tree.php : les valeurs du rdf ne sont pas chargée
	on charge un iframe avec les paramètres de page
	*/
	document.loadOverlay(url,null);
	
	//var Brower = document.getElementById(id);
	//alert(url);
	//pour un iframe
	//Brower.setAttribute("src",url);
	//pour un brower
	//Brower.loadURI(url, null, null);
	/*
	newChild = makeRequest('http://localhost/mundilogiweb/ieml/overlay/iframe.php');
	parent = document.getElementById("singlebox");
	while(parent.hasChildNodes())
	  parent.removeChild(parent.firstChild);
	parent.value=newChild;
	*/
	
}


	function makeRequest(url,id) {

        var httpRequest = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
                // Voir la note ci-dessous à propos de cette ligne
            }
        }
        else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {}
            }
        }

        if (!httpRequest) {
            alert('Abandon :( Impossible de créer une instance XMLHTTP');
            return false;
        }
        httpRequest.onreadystatechange = function() { returnContents(httpRequest,id); };
        httpRequest.open('GET', url, true);
        httpRequest.send(null);

    }

    function returnContents(httpRequest,id) {

        if (httpRequest.readyState == 4) {
            if (httpRequest.status == 200) {
                //alert(httpRequest.responseText);
				//affichage de la réponse
				document.getElementById(id).value = httpRequest.responseText;
                //return httpRequest.responseText;
            } else {
                alert('Un problème est survenu avec la requête.');
            }
        }

    }
    function alertContents(httpRequest) {

        if (httpRequest.readyState == 4) {
            if (httpRequest.status == 200) {
                alert(httpRequest.responseText);
            } else {
                alert('Un problème est survenu avec la requête.');
            }
        }

    }

	
function read(filepath) {
//http://xulfr.org/wiki/RessourcesLibs/LectureFichierCodeAvecCommentaires
 try  {
  //netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
 } catch (e) {
  alert("Permission refusée de lire le fichier (" + e + ")");
  return ;
 }

//Le fichier est ouvert
 var file =  Components.classes["@mozilla.org/file/local;1"]
            .createInstance(Components.interfaces.nsILocalFile);
 file.initWithPath(filepath);
 if ( file.exists() != true) {
  alert("Le fichier "+filepath+" n'existe pas");
  return ;
 }

 //Mode de lecture du fichier, un flux est nécessaire
 //Le second argument définit les différents modes de lecture parmis
 //PR_RDONLY     =0x01 lecture seulement
 //PR_WRONLY     =0x02 écriture seulement
 //PR_RDWR       =0x04 lecture ou écriture
 //PR_CREATE_FILE=0x08 si le fichier n'existe pas, il est créé (sinon, sans effet)
 //PR_APPEND     =0x10 le fichier est positionné à la fin avant chaque écriture
 //PR_TRUNCATE   =0x20 si le fichier existe, sa taille est réduite à zéro
 //PR_SYNC       =0x40 chaque écriture attend que les données ou l'état du fichier soit mis à jour
 //PR_EXCL       =0x80 idem que PR_CREATE_FILE, sauf que si le fichier existe, NULL est retournée
 //Le troisième argument définit les droits

 var inputStream = Components.classes["@mozilla.org/network/file-input-stream;1"]
         .createInstance( Components.interfaces.nsIFileInputStream );
 inputStream.init(file, 0x01, 00004, null);
 var sis = Components.classes["@mozilla.org/binaryinputstream;1"]
          .createInstance(Components.interfaces.nsIBinaryInputStream);

 sis.setInputStream( inputStream );
 var output = sis.readBytes( sis.available() );
 return output;
 }
 
 function startSelectTab()
{ 
var listbox=document.getElementById("boxlist");
var cell = listbox.selectedItem.childNodes[0]; // suivant l'index de colonne que vous desirez
var celldescp = listbox.selectedItem.childNodes[1];
var cellF = listbox.selectedItem.childNodes[2];
var celldescpF = listbox.selectedItem.childNodes[3];

txtIdieml=document.getElementById("id-trad-ieml");
txtIdieml.value=cell.getAttribute('label');
txtId10F=document.getElementById("id-trad-flux");
txtId10F.value=cellF.getAttribute('label');

txtCode = document.getElementById("code-trad-ieml");
txtCode.value=cell.getAttribute('label');
txtDescp= document.getElementById("lib-trad-ieml");
txtDescp.value = celldescp.getAttribute('label');
txtCodeF = document.getElementById("code-trad-flux");
txtCodeF.value=cellF.getAttribute('label');
txtDescpF= document.getElementById("lib-trad-flux");
txtDescpF.value = celldescpF.getAttribute('label');

}


