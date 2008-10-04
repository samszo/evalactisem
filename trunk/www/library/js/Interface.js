   
var E="*";
var P=";";
var S=":";

function OuvreLienOnglet(lien){
	window.open(lien);
}


function ChargeCycle(key){
	//vérifie que l'onglet n'est pas déjà rempli
	document.getElementById('keyGrid').value=key;
	if(document.getElementById('iemlCycle_'+key).hasChildNodes())
		return;
    document.getElementById('label_Maj').setAttribute('value','Veuillez patienter, le chargement du cycle est en cours...');
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

    document.getElementById('label_Maj').setAttribute('value','Veuillez patienter la récupération du flux est en cours...');
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
		
	//affiche directement les outils de traduction
	/*
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
	*/

	SetDonnee();	
	
	
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
        var CoucheM="";
        var CoucheS="";
        //traduction sauvegarder dans la BDD
        arrNoms = result.split(E);
       
        
        //flux
        arrNom= arrNoms[0].split(P);
       
      
        if(arrNoms[1].length > 1){
        
	        Descp=arrNoms[2].split(P);
	        Trad=arrNoms[1].split(P);
	        Tag=arrNoms[3].split(P);
	        Couche=arrNoms[4].split(P);
	        Descp=arrNoms[2].split(P);
	        Trad=arrNoms[1].split(P);
	        Tag=arrNoms[3].split(P);
        }
        
       
       for(i=0;i<arrNom.length-1;i++){
                
                //traduire les tags
                trad=TradIeml.recherchez(arrNom[i]);
                
                if(trace)
			        console.log("interface:Trad_Pars_Ieml:trad="+trad);
                //calcul des flux multi-trad
                if(trad!="**"){
                        ieml=trad.split(E);
                        iemlTrad=ieml[0].split(P);
                        //iemlCouche=ieml[2].split(S);
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
		                            CoucheM+=ieml[2]+E;
		                            //console.log("Interfce:Trad_Pars_Ieml:CoucheM="+CoucheM);
                              
                               }
                           
                        }else{
                            //Signl trad
                            if(iemlTrad.length==2){
                                synIemlS+=TradIeml.syntaxe_ieml(ieml[0]);
                                SignlTrad+=ieml[0];
                                FluxS+=arrNom[i]+P;
                                descpS+=ieml[1];
                                CoucheS+=ieml[2];
                                //console.log("Interfce:Trad_Pars_Ieml:CoucheS="+CoucheS);
                                
							}
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
        //flux singles trad trouvées + trad manuels: il faut pas les ajoutées directement
        synIemlS+=bdd;
        FluxS+=arrNoms[3];
        descpS+=arrNoms[2];
        CoucheS+=arrNoms[4];
        
	//affiche les infos de traduction
	 document.getElementById("TableFlux").setAttribute("hidden","true");
	 document.getElementById('infosTrad').setAttribute("hidden","false");

	//affiche le tree des singles trad
	var doc = document.getElementById('contDonnee');
		doc.setAttribute("hidden","false");
		
	if(trace){
    	console.log("interface:Trad_Pars_Ieml:FluxS="+FluxS+" : "+descpS );
    	console.log("interface:Trad_Pars_Ieml:FluxM="+FluxM +" : "+descpM);
    	console.log("interface:Trad_Pars_Ieml:FluxN="+FluxN);
	    console.log("interface:Trad_Pars_Ieml:FluxN="+FluxN);
    }
	if(FluxS.length>2){
		var url = urlAjax+"library/php/ExeAjax.php";
		var urlparams="f=GetTreeTrad&flux="+FluxS+"&trad="+synIemlS+"&descp="+descpS+"&type=Signl_Trad&primary=true&bdd="+bdd+"&couche="+CoucheS;
		AppendResultPost(url,urlparams,document.getElementById('tpSingleTrad'),false);
	}
	//ajoute le tree des multi trad
	if(FluxM.length>2){
		url = urlAjax+"library/php/ExeAjax.php";
		if(trace)
			console.log(synIemlM+' '+descpM);
		urlparams="f=GetTreeTrad&flux="+FluxM+"&trad="+synIemlM+"&descp="+descpM+"&type=Multi_Trad&primary=true&bdd="+bdd+"&couche="+CoucheM;
		AppendResultPost(url,urlparams,document.getElementById('tpMultiTrad'),false);
		
    }
	//ajoute le tree des no trad
	if(FluxN.length>2){
		url = urlAjax+"library/php/ExeAjax.php";
		urlparams="f=GetTreeTrad&flux="+FluxN+"&trad=&descp=&type=No_Trad&primary=false&bdd="+bdd+"&couche=";
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
    var message=document.getElementById("trad-message");
    
	if(codeIeml.value==""){
		alert("Veuillez sélectionner une expression IEML");
		return
	}	
	if(codeFlux.value==""){
		alert("Veuillez sélectionner un tag del.icio.us");
		return
	}	
	
	message.value = GetResult(urlAjax+"library/php/ExeAjax.php?f=AddTrad&libIeml="+libIeml.value+"&codeIeml="+codeIeml.value+"&codeFlux="+codeFlux.value);
	
	SetDonnee();
	
}   
function RequetteAddTrad(){
	var libflux= document.getElementById("code-trad-flux");
	var codeIeml = document.getElementById("code-trad-ieml");
	var libIeml=document.getElementById("code-trad-flux");
	var idflux = document.getElementById("id-trad-flux");
	
	//vérification des valeurs
	if(codeIeml.value=="")
		document.getElementById("trad-message").value = "Veuillez sélectionner une valeur pour chaque langage";
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
	//récupération des valeurs
	
	var libIeml=document.getElementById("lib-trad-ieml");
    var codeIeml=document.getElementById("code-trad-ieml");
    var codeFlux=document.getElementById("code-trad-flux");
   
	if(codeIeml.value==""){
		alert("Veuillez sélectionner une expression IEML");
		return
	}	
	if(codeFlux.value==""){
		alert("Veuillez sélectionner un tag del.icio.us");
		return
	}	

	var message=document.getElementById("trad-message");
	message.value = GetResult(urlAjax+"library/php/ExeAjax.php?f=SupTrad&codeIeml="+codeIeml.value+"&libIeml="+libIeml.value+"&codeflux="+codeFlux.value);

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
	var txtcode_ieml = document.getElementById("code-trad-ieml");
	var txtlib_ieml=document.getElementById("lib-trad-ieml");
  	var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
  	IdTreecell=selection.getAttribute('id');
  	txtcode_flux.value=document.getElementById("T"+IdTreecell).getAttribute('label');
    txtcode_ieml.value = "";
    txtlib_ieml.value = "";
   
    
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
function Select_Trad(id,treecolTag,treecolTrad,treecolIeml,type){
  try{
	  var tree = document.getElementById(id);
      var tag = GetTreeValSelect(id,treecolTag);
      var iemlCode = GetTreeValSelect(id,treecolIeml);
	  var txtcode_ieml = document.getElementById("code-trad-ieml");
	  var txtcode_flux=document.getElementById("code-trad-flux");
	  var txtlib_ieml=document.getElementById("lib-trad-ieml");

     if(tag=="" && iemlCode==""){
	 	txtcode_flux.value= "";
		txtlib_ieml.value="";
		txtcode_ieml.value="";
     	return;
     }
	  var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
     
     if(tag!=""){
	 	txtcode_flux.value= tag;
		txtlib_ieml.value="";
		txtcode_ieml.value="";
     }
     
     if(iemlCode!="" ){
       
		var parent=tree.contentView.getParentIndex(tree.currentIndex);
		var GrandParent=tree.contentView.getParentIndex(parent);
		
		txtcode_ieml.value= iemlCode;
		txtlib_ieml.value=GetTreeValSelect(id,treecolTrad);
		if(GrandParent==0 || GrandParent==-1 )
			txtcode_flux.value=tree.view.getCellText(parent,tree.treeBoxObject.columns[treecolTag]);
		else
			txtcode_flux.value=tree.view.getCellText(GrandParent,tree.treeBoxObject.columns[treecolTag]);
     }
     	  
  }
  		 
 catch(e){
  console.log("interface:Select_Trad:"+e+""); 
 }
  
}
function Parser(op,type){
    var tree = document.getElementById("Signl_Trad");
    //prise en compte de la sélection multiple
    Iemlcode=GetIemlTreeExp("Signl_Trad", 3, op);
    if(Iemlcode==")"){
    	alert("Veuillez sélectionner un ou plusieurs Tags traduits");
    	return;
	}    
	var url = urlAjax+"library/php/ExeAjax.php?f=GetGraph&code="+Iemlcode+"&type="+type;
	url = GetResult(url);

	GoUrl(url,'bIemlStat');
	
	var tabStatIeml = document.getElementById('tabStatIeml')
	document.getElementById('tbIframe').selectedTab=tabStatIeml;	
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
	
	//pour gérer la multisélection
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
	//met à jour chaque expression suivant le niveau le plus haut
	//nécessaire pour que l'expression soit valide pour le parser
	if(trace)
		console.log('interface:GetIemlTreeExp:'+maxNiv);   
	
	for (var i = 0; i < arrIEML.length; i++){
		if(arrIEML[i][1]<maxNiv){
			//met à jour le layer de l'expression
			arrIEML[i][0]= SetIemlMaxLayer(arrIEML[i],maxNiv); 	
		}
		IemlToParse += arrIEML[i][0]+op;
	}
	//supprime le dernier opérateur
	IemlToParse = IemlToParse.substring(0, IemlToParse.length-1);
	//finalise l'expression
	IemlToParse += ")";

	return IemlToParse;

  } catch(ex2){ alert("histogrammes:GetIemlTreeExp:"+ex2+" IemlToParse="+IemlToParse); }
}


function GetIemlLayer(ieml){
  try {
	//récupère le dernier caractère
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
	if(trace)
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
		alert("il n'y pas de Posts recents à mettre a jour");
		document.getElementById('Maj').setAttribute("hidden","true");
	}else{
		message='Les Posts suivants ont ete mis a jour: ';
		alert(message+result);
		document.getElementById('Maj').setAttribute("hidden","true");
	}
}
function SupprimerCompteDelicious(){
    
    var meter=document.getElementById('Maj');
	meter.setAttribute("hidden","false");
	document.getElementById('label_Maj').setAttribute("value","La suppression est en cours ...");
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=Delet_Compte_Delicious",'SupprimeDelicious','');
	
}
function GoUrl(url,idBox){
	
	box=document.getElementById(idBox);
	box.removeChild(box.firstChild);
	ifram=document.createElement("iframe");
	ifram.setAttribute("src",url);
	ifram.setAttribute("flex","1");
	box.appendChild(ifram);
	
		
}
function ShowBoussole(){
    document.getElementById('webFrame1').setAttribute("hidden","false");
	document.getElementById('webFrame').setAttribute("hidden","true");
}

function ShowBookmark(compte){
    document.getElementById('webFrame1').setAttribute("hidden","true");
	document.getElementById('webFrame').setAttribute("hidden","false");
	document.getElementById('webFrame').setAttribute("src","http://del.icio.us/"+compte);
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
        //valider le libellé 
        //http://developer.mozilla.org/fr/docs/Extraits_de_code:Dialogues_et_invites
        var prompts = Components.classes["@mozilla.org/embedcomp/prompt-service;1"]
                                .getService(Components.interfaces.nsIPromptService);
        var input = {value: lbl};
        var check = {value: false};
        result = prompts.prompt(window, "Validation critère", "Valider ou modifier la règle", input, null, check);
        // input.value contient la chaîne de caractères saisie par l'utilisateur
        // check.value indique l'état de la case à cocher
        // result - contient true si l'utilisateur a cliqué sur OK      
        if(!result)
                return;
        lbl = input.value;
        //lbl = Utf8.encode(lbl);
        alert(lbl);
 
  } catch(ex2){alert("Interface:ShowDialog:"+ex2);}
  
 }       
        

