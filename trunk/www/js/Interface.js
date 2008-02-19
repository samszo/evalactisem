function AddTrad()
{
	//r�cup�ration des valeurs
	idIeml = document.getElementById("id-trad-ieml");
	id10eF = document.getElementById("id-trad-10eF");

	//construction de la requete
	url = urlExeAjax+"?f=AddTrad&idIeml="+idIeml.value+"&id10eF="+id10eF.value;

	//v�rification des valeurs
	if(idIeml.value=="" || id10eF.value=="")
		document.getElementById("trad-message").value = "Veuillez s�lectionner une valeur pour chaque langage";
	else
		AjaxRequest(url,"AfficheResult","trad-message");
		
}

function SetOnto(type,col,id,value)
{
	//construction de la requete
	url = urlExeAjax+"?f=SetOnto&type="+type+"&col="+col+"&id="+id+"&value="+value;

	//v�rification des valeurs
	//if(idIeml.value=="" || id10eF.value=="")
	//	document.getElementById("trad-message").value = "Veuillez s�lectionner une valeur pour chaque langage";
	//else
		AjaxRequest(url,"AfficheResult","onto-message-"+type);
		
}

function ChargeBrower(id,url)
{
	/* bug sur le chargement de l'overlay tree.php : les valeurs du rdf ne sont pas charg�e
	on charge un iframe avec les param�tres de page
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

	
function read(filepath) {
//http://xulfr.org/wiki/RessourcesLibs/LectureFichierCodeAvecCommentaires
 try  {
  //netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
 } catch (e) {
  alert("Permission refus�e de lire le fichier (" + e + ")");
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

 //Mode de lecture du fichier, un flux est n�cessaire
 //Le second argument d�finit les diff�rents modes de lecture parmis
 //PR_RDONLY     =0x01 lecture seulement
 //PR_WRONLY     =0x02 �criture seulement
 //PR_RDWR       =0x04 lecture ou �criture
 //PR_CREATE_FILE=0x08 si le fichier n'existe pas, il est cr�� (sinon, sans effet)
 //PR_APPEND     =0x10 le fichier est positionn� � la fin avant chaque �criture
 //PR_TRUNCATE   =0x20 si le fichier existe, sa taille est r�duite � z�ro
 //PR_SYNC       =0x40 chaque �criture attend que les donn�es ou l'�tat du fichier soit mis � jour
 //PR_EXCL       =0x80 idem que PR_CREATE_FILE, sauf que si le fichier existe, NULL est retourn�e
 //Le troisi�me argument d�finit les droits

 var inputStream = Components.classes["@mozilla.org/network/file-input-stream;1"]
         .createInstance( Components.interfaces.nsIFileInputStream );
 inputStream.init(file, 0x01, 00004, null);
 var sis = Components.classes["@mozilla.org/binaryinputstream;1"]
          .createInstance(Components.interfaces.nsIBinaryInputStream);

 sis.setInputStream( inputStream );
 var output = sis.readBytes( sis.available() );
 return output;
 }

