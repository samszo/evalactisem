    var src, dst, trl;
	var trace=false;
	var arrNavig = new Array("ModifPave('O:|M:');");

    function init() {
	    location.reload();
    }


    function clickSrc(evt) {

	   	var tgt = evt.target;
	   	var txtLgd;
		if(!src){
	    	src=tgt;
	    	txtLgd = "txtSrc"
	    }else{
			if(!dst){
		    	dst = tgt;
		    	txtLgd = "txtDst"
		    }else{
				trl = tgt;	    
		    	txtLgd = "txtTrl"
		    }
	    }
	    	
		ModifPave(tgt);
		
		//mis à jour du texte
	    document.getElementById(txtLgd).firstChild.data=tgt.getAttribute("iemlCode");
	
		if(trace)	
		    console.log("iemlBoussole:clickSrc:%id=",tgt.getAttribute("id"));
	    //alert(src.getAttribute("iemlCode"));
	
    }
    
    function ModifTxtMouseOver(evt){
	
		//mis à jour du texte sur le mouse over
	   	var tgt = evt.target;
	    document.getElementById("txtMouseOver").firstChild.data=tgt.getAttribute("iemlCode");
    	
    }
    
    function ModifPave(doc){

	    var arrId = doc.getAttribute("id").split("_");
	    var iemlCode = doc.getAttribute("iemlCode");
		if(trace)	
		    dump("iemlBoussole:ModifPave:iemlCode="+iemlCode);
	    
		var id
		var css;
		//met à jour les pavés seulement dans le cas du choix d'une primitive
		if(arrId.length==2){
			for (var i = 1; i <= 16; i++) {
				//récupère l'identifiant du pavé
				id = 'g_'+iemlCode+'_'+i;
				if(trace)	
				    dump("iemlBoussole:ModifPave:id="+id);
				if(!document.getElementById(id))return;
				//modifie la class du pavé
				if(document.getElementById(id).getAttribute("class")=="styleI")
					css = 'style'+arrId[1]+'dst';
				else
					css = 'styleI';
				document.getElementById(id).setAttribute("class",css);		
			}
		}
    	
    }

    function ShowHidePave(idSrc){

		//affiche les pavés d'une branche
	   	var tgt = document.getElementById(idSrc);
	    var iemlCode = tgt.getAttribute("iemlCode");
	   	if(trace)
			alert("iemlBoussole:ShowHidePave:iemlCode="+iemlCode);
	    
		var id
		for (var i = 1; i <= 16; i++) {
			//récupère l'identifiant du pavé
			id = 'g_'+iemlCode+'_'+i;
		    if(trace)
			    dump("iemlBoussole:ShowHidePave:id="+id);
			if(!document.getElementById(id))return;
			//modifie la visibilité du pavé
			if(document.getElementById(id).getAttribute("visibility")=="hidden"){
				document.getElementById(id).setAttribute("visibility","visible");
			}else{
				document.getElementById(id).setAttribute("visibility","hidden");
				//gère les pavés enfants
				HidePave(id);
				if(trace)
					//supprime les RecordPoint
					DelRecordPoint();
			}
	
		}
    	
    }


    function HidePave(idSrc){

		//affiche les pavés d'une branche
	   	var tgt = document.getElementById(idSrc);
	    var iemlCode = tgt.getAttribute("iemlCode");
	   	if(trace)
			alert("iemlBoussole:HidePave:iemlCode="+iemlCode);
	    
		var id
		for (var i = 1; i <= 16; i++) {
			//récupère l'identifiant du pavé
			id = 'g_'+iemlCode+'_'+i;
		    if(trace)
			    dump("iemlBoussole:HidePave:id="+id);
			if(!document.getElementById(id))return;
			//modifie la visibilité du pavé
			document.getElementById(id).setAttribute("visibility","hidden");
			//gère les pavés enfants
			HidePave(id);	
		}
    	
    }


    function SelectPave(evt,idDst){

		if(trace)
			ShowRecordPoint(evt);

		//met à jour la branche suivant le choix du pavé
	   	var tgt = evt.target;
		var cssSrc = tgt.getAttribute("class");		
		var cssDst, visiDst;
		
		//récupère l'identifiant du pavé de la branche
		var id = 'g_'+idDst+'_0';

		if(!document.getElementById(id))return;
		//alert(id+' '+cssSrc);
		//modifie la class du pavé
		if(document.getElementById(id).getAttribute("class")==cssSrc){
			cssDst = 'styleF';
			visiDst = "hidden";
		}else{
			cssDst = cssSrc;
			visiDst = "visible";
		}
		//alert(visiDst+' '+cssDst);
		
		document.getElementById(id).setAttribute("class",cssDst);
		//affiche le motif on pour la branche
		//document.getElementById('g_'+arrId[1]+"_on").setAttribute("visibility",visiDst);
		
    }
    
    function RecordPoint(evt){

		//met à jour la branche suivant le choix du pavé
	   	var tgt = evt.target;
	    var point = tgt.getAttribute("cx")+","+tgt.getAttribute("cy")+" ";

	    document.getElementById("txtRecordPoint").firstChild.data += point;		
		
    }

	function DelRecordPoint(){
		
	    var cont = document.getElementById("RecordPoints");
		while(cont.hasChildNodes())
			cont.removeChild(cont.firstChild);
		
	}

    function ShowRecordPoint(evt){

		//création de point réactif à partir d'un path
		var SVG_NS ="http://www.w3.org/2000/svg";
	   	var tgt = evt.target;
	    var arrPoints = tgt.getAttribute("points").split(" ");
	    var cont = document.getElementById("RecordPoints");
	    
		for (var i=0; i < arrPoints.length; i++) {
			var point = arrPoints[i].split(",");
			var a=document.createElementNS(SVG_NS, "circle");
			a.setAttribute("cx", point[0]);
			a.setAttribute("cy", point[1]);
			a.setAttribute("r", "6");
			a.setAttribute("class", "styleBdst");
			a.setAttribute("onclick", "RecordPoint(evt)");
			cont.appendChild(a); 
		}	
    }

    