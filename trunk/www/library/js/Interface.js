   
E="*";
P=";";

function ChargeCycle(key){
	//v�rifie que l'onglet n'est pas d�j� rempli
	if(document.getElementById('iemlCycle_'+key).hasChildNodes())
		return;
    document.getElementById('label_Maj').setAttribute('value','Veuillez patienter le chargement de cycle est en cours...');
    var meter=document.getElementById('Maj');
	meter.setAttribute("hidden","false");
	meter.setAttribute("value","50");
	
	url = urlAjax+"library/php/ExeAjax.php";
	urlparams="f=IemlCycle&key="+key;
	AppendResultPost(url,urlparams,document.getElementById('iemlCycle_'+key),false);
	meter.setAttribute("value","100");
	meter.setAttribute("hidden","true");
	document.getElementById('label_Maj').setAttribute("hidden","true");
		
}

function show_tooltip(evt)
{
        var matrix = evt.target.ownerDocument.getElementById("root").getScreenCTM()
        var  decale_x = matrix.e 
        var  decale_y = matrix.f
        var values = count.split(P)
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

function pf_couleur(num, color){
   
    document.getElementById("colorpicker" + num).hidePopup();
    document.getElementById("tb_0" + num).value =color;
    document.getElementById("tb_0" + num).inputField.style.backgroundColor=color; 
}
function GetFlux(){

    document.getElementById('label_Maj').setAttribute('value','Veuillez patienter la r�cup�ration du flux est en cours...');
    var meter=document.getElementById('Maj');
	meter.setAttribute("hidden","false");
	AjaxRequest(urlAjax+"library/php/RecupFlux.php?requette=GetAllPosts&req=GetAllTags",'' ,'');
	AjaxRequest(urlAjax+"library/php/RecupFlux.php?requette=GetAllTags&req=GetAllTags",'DelIiciousTreeGraph','');
	
}
function SetDonnee(){
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=Recup_onto_trad" ,'Trad_Pars_Ieml','');
	
}
function RecupDeliciousFlux(){
	   

		query_flux=document.getElementById("requette").selectedItem.value;
		query_graph=document.getElementById("type").selectedItem.value;
	    
	 
	if((query_flux=="GetAllTags")){
		var meter=document.getElementById('Maj');
	    meter.setAttribute("hidden","false");
		AjaxRequest(urlAjax+"library/php/RecupFlux.php?requette=GetAllTags"+"&req="+query_graph,'DelIiciousTreeGraph','');

	}
	
	if((query_flux=="GetAllBundles")||(query_flux=="GetAllPosts")){
		var meter=document.getElementById('Maj');
	    meter.setAttribute("hidden","false");
		AjaxRequest(urlAjax+"library/php/RecupFlux.php?requette="+query_flux+"&req="+document.getElementById("type").selectedItem.value ,'DelIiciousTreeGraph','','');
       
        
        if(document.getElementById("type").selectedItem.value="tagsFbundles")
        	document.getElementById("titre").value="Tags en fonction de count";
        else
        	document.getElementById("titre").value="Bundles en fonction de Tags";
        	
    }else
    	if(query_flux=="GetRecentPosts"){
    	    var meter=document.getElementById('Maj');
	        meter.setAttribute("hidden","false");
    		tag=document.getElementById("id-tag").value;
    		count=document.getElementById("id-count").value;
    		AjaxRequest(urlAjax+"library/php/RecupFlux.php?requette="+query_flux+"&tag="+tag+"&count="+count+"&req="+document.getElementById("type").selectedItem.value ,'DelIiciousTreeGraph','');
            if(document.getElementById("type").selectedItem.value=="tagsFbundles")
        		document.getElementById("titre").value="Bundles en fonction de Tags";
       	   else
        		document.getElementById("titre").value="Tags en fonction de count";
    }else
        if(query_flux=="GetPosts"){
            var meter=document.getElementById('Maj');
	        meter.setAttribute("hidden","false");
    		tag=document.getElementById("id-tag").value;
    		url=document.getElementById("id-url").value;
    		date=document.getElementById("id-date").value;
    		AjaxRequest(urlAjax+"library/php/RecupFlux.php?requette="+query_flux+"&tag="+tag+"&url="+url+"&date="+date+"&req="+document.getElementById("type").selectedItem.value,'DelIiciousTreeGraph','');
           
            if(document.getElementById("type").selectedItem.value=="tagsFbundles")
        		document.getElementById("titre").value="Bundels en fonction de Tags";
        	else
        		document.getElementById("titre").value="Tags en fonction de count";
    }


}

function pf_dessin(query)
{
	lien='library/php/stats.php?large='+escape("400");
	lien=lien+'&query='+query;
	lien=lien+'&haut='+escape('300');
	lien=lien+'&titre='+escape(document.getElementById("titre").value);
	lien=lien+'&type=pie';
	lien=lien+'&col1='+escape('#FFCC33');
	lien=lien+'&col2='+escape('#33FFFF');
	lien=lien+'&col3='+escape('#000066');
	lien=lien+'&col4='+escape('#000000');
	document.getElementById("webFrame").setAttribute("src",lien);
    
}

function DelIiciousTreeGraph(result,param){
        
	query=document.getElementById("requette").selectedItem.value;
	query_graph=document.getElementById("type").selectedItem.value;
	
	var parser = new DOMParser();
	xmlFlux = parser.parseFromString(result, "text/xml");
	iterSec = xmlFlux.evaluate("/marque", xmlFlux, null, XPathResult.ANY_TYPE, null );
  	
  	nSec = iterSec.iterateNext();
  	if(!nSec)
  		return;
	for (var j = 0; j < nSec.childNodes.length; j++) {
		if(nSec.childNodes[j].tagName=="tags"){
			
			nom = nSec.childNodes[j].textContent;
		    
		    tag=nSec.childNodes[j].textContent;
		    
		}
		if(nSec.childNodes[j].tagName=="count"){
			
			count = nSec.childNodes[j].textContent;	
		    
		}
		
		if(nSec.childNodes[j].tagName=="count"){
			//document.getElementById("donnees").value = nSec.childNodes[j].textContent;	
		    
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
		if(nSec.childNodes[j].tagName=="note"){
			note = nSec.childNodes[j].textContent;
			
		}
	}
	
	Tree= document.getElementById("TableFlux");
	if(Tree.hasChildNodes())
	 	Tree.removeChild(Tree.lastChild);
	 Tree.setAttribute("hidden","false");
	//cache les infos de traduction
	document.getElementById('infosTrad').setAttribute("hidden","true");
	document.getElementById('treeDicoIeml').setAttribute("hidden","true");
	document.getElementById('contDonnee').setAttribute("hidden","true");
	//affiche le iframe
	
	if((query=="GetAllBundles")||(query=="GetAllTags")||(query=="")){
	    Tree.setAttribute("hidden","false");
		AppendResult(urlAjax+"overlay/tree.php?box=box2&ParaNom=GetOntoTree&type=flux",Tree,false);
		
	}else
	if((query=="GetAllPosts")||(query=="GetRecentPosts")||(query=="GetPosts")){
	    
		Tree= document.getElementById("TableFlux");
		Tree.setAttribute("hidden","false");
		urlparams="f=Table_Flux&tag="+tag+"&desc="+desc+"&url="+url+"&date="+date+"&note="+note;
		AppendResultPost(urlAjax+"library/php/ExeAjax.php",urlparams,Tree,false);
	    
	}
	
	pf_dessin(query_graph);
	var meter=document.getElementById('progmeter');
	meter.setAttribute("value","100");
	document.getElementById('Maj').setAttribute("hidden","true");
	
   
}

function SaveFlux(result,param){
	
	DelIiciousTreeGraph(result,param);
	Flux=result;
}

function Trad_Pars_Ieml(result, param){
	 	var trad;
        var synIeml="";
        var Ieml; 
        var FluxN="";
        var FluxM="";
        var FluxS="";
        var MultiTrad="";
        var SignlTrad="";
        var descpM="";
        var descpS="";
        var iemlTrad;var synIemlM=""; var synIemlS="";
        var in_array="true";
        var T=new Array();
        var Descp=new Array();
        var Trad=new Array();
        var Tag=new Array();
        var arrNoms=new Array();
        console.log(result);
        arrNoms = result.split(E);
        arrNom= arrNoms[0].split(P);
        if(arrNoms[1].length > 1){
        
	        Descp=arrNoms[2].split(P);
	        Trad=arrNoms[1].split(P);
	        Tag=arrNoms[3].split(P);
        }
       
       for(i=0;i<arrNom.length-1;i++){
                
                trad=TradIeml.recherchez(arrNom[i]);
                
                if(trad!=E){
                        ieml=trad.split(E);
                        iemlTrad=ieml[0].split(P);
                                if(iemlTrad.length>2){
                                in_array="false";
                                    for(j=0;j<Descp.length;j++){
                                    	if(arrNom[i]==Tag[j]){
		                                    in_array="true";
		                                    
		                                }
		                         }
		                            if(in_array=="false"){
		                            synIemlM+=TradIeml.syntaxe_ieml(ieml[0])+E;
		                            MultiTrad+=ieml[0]+E;
		                           	FluxM+=arrNom[i]+P;
		                            descpM+=ieml[1]+E;
                              
                                  }
                           
                        }else
                            if(iemlTrad.length==2){
                                synIemlS+=TradIeml.syntaxe_ieml(ieml[0]);
                                SignlTrad+=ieml[0];
                                FluxS+=arrNom[i]+P;
                                descpS+=ieml[1];

                        }
                        
                }else{
                    in_array="false";       
                    for(j=0;j<Descp.length;j++){
                        if(arrNom[i]==Tag[j]){
		                 in_array="true";
		                }    
		            }
		            if(in_array=="false"){
		            	FluxN+=arrNom[i]+P;
		            }
		                
             }
        }
       
       	var bdd = arrNoms[1].replace(/\\/g, "");
        synIemlS+=bdd;
        FluxS+=arrNoms[3];
        descpS+=arrNoms[2];

	//affiche les infos de traduction
	 document.getElementById("TableFlux").setAttribute("hidden","true");
	 document.getElementById('infosTrad').setAttribute("hidden","false");

	//affiche le tree des singles trad
	var doc = document.getElementById('contDonnee');
		doc.setAttribute("hidden","false");
	if(FluxS.length>2){
		
		var url = urlAjax+"library/php/ExeAjax.php";
		var urlparams="f=GetTreeTrad&flux="+FluxS+"&trad="+synIemlS+"&descp="+descpS+"&type=Signl_Trad&primary=true&bdd="+bdd;
		AppendResultPost(url,urlparams,document.getElementById('tpSingleTrad'),false);

	}
	//ajoute le tree des multi trad
	if(FluxM.length>2){
		url = urlAjax+"library/php/ExeAjax.php";
		console.log(synIemlM+' '+descpM);
		urlparams="f=GetTreeTrad&flux="+FluxM+"&trad="+synIemlM+"&descp="+descpM+"&type=Multi_Trad&primary=true&bdd="+bdd;
		AppendResultPost(url,urlparams,document.getElementById('tpMultiTrad'),false);
		
    }
	//ajoute le tree des no trad
	if(FluxN.length>2){
		url = urlAjax+"library/php/ExeAjax.php";
		urlparams="f=GetTreeTrad&flux="+FluxN+"&trad=&descp=&type=No_Trad&primary=false&bdd="+bdd;
		AppendResultPost(url,urlparams,document.getElementById('tpNoTrad'),false);
	}
	
  //document.getElementById("iemlCycle").setAttribute("src","testGoogleDoc.php");
  document.getElementById('infosTrad').setAttribute("hidden","false");
  document.getElementById('treeDicoIeml').setAttribute("hidden","false");
  document.getElementById('contDonnee').setAttribute("hidden","false");
	
}


function FluxGraphe(result,param){
	alert(result);
}

function Trad(id,src){
	
	var iFrame =document.getElementById(id);
    iFrame.setAttribute("src",src);
}
function AddTrad(){
	
	var libIeml=document.getElementById("lib-trad-ieml");
    var codeIeml=document.getElementById("code-trad-ieml");
    var codeFlux=document.getElementById("code-trad-flux");
	
    alert(libIeml.value);
	
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=AddTrad&libIeml="+libIeml.value+"&codeIeml="+codeIeml.value+"&codeFlux="+codeFlux.value,"","","trad-message");
	
	SetDonnee();
	
}   
function RequetteAddTrad(){
	var libflux= document.getElementById("code-trad-flux");
	var codeIeml = document.getElementById("code-trad-ieml");
	var libIeml=document.getElementById("code-trad-flux");
	var idflux = document.getElementById("id-trad-flux");
	
	//v�rification des valeurs
	if(codeIeml.value=="")
		document.getElementById("trad-message").value = "Veuillez s�lectionner une valeur pour chaque langage";
	else
		
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=AddTrad&idflux="+idflux.value+"&libflux="+libflux.value+"&codeIeml="+codeIeml.value,"","","trad-message");
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
	

}

//Supression d'une traduction

function SupTrad()
{
	//r�cup�ration des valeurs
	
	var libIeml=document.getElementById("lib-trad-ieml");
    var codeIeml=document.getElementById("code-trad-ieml");
    var codeFlux=document.getElementById("code-trad-flux");
   
    alert(libIeml.value);
	url = urlExeAjax+"?f=SupTrad&codeIeml="+codeIeml.value+"libIeml="+libIeml.value+"&codeflux="+codeFlux.value;
   
	//v�rification des valeursboxlistJ
	if(codeIeml.value=="" || codeFlux.value=="")
		document.getElementById("trad-message").value = "Veuillez s�lectionner une traduction";
	else
		AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=SupTrad&codeIeml="+codeIeml.value+"&libIeml="+libIeml.value+"&codeflux="+codeFlux.value,""," ","trad-message");
		SetDonnee();
		
}
function startSelectTab()
{ 
	var listbox=document.getElementById("boxlist");
	var cell = listbox.selectedItem.childNodes[0]; // suivant l'index de colonne que vous desirez
	var celldescp = listbox.selectedItem.childNodes[2];
	var cellF = listbox.selectedItem.childNodes[1];
	var celldescpF = listbox.selectedItem.childNodes[3];

	txtIdieml=document.getElementById("id-trad-ieml");
	txtIdieml.value=cell.getAttribute('label');
	txtIdFlux=document.getElementById("id-trad-flux");
	txtIdFlux.value=cellF.getAttribute('label');
	
	txtCode = document.getElementById("code-trad-ieml");
	txtCode.value=cell.getAttribute('label');
	txtDescp= document.getElementById("lib-trad-ieml");
	txtDescp.value = celldescp.getAttribute('label');
	txtCodeF = document.getElementById("code-trad-flux");
	txtCodeF.value=cellF.getAttribute('label');
	txtDescpF= document.getElementById("lib-trad-flux");
	txtDescpF.value = celldescpF.getAttribute('label');

}
function Select_NoTrad(id,treecol){
	
	var tree = document.getElementById(id);
  	var txtcode_flux=document.getElementById("code-trad-flux");
  	var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
  	txtcode_flux.value=selection.firstChild.lastChild.getAttribute("label");
    
   
    
}
function Select_Dictio(id,treecol1,treecol2){
	
	var tree = document.getElementById(id);
	var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
  	var txtcode_flux=document.getElementById("code-trad-flux");
  	var txtcode_ieml = document.getElementById("code-trad-ieml");
  	var txtlib_ieml=document.getElementById("lib-trad-ieml");
  	txtlib_ieml.value=tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn(treecol2));
    txtcode_ieml.value= tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn(treecol1));
}
function Select_Trad(id,treecol){
  try{
  var tree = document.getElementById(id);
  var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
     
 	  var parent=tree.contentView.getParentIndex(tree.currentIndex);
	  var parentItem=tree.contentView.getItemAtIndex(parent);
	  var txtcode_ieml = document.getElementById("code-trad-ieml");
	  var txtcode_flux=document.getElementById("code-trad-flux");
	  var txtlib_ieml=document.getElementById("lib-trad-ieml");
	  txtcode_ieml.value= selection.firstChild.lastChild.getAttribute("label");
	  txtlib_ieml.value=tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn(treecol));
	  txtcode_flux.value=parentItem.firstChild.firstChild.getAttribute("label");
  
  }
  		 
 catch(e){}
  
}
function Parser(op,type){
    var tree = document.getElementById("Signl_Trad");
    Iemlcode=tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn("treecol_Signl_Trad"));
    //prise en compte de la s�lection multiple
    Iemlcode=GetIemlTreeExp("Signl_Trad", 2, op);
	var url = urlAjax+"library/php/ExeAjax.php?f=GetGraph&code="+Iemlcode+"&type="+type;
	url = GetResult(url);
	Trad('webFrame',url);
	
}


function GetIemlTreeExp(idTree, col, op){

  try {
    var start = new Object();
	var end = new Object();
	var tree;
	var IemlToParse = "(";
	var arrIEML = [];
	var c;
	var i;
	var val, niv, maxNiv;
	
  	if (window.parent != self) 
		tree = parent.document.getElementById(idTree);
	else
		tree = document.getElementById(idTree);
	
	//pour g�rer la multis�lection
	var numRanges = tree.view.selection.getRangeCount();
	i=0;
	maxNiv=0;	
	for (var t = 0; t < numRanges; t++){
		tree.view.selection.getRangeAt(t,start,end);
		for (var v = start.value; v <= end.value; v++){
			c = tree.treeBoxObject.columns[col];
			val = tree.view.getCellText(v,c);
			
			if(val!=""){
				//enregistre l'expression
		        //et le niveau le plus haut
		        niv = GetIemlLayer(val);
		        arrIEML.push(new Array(val, niv));
		        if(niv>maxNiv)
		        	maxNiv=niv;
				i++;
			}
		}
	}
	//met � jour chaque expression suivant le niveau le plus haut
	//n�cessaire pour que l'expression soit valide pour le parser
	//if(trace)
		console.log('interface:GetIemlTreeExp:'+maxNiv);   
	
	for (var i = 0; i < arrIEML.length; i++){
		if(arrIEML[i][1]<maxNiv){
			//met � jour le layer de l'expression
			arrIEML[i][0]= SetIemlMaxLayer(arrIEML[i],maxNiv); 	
		}
		IemlToParse += arrIEML[i][0]+op;
	}
	//supprime le dernier op�rateur
	IemlToParse = IemlToParse.substring(0, IemlToParse.length-1);
	//finalise l'expression
	IemlToParse += ")";

	return IemlToParse;

  } catch(ex2){ alert("histogrammes:GetIemlTreeExp:"+ex2+" IemlToParse="+IemlToParse); }
}


function GetIemlLayer(ieml){
  try {
	//r�cup�re le dernier caract�re
	var c = ieml.substr(ieml.length-1,1);
	var niv;
	if(c==":")
		niv = 1;
	if(c==".")
		niv = 2;
	if(c=="-")
		niv = 3;
	if(c=="'")
		niv = 4;
	if(c==",")
		niv = 5;
	if(c=="_")
		niv = 6;
	//if(trace)
		console.log('interface:GetIemlLayer:niv='+niv+' c='+c);   
	
	return niv;

  } catch(ex2){ alert("interfaces:GetIemlLayer:"+ex2+" ieml="+ieml); }
}

function SetIemlMaxLayer(arrIEML,maxNiv){

  try {
	if(arrIEML[1]<1 && maxNiv>=1)
		arrIEML[0] += ":";
	if(arrIEML[1]<2 && maxNiv>=2)
		arrIEML[0] += ".";
	if(arrIEML[1]<3 && maxNiv>=3)
		arrIEML[0] += "-";
	if(arrIEML[1]<4 && maxNiv>=4)
		arrIEML[0] += "'";
	if(arrIEML[1]<5 && maxNiv>=5)
		arrIEML[0] += ",";
	if(arrIEML[1]<6 && maxNiv>=6)
		arrIEML[0] += "'";
	
	return arrIEML[0];
  } catch(ex2){ alert("interfaces:SetIemlMaxLayer:"+ex2+" ieml="+ieml); }
}
function recup_dictio(){
	var res;
	for(i=0;i<1990;i++){
		res=TradIeml.recup_dictio(i);
		arres=res.split(E);
		arres[1]=arres[1].substring(0, arres[1].length-1)
		AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=insert_ieml_onto&Iemlcode="+arres[1]+"&Iemllib="+arres[2]+"&Imelparent="+arres[0],""," ","");
	}
}

function AddPostIemlDelicios(){
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=AddPostIeml",'Afficher','')
	var meter=document.getElementById('Maj');
	document.getElementById('label_Maj').setAttribute('value','Veuillez patienter la mise a jour est en cours...');
	meter.setAttribute("hidden","false");
	
}

function SupprimeDelicious(result,prarm){
	
		var meter=document.getElementById('progmeter');
		meter.setAttribute("value","100");
	    alert(result);
		document.getElementById('Maj').setAttribute("hidden","true");
	    window.location.href = "exit.php";
}
function Afficher(result,prarm){
	
	var meter=document.getElementById('progmeter');
	meter.setAttribute("value","100");
	if(result==''){
		alert("il n y pas deS Posts recents � mettre a jour");
		document.getElementById('Maj').setAttribute("hidden","true");
	}else{
		
		message='Les Posts suivants ont ete mis a jour: ';
		alert(message+result);
		document.getElementById('Maj').setAttribute("hidden","true");
	    document.getElementById('Maj').setAttribute("hidden","true");
	}
}
function SupprimerCompteDelicious(){
    
    var meter=document.getElementById('Maj');
	meter.setAttribute("hidden","false");
	document.getElementById('label_Maj').setAttribute("value","La suppression est en cours ...");
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=Delet_Compte_Delicious",'SupprimeDelicious','');
	
}
function GoUrl(url){
	
	box=document.getElementById('TableFlux');
	box.removeChild(box.lastChild);
	ifram=document.createElement("iframe");
	ifram.setAttribute("src",url);
	ifram.setAttribute("flex","1");
	box.appendChild(ifram);
	
		
}
function CreaNoeud(){
    document.getElementById('webFrame1').setAttribute("hidden","false");
    document.getElementById('webFrame1').setAttribute("src","library/svg/iemlMenuBoussole.xul");
	document.getElementById('webFrame').setAttribute("src","library/svg/iemlBoussole.svg");
}

function SelectionCycle(){
Table=window.parent.frames['iemlCycle'].document.getElementById('')
window.parent.frames['iemlCycle'].document.getElementById('O:.B:M:.-').setAttribute('style',"background-color:green");

}
function ModifTrad(){
table=window.parent.frames('webFrame').document.getElementById('Points')
window.parent.frames['webFrame'].document.getElementById('palette_status').setAttribute('style',"background-color:green");
}

function ShowDialog(){
        
  try {
        
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        var lbl = "tutu";
        //valider le libell� 
        //http://developer.mozilla.org/fr/docs/Extraits_de_code:Dialogues_et_invites
        var prompts = Components.classes["@mozilla.org/embedcomp/prompt-service;1"]
                                .getService(Components.interfaces.nsIPromptService);
        var input = {value: lbl};
        var check = {value: false};
        result = prompts.prompt(window, "Validation crit�re", "Valider ou modifier la r�gle", input, null, check);
        // input.value contient la cha�ne de caract�res saisie par l'utilisateur
        // check.value indique l'�tat de la case � cocher
        // result - contient true si l'utilisateur a cliqu� sur OK      
        if(!result)
                return;
        lbl = input.value;
        //lbl = Utf8.encode(lbl);
        alert(lbl);
 
  } catch(ex2){console.log("Interface:ShowDialog:"+ex2);}
 }       
        

