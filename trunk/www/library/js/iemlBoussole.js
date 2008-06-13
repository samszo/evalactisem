    var src, dst, trl,evnt;
	var trace=false;
	var creaPoint=false;
	var DynaPaveCreaPoint=false;
	var values="";
	var arrNavig = new Array("ModifPave('O:|M:');");
	//nombre maximum d'enfant 
	var maxEnfant = 60;
	var colorChoos=false;
	var color="";
	var arrNavig = new Array("ModifPave('O:|M:');");
   
    function init() {
        GetPalette();
	   
    }

	 function BoucleHideShow(iemlCode, gId){
		
		var id;
		    	
		for (var i = 1; i <= maxEnfant; i++) {
			id = 'g_'+iemlCode+gId+i;
			if(trace)
			    dump("iemlBoussole:BoucleHideShow:id="+id +"document.getElementById(id) "+document.getElementById(id));
			if(!document.getElementById(id))return;
			//modifie la visibilité du pavé
			if(document.getElementById(id).getAttribute("visibility")=="hidden"){
				document.getElementById(id).setAttribute("visibility","visible");
				HidePave(id,gId);
			}else{
				document.getElementById(id).setAttribute("visibility","hidden");
				//gère les pavés enfants
				HidePave(id,gId);
				if(trace)
					//supprime les RecordPoint
					DelRecordPoint();
			}
	
		}
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
		    alert("iemlBoussole:ModifPave:iemlCode="+iemlCode);
	    
		var id
		var css;
		//met à jour les pavés seulement dans le cas du choix d'une primitive
		if(arrId.length==2){
			for (var i = 1; i <= 16; i++) {
				//récupère l'identifiant du pavé
				id = 'g_'+iemlCode+'_'+i;
				if(trace)	
				    alert("iemlBoussole:ModifPave:id="+id);
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
	   	tgt = document.getElementById(idSrc);
	    var iemlCode = tgt.getAttribute("iemlCode");
	   	if(trace)
			alert("iemlBoussole:ShowHidePave:iemlCode="+iemlCode);
	    
		var id;

		//traitement du pavé central
	    BoucleHideShow(iemlCode,'_');

		//traitement de la branche
	    //BoucleHideShow(iemlCode,'-');

    	
    }
    
    function BoucleHideShow(iemlCode, gId){
		
		var id;
		    	
		for (var i = 1; i <= maxEnfant; i++) {
			id = 'g_'+iemlCode+gId+i;
			if(trace)
			    dump("iemlBoussole:BoucleHideShow:id="+id +"document.getElementById(id) "+document.getElementById(id));
			if(!document.getElementById(id))return;
			//modifie la visibilité du pavé
			if(document.getElementById(id).getAttribute("visibility")=="hidden"){
				document.getElementById(id).setAttribute("visibility","visible");
				HidePave(id,gId);
			}else{
				document.getElementById(id).setAttribute("visibility","hidden");
				//gère les pavés enfants
				HidePave(id,gId);
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
		for (var i = 1; i <= maxEnfant; i++) {
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

   
   
    function SelectPave1(evt,idDst,idSrc){
        
		if(DynaPaveCreaPoint)
			CreaDynaPave(evt);
        if(creaPoint)
        	ShowRecordPoint(evt);
		//met à jour la branche suivant le choix du pavé
	   	var tgt = evt.target;
		var cssSrc,cssDst, visiDst;
		if(!idSrc)
			cssSrc = tgt.getAttribute("class");		
		
		//récupère l'identifiant du pavé de la branche
		var id = 'g_'+idDst+'_0';
		if(trace)
			alert(id+' '+cssSrc);
		
		if(!document.getElementById(id))return;
		
		//modifie la class du pavé
		if(document.getElementById(id).getAttribute("class")==cssSrc){
			//efface
			cssDst = 'styleF';
			visiDst = "hidden";
		}else{
			//met le style de la source
			cssDst = cssSrc;
			visiDst = "visible";
		}
		//alert(visiDst+' '+cssDst);
		document.getElementById(id).setAttribute("class",cssDst);
		//affiche le motif on pour la branche
		//document.getElementById('g_'+arrId[1]+"_on").setAttribute("visibility",visiDst);
		
    }
    
    function SelectPave(evt,idDst,idSrc){
        
		if(DynaPaveCreaPoint)
			CreaDynaPave(evt);
        if(creaPoint)
        	ShowRecordPoint(evt);
		//met à jour la branche suivant le choix du pavé
	   	var tgt = evt.target;
		var cssSrc1,cssSrc2,cssDst, visiDst;
		//recuperation de l'identifiant de premier pavé
		if(!idSrc){
			   if(idDst=='*(O:|M:)**'){
				id1 = tgt.getAttribute("id");
				//recuperation de l'identifaint de deuxieme pavé
				lemme_id=id1.split("_");
				indice=parseInt(lemme_id[2])+1;
				id2 = "g_"+lemme_id[1]+"_"+indice;
				if(!lemme_id[2])return;
			    if(!document.getElementById(id2)){
			    	indice=parseInt(lemme_id[2])-1;
			    	id2 = "g_"+lemme_id[1]+"_"+indice;
			    }
			    
			   // alert(lemme_id[2]+' '+indice);
			    if(document.getElementById(id1).getAttribute("iemlCode")==document.getElementById(id2).getAttribute("iemlCode")){
			    	cssSrc1=document.getElementById(id2).getAttribute("class");
			        cssSrc2=document.getElementById(id1).getAttribute("class");
			    }else{
			        
				    indice=parseInt(lemme_id[2])-1;
				    id2 = "g_"+lemme_id[1]+"_"+indice;
				    if(!document.getElementById(id2))return;
				    	cssSrc1=document.getElementById(id1).getAttribute("class");
				        cssSrc2=document.getElementById(id2).getAttribute("class");
	 
			    }
		   
			 }else{
			 
			 	cssSrc1=tgt.getAttribute("class");
			    cssSrc2=cssSrc1;
			 }
		 }
		 if(trace)
			alert(id1+' '+id2);
		    //récupère l'identifiant du pavé de la branche
		 var idBrache1 = 'g_'+idDst+'_0';
		 var idBrache2 = 'g_'+idDst+'-1';
		 var idLine = 'g_'+idDst+'-2';
		 if(trace)
			alert(idBrache1+' '+idBrache2);
			
		if(!document.getElementById(idBrache1)||!document.getElementById(idBrache2))return;
			//modifie la class du pavé
		if((document.getElementById(idBrache1).getAttribute("class")==cssSrc1)&&(document.getElementById(idBrache2).getAttribute("class")==cssSrc2)){
			//efface
			cssDst1 = 'styleF';
			cssDst2 = 'styleF';
			visiL=  "hidden";
		}else{
			//met le style de la source
			cssDst1 = cssSrc1;
			cssDst2 = cssSrc2;
			visiL=  "visible";
		}
		//alert(visiDst+' '+cssDst);
		document.getElementById(idBrache1).setAttribute("class",cssDst1);
		document.getElementById(idBrache2).setAttribute("class",cssDst2);
		document.getElementById(idLine).setAttribute("visibility",visiL);
		
		//affiche le motif on pour la branche
		//document.getElementById('g_'+arrId[1]+"_on").setAttribute("visibility",visiDst);
		
    }
    
    function RecordPoint(evt){
		
	   	var tgt = evt.target;
	    var point = tgt.getAttribute("cx")+","+tgt.getAttribute("cy")+" ";

	    document.getElementById("txtRecordPoint").firstChild.data += point;		
	    var pave = document.getElementById("txtRecordPoint").firstChild.data;
	    document.getElementById("dynaPave").setAttribute("points",pave);		
		values=point+document.getElementById("ShowPoints").getAttribute("value");
		document.getElementById("menu").setAttribute("hidden",'false');
		document.getElementById("ShowPoints").setAttribute("value",values);
		
    }
    

	function DelRecordPoint(){
		
	    var cont = document.getElementById("RecordPoints");
		while(cont.hasChildNodes())
			cont.removeChild(cont.firstChild);
		
	}
function jsdump(str) 
{ 
  Components.classes['...@mozilla.org/consoleservice;1'] 
            .getService(Components.interfaces.nsIConsoleService) 
            .logStringMessage(str); 
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
			a.setAttribute("onclick", "RecordPoint(evt);");
			cont.appendChild(a); 
		}	
    }
    
    
   function CreaDynaPave(evt){
   		var SVG_NS ="http://www.w3.org/2000/svg";
   		var tgt = evt.target;
   		var g=document.getElementById("Pave");
	    var point = tgt.getAttribute("points");
	    var iemlcode=tgt.getAttribute("iemlCode")
	    var p=document.createElementNS(SVG_NS, "polygon");
	    p.setAttribute("points", point);
	    p.setAttribute("class", "styleE");
	    p.setAttribute("iemlCode", iemlcode);
	    g.appendChild(p);
    }
    
    function ShowPave(){
    	if(DynaPaveCreaPoint==false){
    		DynaPaveCreaPoint=true;
    		document.getElementById("Pave_status").setAttribute("style","background-color:green");
    		
    	}else{
    		DynaPaveCreaPoint=false;
    		document.getElementById("Pave_status").setAttribute("style","background-color:red")
    		var pave = document.getElementById("Pave");
       		while(pave.hasChildNodes())
				pave.removeChild(pave.firstChild);
    	
    	}
    }
    
    function ShowPoints(){
    	if(creaPoint==true){
    		creaPoint=false;
    		document.getElementById("Points_status").setAttribute("style","background-color:red");
    		document.getElementById("dynaPave").setAttribute("points", "");
    		document.getElementById("ShowPoints").setAttribute("value","");
    		document.getElementById("menu").setAttribute("hidden",'true');
    		
       }else{
       	creaPoint=true;
       	document.getElementById("Points_status").setAttribute("style","background-color:green")
       }
}
    function Trace(){
     if(trace==true){
        document.getElementById("Trace_status").setAttribute("style","background-color:red");
     	trace=false;
    } else
     	if(trace==false){
     	    document.getElementById("Trace_status").setAttribute("style","background-color:green");
     		trace=true;
     	}
	}
	
	
  function colorpicker(couleur){
   	 document.getElementById("colorpicker").hidePopup();
   	 colorChoos='true';
   	 color=couleur;
  }
  
  
  function ChangeColor(id){
    var colors="";
    var l=0;
  	if(colorChoos=='true'){
  		 document.getElementById(id).setAttribute("fill", color);
  	     colorChoos='false';
  	     var arrIdB= new Array("g_*F:**","g_*(O:|M:)**");
	  	 for(var i = 0; i < arrIdB.length; i++){
		  	 iemlCode=document.getElementById(arrIdB[i]).getAttribute("iemlCode");
		  	 //recuppere les couleurs de tous les paves
		  	 for (var k = 1; k <= maxEnfant; k++) {
		  	    id = 'g_'+iemlCode+'_'+k;
		  	    if(!document.getElementById(id)) break;
			  	colors+='<color id="'+id+'">'+document.getElementById(id).getAttribute("fill")+'</color>';
			  	colors+=RecupFill(id);
		  	
		  	}
		  	
	  	}
  	 colors='<palette>'+colors+'</palette>';
     urlparams="f=SavePalette&color="+colors;
     AjaxRequestPost(urlAjax+"/library/php/ExeAjax.php",urlparams,"","","");
  	}
 } 
 function RecupFill(idb){
         var fill="";
		//affiche les pavés d'une branche
	    var iemlCode = document.getElementById(idb).getAttribute("iemlCode");
	   	if(trace)
			alert("iemlBoussole:RecupFill:iemlCode="+iemlCode);
	       
		var id
		for (var i = 1; i <= 18; i++) {
			//récupère l'identifiant du pavé
			
			id = 'g_'+iemlCode+'_'+i;
		    if(trace)
			    dump("iemlBoussole:RecupFill:id="+id);
			if(!document.getElementById(id)) break;
			//modifie la visibilité du pavé
		   if(trace)
			  dump("iemlBoussole:RecupFill:fill="+fill);
			
			fill+='<color id="'+id+'">'+document.getElementById(id).getAttribute("fill")+'</color>';
			if(trace)
			  dump("iemlBoussole:RecupFill:fill="+color);
			//gère les pavés enfants
			
		}
    	return fill;
    }
    function GetPalette(){
    	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=GetPalette" ,'AfficheResult','');
    }
   
    function AfficheResult(result,param){
    var l=0;
    arrColor = new Array();
         arrPatette=result.split('&'); 
         for(l=0 ; l < arrPatette.length; l++){
         	C=arrPatette[l].split(';');
         	arrColor[C[0]]=C[1];
         }
        
         var arrIdB= new Array("g_*F:**","g_*(O:|M:)**");
	  	 for(var i = 0; i < arrIdB.length; i++){
		  	 iemlCode=document.getElementById(arrIdB[i]).getAttribute("iemlCode");
		  	 //recuppere les couleurs de tous les paves
		  	 for (var k = 1; k <= maxEnfant; k++) {	
		  	    id = 'g_'+iemlCode+'_'+k;
		  	    if(l<arrPatette.length-1) l++;
		  	    if(!document.getElementById(id)) break;
					document.getElementById(id).setAttribute("fill",arrColor[id]);
                    iemlCodeChild = document.getElementById(id).getAttribute("iemlCode");
  					for (var j = 1; j <= maxEnfant; j++) {
						idChild = 'g_'+ iemlCodeChild+'_'+j;
						if(!document.getElementById(idChild)) break;
						document.getElementById(idChild).setAttribute("fill",arrColor[idChild]);
						
  					}
	  		}
  		}
  }
 