      
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
	
	function SelectNetwork(id,colLogin){
	  try{
	      var login = GetTreeValSelect(id,colLogin);
		  var url = urlTagCloud+"?login="+login+"&ShowAll=1";
		  //calcul les argument
		  var NbDeb = document.getElementById("scrollTagIntMin").getAttribute("curpos");
		  if(NbDeb>0)
			  url += "&NbDeb="+NbDeb;
		  var NbFin = document.getElementById("scrollTagIntMax").getAttribute("curpos");
		  if(NbFin>0 && NbFin<100)
			  url += "&NbFin="+NbFin;
				  
		  AppendSVG(url,document.getElementById("tagcloud"),false);
		  //met à jour les stats

	  }catch(e){
	  console.log("tagcloud:SelectNetwork:"+e+""); 
	 }
	}
	
	