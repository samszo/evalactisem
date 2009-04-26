    
    //tag pour ne pas recharger le svg pendant qu'on initialise les param�tres des outils
    var SetParamOutil;
      
    function GrossiMaigriTag(evt){
		//augmente une fois la taille du texte pour le voir plus facilement
		var tgt=evt.target;
		var tgtText = tgt.nextSibling.nextSibling; 
		if(tgt.getAttribute("grossi")=="oui"){
		   	tgt.setAttribute("r",tgt.getAttribute("r")/10);
		   	tgt.setAttribute("grossi","non");
		   	tgtText.setAttribute("style","fill:black;font-size:1000px;");
		}else{
		   	tgt.setAttribute("r",tgt.getAttribute("r")*10);
		   	tgt.setAttribute("grossi","oui");
		   	//var style = tgtText.setAttribute("style").split(";");
		   	//var font = style[1].split(";");
		   	tgtText.setAttribute("style","fill:black;font-size:10000px;");
		}
    }
    
    function MaigriTag(evt){
		//augmente la taille du texte pour le voir plus facilement
	   	tgtEC = evt.target;
	   	tgtEC.setAttribute("r",(tgtEC.getAttribute("r")/10));
    }

	function onScroll(event) {
		if (event.attrName == "curpos") {
			var sc = event.target;
			document.getElementById(sc.getAttribute("idLbl")).value = sc.getAttribute("curpos");
		}
	}

	function GetTreeDeliciousNetwork(){
	 try {
			//pour les traduction faites
			var url = urlExeAjax+"?f=GetTreeDeliciousNetwork";
			AppendResult(url,document.getElementById('DeliciousNetwork'),false);
				
	  } catch(ex2){ alert("tagcloud:GetTreeDeliciousNetwork:"+ex2); }
	}
	
	function SelectNetwork(id,GetParam){
	  try{
	    var login = GetTreeValSelect(id,0);
		var url = urlTagCloud+"?login="+login;
		//r�cup�re le choix d'affichage
		var ShowAll = document.getElementById("ShowAll").value;
		url += "&ShowAll="+ShowAll;
		//r�cup�re le type de repr�sentation
		var TC = document.getElementById("choixTC").value;
		url += "&TC="+TC;
		var ajout = document.getElementById("choixAjout").value;
		if(ajout==-1)ajout=false;
		//r�cup�re la langue
		var langue = document.getElementById("choixLangue").value;
		url += "&lang="+langue;
		//r�cup�re le temps vide
		var TempsVide = document.getElementById("TempsVide").checked;
		url += "&TempsVide="+TempsVide;

		if(GetParam!=-1){
			  //calcul les arguments
			  var NbDeb = document.getElementById("scrollTagIntMin").getAttribute("curpos");
			  if(NbDeb>0)
				  url += "&NbDeb="+NbDeb;
			  var NbFin = document.getElementById("scrollTagIntMax").getAttribute("curpos");
			  if(NbFin>0)
				  url += "&NbFin="+NbFin;
			  var DateDeb = document.getElementById("dpTagDeb").value;
			  if(DateDeb)
				  url += "&DateDeb="+DateDeb;
			  var DateFin = document.getElementById("dpTagFin").value;
			  if(DateFin)
				  url += "&DateFin="+DateFin;
		}
		
		//charge le svg  
		AppendSVG(url,document.getElementById("tagcloud"),ajout);
		
		//met � jour les param�tres des outils
	  	var svg = document.getElementById("SVGglobal");
	  	InitOutilsParams(svg.getAttribute("TagNbMin"),svg.getAttribute("TagNbMax"),svg.getAttribute("TagDateDeb"),svg.getAttribute("TagDateFin"));
		  
		  
	  }catch(e){
	  console.log("tagcloud:SelectNetwork:"+e+""); 
	 }
	}
	
	function InitOutilsParams(TagIntMin,TagIntMax,TagDeb,TagFin){
	  try{
		  SetParamOutil=true;
		  document.getElementById("scrollTagIntMin").setAttribute("curpos",TagIntMin);
		  document.getElementById("scrollTagIntMin").setAttribute("maxpos",TagIntMax);
		  document.getElementById("scrollTagIntMax").setAttribute("curpos",TagIntMax);
		  document.getElementById("scrollTagIntMax").setAttribute("maxpos",TagIntMax);
		  document.getElementById("dpTagDeb").value = TagDeb;
		  document.getElementById("dpTagFin").value = TagFin;
	  }catch(e){
	  console.log("tagcloud:InitOutilsParams:"+e+""); 
	 }
	  SetParamOutil=false;
	}
	