var LastAncreSelect=-1;
var LastPageSelect=-1;
//var urlExeAjax = "http://localhost/projects/parImpExp/library/php/ExeAjax.php";
var urlExeAjax = "http://localhost/archipelago/parImpExp/library/php/ExeAjax.php";


function AddParamPages(niv){
	
  try {
	
	//création des paramètres du niveau
	doc = parent.document.getElementById('TabSelect');

	var url = urlExeAjax+"?f=GetParamNiveau&niv="+niv;
	AppendResult(url,doc);
	
 	//masque les fenêtre select et web
  	if (window.parent != self) {
		parent.document.getElementById('TabWeb').hidden = "true";
		parent.document.getElementById('TabWebSplit').hidden = "true";
  	}else{
		document.getElementById('TabWeb').hidden = "true";
		document.getElementById('TabWebSplit').hidden = "true";
  	}
 	
  } catch(ex2){alert("svgAgentSite:AddParamPages:"+ex2+" url="+url);}
	
}

function AddParamBloc(idBloc,idPage){
	
  try {
	
	//création des paramètres du niveau
	doc = parent.document.getElementById('TabSelect');

	var url = urlExeAjax+"?f=GetParamBlocs&idBloc="+idBloc+"&idPage="+idPage;
	AppendResult(url,doc);
	
 	//masque les fenêtre select et web
  	if (window.parent != self) {
		parent.document.getElementById('TabWeb').hidden = "true";
		parent.document.getElementById('TabWebSplit').hidden = "true";
  	}else{
		document.getElementById('TabWeb').hidden = "true";
		document.getElementById('TabWebSplit').hidden = "true";
  	}
 	
  } catch(ex2){alert("svgAgentSite:AddParamPages:"+ex2+" url="+url);}
	
}

function VoirSelectPage(id){
	
  try {

	var doc;
		
  	if (window.parent != self) {
		doc = parent.document.getElementById('TabPage');
		docASelect = document.getElementById("SVGpagerect_"+id);		
		docDSelect = document.getElementById(LastPageSelect);		
	}else{
		doc = document.getElementById('TabPage');
		docSVG = frames["SVGFrame"].document.getElementById("SVGpagerect_"+id);		
		docDSelect = frames["SVGFrame"].document.getElementById(LastPageSelect);		
	}
	
	//vide le conteneur
	while(doc.hasChildNodes())
		doc.removeChild(doc.firstChild);

	var url = urlExeAjax+"?f=GetFormPage&id="+id;

	AppendResult(url,doc);


	//change le style svg de la page selectionner et déselectionnée
  	var style = "stroke:green;stroke-width:6;fill:blue;";
	docASelect.setAttribute("style",style);
	if(LastPageSelect!=-1){
	  	style = "stroke:yellow;stroke-width:2;fill:red;";
		docDSelect.setAttribute("style",style);
	}
	LastPageSelect="SVGpagerect_"+id;
	   
   } catch(ex2){alert("svgAgentSite:VoiSelectPage:"+ex2+" url="+url);}
	
}



function AncreSelection(id){

  try {
  	
  	if(LastAncreSelect!=-1)
		//désélectionne l'ancre précédente
		AncreDeSelection(LastAncreSelect);
  	
  	
  	//"SVGarc_site_1_page_1;
  	//"SVGarc_page_1_page_1;
  	var arrId = id.split("_");
  	//alert(arrId);
  	var style = "stroke:green;stroke-width:6;fill:blue;";
	frames["SVGFrame"].document.getElementById(id).setAttribute("style",style);
	var idSvg = "SVGLien_"+arrId[1]+"_"+arrId[2]+"_page_"+arrId[4];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	var idSvg = "SVGLienTronc_"+arrId[1]+"_"+arrId[2]+"_page_"+arrId[4];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGFinTronc_"+arrId[1]+"_"+arrId[2];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGDebTronc_"+arrId[1]+"_"+arrId[2];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGpagerect_"+arrId[4];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGpageentree_"+arrId[4];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);

   } catch(ex2){alert("svgAgentSite:AncreSelection:"+ex2+" id="+id+" idSvg="+idSvg );}

  	LastAncreSelect = id;

}


function AncreDeSelection(id){

  try {
  	
  	if(!frames["SVGFrame"].document.getElementById(id))
  		return;
  		
  	//"SVGarc_site_1_page_1;
  	//"SVGarc_page_1_page_1;
  	var arrId = id.split("_");
  	var style = "stroke:yellow;stroke-width:2;fill:red;";
  	//alert(arrId);
	frames["SVGFrame"].document.getElementById(id).setAttribute("style",style);
	var idSvg = "SVGLien_"+arrId[1]+"_"+arrId[2]+"_page_"+arrId[4];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGLienTronc_"+arrId[1]+"_"+arrId[2]+"_page_"+arrId[4];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGFinTronc_"+arrId[1]+"_"+arrId[2];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGDebTronc_"+arrId[1]+"_"+arrId[2];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGpagerect_"+arrId[4];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);
	idSvg = "SVGpageentree_"+arrId[4];
	frames["SVGFrame"].document.getElementById(idSvg).setAttribute("style",style);


   } catch(ex2){alert("svgAgentSite:AncreSelection:"+ex2+" id="+id+" idSvg="+idSvg );}

}

