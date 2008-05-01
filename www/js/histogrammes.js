urlAjax="http://localhost/evalactisem/";
E="*";
P=";";
function show_tooltip(evt)
{
        var matrix = evt.target.ownerDocument.getElementById("root").getScreenCTM()
        var  decale_x = matrix.e 
        var  decale_y = matrix.f
        var values = res.split(P)
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

function SetDonnee(){
	
	AjaxRequest(urlAjax+"library/ExeAjax.php?f=Recup_onto_trad" ,'Trad_Pars_Ieml','');
}
function RecupDeliciousFlux(){
	
		query_flux=document.getElementById("requette").selectedItem.value;
		query_graph=document.getElementById("type").selectedItem.value;
	    
	
	if((query_flux=="")){
		AjaxRequest(urlAjax+"library/RecupFlux.php?requette=GetAllTags"+"&req=GetAllTags",'DelIiciousTreeGraph','');
	}
	if(query_graph=""){
		AjaxRequest(urlAjax+"library/RecupFlux.php?req=GetAllTags"+"&requette=GetAllTags",'DelIiciousTreeGraph','');
		
		
	}
	if((query_flux=="GetAllTags")||(query_flux=="GetAllBundles")||(query_flux=="GetAllPosts")){
		AjaxRequest(urlAjax+"library/RecupFlux.php?requette="+query_flux+"&req="+document.getElementById("type").selectedItem.value ,'DelIiciousTreeGraph','');
    }else
    	if(query_flux=="GetRecentPosts"){
    		tag=document.getElementById("id-tag").value;
    		count=document.getElementById("id-count").value;
    		AjaxRequest(urlAjax+"library/RecupFlux.php?requette="+query_flux+"&tag="+tag+"&count="+count+"&req="+document.getElementById("type").selectedItem.value ,'DelIiciousTreeGraph','');
    }else
        if(query_flux=="GetPosts"){
    		tag=document.getElementById("id-tag").value;
    		url=document.getElementById("id-url").value;
    		date=document.getElementById("id-date").value;
    		AjaxRequest(urlAjax+"library/RecupFlux.php?requette="+query_flux+"&tag="+tag+"&url="+url+"&date="+date+"&req="+document.getElementById("type").selectedItem.value,'DelIiciousTreeGraph','');
    }


}

function pf_dessin(noms, donnees)
{
	lien='library/stats.php?large='+escape("400");
	lien=lien+'&haut='+escape('300');
	lien=lien+'&titre='+escape(document.getElementById("titre").value);
	lien=lien+'&donnees='+escape(donnees);
	lien=lien+'&noms='+escape(noms);
	lien=lien+'&type=pie';
	lien=lien+'&col1='+escape('#FFCC33');
	lien=lien+'&col2='+escape('#33FFFF');
	lien=lien+'&col3='+escape('#000066');
	lien=lien+'&col4='+escape('#000000');
	document.getElementById("webFrame").setAttribute("src",lien);
    //alert(lien);
}


function DelIiciousTreeGraph(result,param){
	
	SaveFlux(result,param);

	query=document.getElementById("requette").selectedItem.value;
	var parser = new DOMParser();
	xmlFlux = parser.parseFromString(result, "text/xml");
    //alert(result);
	iterSec = xmlFlux.evaluate("/marque", xmlFlux, null, XPathResult.ANY_TYPE, null );
  	
  	nSec = iterSec.iterateNext();
  	if(!nSec)
  		return;
	for (var j = 0; j < nSec.childNodes.length; j++) {
		if(nSec.childNodes[j].tagName=="nom"){
			nom = nSec.childNodes[j].textContent;
		    
		    tag=nSec.childNodes[j].textContent;
		    
		}
		if(nSec.childNodes[j].tagName=="nombre"){
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
		if(nSec.childNodes[j].tagName=="noms"){
			//document.getElementById("noms").value = nSec.childNodes[j].textContent;
		    abscises=nSec.childNodes[j].textContent;
			//alert(abscises);
		}
		if(nSec.childNodes[j].tagName=="donnees"){
			ordonnees = nSec.childNodes[j].textContent;	
		    res=nSec.childNodes[j].textContent;	
		    //alert(ordonnees);
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

function SaveFlux(result,param){
	
	//alert(result);
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
        var  Tag=new Array();
        
    var parser = new DOMParser();
        FluxTag = parser.parseFromString(Flux, "text/xml");
        iterSecTag = FluxTag.evaluate("/marque",FluxTag, null, XPathResult.ANY_TYPE, null );
        nSecTag = iterSecTag.iterateNext();
       
        if(!nSec)
                return;
        for (var j = 0; j < nSec.childNodes.length; j++) {
                if(nSec.childNodes[j].tagName=="nom"){
                        arrNoms= nSec.childNodes[j].textContent;
                        
                }
                
        }
                
       
        arrNom = arrNoms.split(P);
        if(result!=1){
        T=result.split(E);
        Descp=T[1].split(P);
        Trad=T[0].split(P);
        Tag=T[2].split(P);
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
                           // alert("il existe plusieurs traduction"+nouv_syn);
                        }else
                            if(iemlTrad.length==2){
                                synIemlS+=TradIeml.syntaxe_ieml(ieml[0]);
                                SignlTrad+=ieml[0];
                                FluxS+=arrNom[i]+P;
                                descpS+=ieml[1];
                                
                                //alert("il existe qu 'une seul traduction: "+FluxS);
                                
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
       
        synIemlS+=T[0];
        FluxS+=T[2];
        descpS+=T[1];
      
    
    

	
    //bookmark='<bookmark id="login"><posts><post id="post_1"><url>www.delicious.dz</url><Tags><tag>ieml</tag><tag>ontologie</tag></Tags></post></posts></bookmark>';

	//var url = "NewTraduction.php?FluxM="+FluxM+"&MultiTrad="+synIemlM+"&descpM="+descpM+"&FluxS="+FluxS+"&SignlTrad="+synIemlS+"&descpS="+descpS+"&FluxN="+FluxN;

	//affiche les infos de traduction
	document.getElementById('infosTrad').setAttribute("hidden","false");
	//affiche le iframe
	document.getElementById('treeReq').setAttribute("hidden","true");
	
	//affiche le tree des singles trad
	var doc = document.getElementById('contDonnee');
		doc.setAttribute("hidden","false");
	if(FluxS.length>2){
		
		var url = urlAjax+"library/ExeAjax.php?f=GetTreeTrad&flux="+FluxS+"&trad="+synIemlS+"&descp="+descpS+"&type=Signl_Trad&primary=true&bdd="+T[0];
		AppendResult(url,doc,false);
	    
	}
	//ajoute le tree des multi trad
	if(FluxM.length>2){
		url = urlAjax+"library/ExeAjax.php?f=GetTreeTrad&flux="+FluxM+"&trad="+synIemlM+"&descp="+descpM+"&type=Multi_Trad&primary=true&bdd="+T[0];
		AppendResult(url,doc,true);
    }
	//ajoute le tree des no trad
	if(FluxN.length>2){
		url = urlAjax+"library/ExeAjax.php?f=GetTreeTrad&flux="+FluxN+"&trad=&descp=&type=No_Trad&primary=false&bdd="+T[0];
		AppendResult(url,doc,true);
	}
	//frame=document.getElementById('treeReq');
	//frame.setAttribute("src",url);
	//AjaxRequest(urlAjax+"overlay/tabletrad.php?FluxM="+FluxM+"&MultiTrad="+synIemlM+"&descpM="+descpM+"&FluxS="+FluxS+"&SignlTrad="+synIemlS+"&descpS="+descpS+"&FluxN="+FluxN);
    //bookmark='<bookmark id="login"><posts><post id="post_1"><url>www.delicious.dz</url><Tags><tag>ieml</tag><tag>ontologie</tag></Tags></post></posts></bookmark>';

    //AjaxRequest(urlAjax+"library/ExeAjax.php?f=GraphGet&bookmark="+bookmark,'FluxGraphe');
    

	//frame=document.getElementById(dst);
	//frame.setAttribute("src",url);
}


function FluxGraphe(result,param){
	alert(result);
}

function Trad(id,src){
	
	var iFrame =document.getElementById(id);
    iFrame.setAttribute("src",src);

	/*
	var box =document.getElementById(id);
	
	childbox=box.firstChild;
	
	while(childbox){
		childbox=box.firstChild;
		box.removeChild(childbox);
		childbox=box.firstChild;
    }
    
    Tradframe=document.createElement("iframe");
    Tradframe.setAttribute("flex",1);
    Tradframe.setAttribute("src",src);
    Tradbox=document.createElement("box");
    Tradbox.setAttribute("flex",1);
    Tradbox.appendChild(Tradframe);
    box.appendChild(Tradbox);
    */
    
}


function AddTradDictio(result,param){
        if(result=="false"){
		aTrad=document.getElementById("code-trad-flux").value;
		rTrad=TradIeml.recherchez(aTrad);
		if(rTrad=="*"){
			document.getElementById("trad-message").value="Il n'exite pas une  traduction qui correspond a cet mot veuillez une traduire a partir de la table ieml "
		}else {
	 		
	 		Trad=rTrad.split(e);
	    	CarIeml=Trad[0].split(P);
	 		DiscIeml=Trad[1].split(P);
	 		if(CarIeml.length >2){
	 		alert("il existe plusieurs possibilités veuillez choisir une");
	 		ChoixTrad(CarIeml,DiscIeml);
	 		}else
	 		if(CarIeml.length ==2){
	 			var codeIeml = document.getElementById("code-trad-ieml");
				//var libIeml=document.getElementById("lib-trad-ieml");
                RequetteAddTrad();
	 		}
	}
	
	}else{
            aTrad=document.getElementById("code-trad-flux").value;
		    rTrad=TradIeml.recherchez(aTrad);
			Trad=rTrad.split(E);
	    	CarIeml=Trad[0].split(P);
	 		DiscIeml=Trad[1].split(P);
	 		if(CarIeml.length >2){
	 		alert("il existe plusieurs possibilités veuillez choisir une");
	 		ChoixTrad(CarIeml,DiscIeml);
	 		}else
	 		if(CarIeml.length ==2){
	 			var codeIeml = document.getElementById("code-trad-ieml");
                RequetteAddTrad();
	 		}

	}
}

function AddTrad(){
	var libIeml=document.getElementById("lib-trad-ieml");
    var codeIeml=document.getElementById("code-trad-ieml");
    var codeFlux=document.getElementById("code-trad-flux");
	alert(libIeml.value);
	AjaxRequest(urlAjax+"library/ExeAjax.php?f=AddTrad&libIeml="+libIeml.value+"&codeIeml="+codeIeml.value+"&codeFlux="+codeFlux.value,"","","trad-message");
	SetDonnee();
}   




//Creation de la table des choix 

function ChoixTrad(CarIeml,DiscIeml){
	var box =document.getElementById("box");
	if(box.hasChildNodes()){
		dernier=box.lastChild;
		box.removeChild(dernier);
	}
		boxTrad=document.createElement("listbox");
		boxTrad.setAttribute("id","Tradbox");
		boxTrad.setAttribute("onclick","StartSelecTrad();RequetteAddTrad()");
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

function RequetteAddTrad(){
	var libflux= document.getElementById("code-trad-flux");
	var codeIeml = document.getElementById("code-trad-ieml");
	var libIeml=document.getElementById("code-trad-flux");
	var idflux = document.getElementById("id-trad-flux");
	
	//vérification des valeurs
	if(codeIeml.value=="")
		document.getElementById("trad-message").value = "Veuillez sélectionner une valeur pour chaque langage";
	else
		
	AjaxRequest(urlAjax+"library/ExeAjax.php?f=AddTrad&idflux="+idflux.value+"&libflux="+libflux.value+"&codeIeml="+codeIeml.value,"","","trad-message");
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
   
   alert(codeIeml.value);
	url = urlExeAjax+"?f=SupTrad&codeIeml="+codeIeml.value+"libIeml="+libIeml.value+"&codeflux="+codeFlux.value;
   
	//vérification des valeursboxlistJ
	if(codeIeml.value=="" || codeFlux.value=="")
		document.getElementById("trad-message").value = "Veuillez sélectionner une traduction";
	else
		AjaxRequest(urlAjax+"library/ExeAjax.php?f=SupTrad&codeIeml="+codeIeml.value+"&libIeml="+libIeml.value+"&codeflux="+codeFlux.value,""," ","trad-message");
		SetDonnee();
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
function Select_NoTrad(id){
	var tree = document.getElementById(id);
  	var txtcode_flux=document.getElementById("code-trad-flux");
  	var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
  	txtcode_flux.value=selection.firstChild.firstChild.getAttribute("label");
    var txtlib_ieml=document.getElementById("lib-trad-ieml");
    txtlib_ieml.value=tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn("treecol_descp"));
    
}
function Select_Trad(id){
  try{
  var tree = document.getElementById(id);
  var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
  
 	  var parent=tree.contentView.getParentIndex(tree.currentIndex);
	  var parentItem=tree.contentView.getItemAtIndex(parent);
	  var txtcode_ieml = document.getElementById("code-trad-ieml");
	  var txtcode_flux=document.getElementById("code-trad-flux");
	  var txtlib_ieml=document.getElementById("lib-trad-ieml");
	  txtcode_ieml.value= selection.firstChild.lastChild.getAttribute("label");
	  txtlib_ieml.value=tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn("treecol_descp"));
	  txtcode_flux.value=parentItem.firstChild.firstChild.getAttribute("label");
	  
  
  }
  		 
 catch(e){}
  
}
function Parser(){
    var tree = document.getElementById("Signl_Trad");
    Iemlcode=tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn("treecol_Signl_Trad"));
    //prise en compte de la sélection multiple
    Iemlcode=GetIemlTreeExp("Signl_Trad", 2);
	//var url = urlAjax+"library/ExeAjax.php?f=Parse&code="+Iemlcode;
	//AjaxRequest(url,"Afficher"," ","");
	var url = urlAjax+"library/ExeAjax.php?f=GetGraph&code="+Iemlcode;
	url = GetResult(url);
	Trad('webFrame',url)
	
}


function GetIemlTreeExp(idTree, col){

  try {

	var tree;
	var cell = "(";
	var c;
	var i;
	var val;
	
  	if (window.parent != self) 
		tree = parent.document.getElementById(idTree);
	else
		tree = document.getElementById(idTree);
	
	//pour gérer la multisélection
	var numRanges = tree.view.selection.getRangeCount();
	for (var t = 0; t < numRanges; t++){
		tree.view.selection.getRangeAt(t,start,end);
		i=0;	
		for (var v = start.value; v <= end.value; v++){
			c = tree.treeBoxObject.columns[col];
			val = tree.view.getCellText(v,c);
			if(val!=""){
				cell += val;
				if(i==2){
					//ajoute une virgule et un opérateur toute les 3 expressions
					cell += ",|"  
					i=-1;
				}
				i++;
				alert(cell);
			}
		}
	}
	//vérifie si la dernière virgule est bien mise
	if(i==0)
		//supprime le dernier opérateur
		cell = cell.substring(0, cell.length-1)
	else  	
		cell += ","
	alert(cell);
	//finalise l'expression
	cell += ")";
	alert(cell);
	return cell;
  } catch(ex2){ alert("histogrammes:GetIemlTreeExp:"+ex2+" cell="+cell); }
}


function Afficher(result,prarm){
	alert(result);
}
