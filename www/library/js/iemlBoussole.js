    var src, dst, trl,evnt;
	var trace=false;
	var values="";
	var arrNavig = new Array("ModifPave('O:|M:');");
	//nombre maximum d'enfant 
	var maxEnfant = 60;
	var colorChoos=false;
	var color="";
	var predEvt="" ;
	var arrNavig = new Array("ModifPave('O:|M:');");
   
    function init() {
        GetPalette();
	   
    }

	 function BoucleHideShow(iemlCode, gId){
		
		var id;
		    	
		for (var i = 1; i <= maxEnfant; i++) {
			id = 'g_'+iemlCode+gId+i;
			if(trace=="true")
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
				if(trace=="true")
					//supprime les RecordPoint
					DelRecordPoint();
			}
	
		}
	}
   
    function ModifTxtMouseOver(evt){
	
		//mis à jour du texte sur le mouse over
	   	var tgt = evt.target;
	   	//prise encompte du cas particulier de la branche
	   	if(tgt.getAttribute("iemlSelect"))
		    document.getElementById("txtMouseOver").firstChild.data=tgt.getAttribute("iemlSelect");
		else		
		    document.getElementById("txtMouseOver").firstChild.data=tgt.getAttribute("iemlCode");
    	
    }
    
    function ModifPave(doc){

	    var arrId = doc.getAttribute("id").split("_");
	    var iemlCode = doc.getAttribute("iemlCode");
		if(trace=="true")	
		    alert("iemlBoussole:ModifPave:iemlCode="+iemlCode);
	    
		var id
		var css;
		//met à jour les pavés seulement dans le cas du choix d'une primitive
		if(arrId.length==2){
			for (var i = 1; i <= 16; i++) {
				//récupère l'identifiant du pavé
				id = 'g_'+iemlCode+'_'+i;
				if(trace=="true")	
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
	   	if(trace=="true")
			alert("iemlBoussole:ShowHidePave:iemlCode="+iemlCode);
	    
		var id;

		//traitement du pavé central
	    BoucleHideShow(iemlCode,'_');

		//traitement de la branche
	    //BoucleHideShow(iemlCode,'-');

    	
    }
    
   
    function HidePave(idSrc){

		//affiche les pavés d'une branche
	   	var tgt = document.getElementById(idSrc);
	    var iemlCode = tgt.getAttribute("iemlCode");
	   	if(trace=="true")
			alert("iemlBoussole:HidePave:iemlCode="+iemlCode);
	       
		var id
		for (var i = 1; i <= maxEnfant; i++) {
			//récupère l'identifiant du pavé
			id = 'g_'+iemlCode+'_'+i;
		    if(trace=="true")
			    dump("iemlBoussole:HidePave:id="+id);
			if(!document.getElementById(id))return;
			//modifie la visibilité du pavé
			document.getElementById(id).setAttribute("visibility","hidden");
			//gère les pavés enfants
			HidePave(id);	
		}
    	
    }
    
    function SelectPave(evt,idDst,idSrc){
        if(!window.parent.document.getElementById('webFrame1'))
        	return;
		if(window.parent.document.getElementById('webFrame1').getAttribute("src")!="" && window.parent.frames['webFrame1'].document.getElementById("DynaPaveCreaPoint")){
			if(window.parent.frames['webFrame1'].document.getElementById("DynaPaveCreaPoint").getAttribute("value")=="true")
				CreaDynaPave(evt);
	        if(window.parent.frames['webFrame1'].document.getElementById("creaPoint").getAttribute("value")=="true")
	        	ShowRecordPoint(evt);
		}
		

		//met à jour la branche suivant le choix du pavé
	   	var tgt = evt.target;
		var cssSrc1,cssSrc2,cssDst, visiDst, iemlCode;
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
		 
	    //récupère le code ieml
	    iemlCode = tgt.getAttribute("iemlCode");
		 if(trace=="true"){
			alert(id1+' '+id2);
			alert(iemlCode);
		 }
		 
		 //récupère l'identifiant du pavé de la branche
		 var idBrache1 = 'g_'+idDst+'_0';
		 var idBrache2 = 'g_'+idDst+'-1';
		 var idLine = 'g_'+idDst+'-2';
		 if(trace=="true")
			alert(idBrache1+' '+idBrache2);
			
		//modifie la class du pavé
		if((document.getElementById(idBrache1).getAttribute("class")==cssSrc1)){
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
		if(document.getElementById(idBrache1)){
			//met à jour les styles
			document.getElementById(idBrache1).setAttribute("class",cssDst1);
			//met à jour le code ieml de la branche
			document.getElementById(idBrache1).setAttribute("iemlSelect",iemlCode);
		}
		if(document.getElementById(idBrache2)){
			document.getElementById(idBrache2).setAttribute("iemlSelect",iemlCode);
			document.getElementById(idBrache2).setAttribute("class",cssDst2);	
			//met à jour la ligne
			document.getElementById(idLine).setAttribute("visibility",visiL);
			//met à jour le code ieml de la ligne
			document.getElementById(idLine).setAttribute("iemlCode",iemlCode);
		};
		
		//affiche le motif on pour la branche
		//document.getElementById('g_'+arrId[1]+"_on").setAttribute("visibility",visiDst);
		
    }
    
    function RecordPoint(evt){
		
	   	var tgt = evt.target;
	    var point = tgt.getAttribute("cx")+","+tgt.getAttribute("cy")+" ";

	    document.getElementById("txtRecordPoint").firstChild.data += point;		
	    var pave = document.getElementById("txtRecordPoint").firstChild.data;
	    document.getElementById("dynaPave").setAttribute("points",pave);		
		values=point+window.parent.frames['webFrame1'].document.getElementById("ShowPoints").getAttribute("value");
		window.parent.frames['webFrame1'].document.getElementById("menu").setAttribute("hidden",'false');
		window.parent.frames['webFrame1'].document.getElementById("ShowPoints").setAttribute("value",values);
		
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
			a.setAttribute("onclick", "RecordPoint(evt);");
			cont.appendChild(a); 
		}	
    }
    
    
   function CreaDynaPave(evt){
   		var SVG_NS ="http://www.w3.org/2000/svg";
   		var tgt = evt.target;
   		var g=document.getElementById("Pave");
	    var point = tgt.getAttribute("points");
	    var iemlcode=tgt.getAttribute("iemlCode");
	    var p=document.createElementNS(SVG_NS, "polygon");
	    p.setAttribute("points", point);
	    p.setAttribute("class", "styleE");
	    p.setAttribute("iemlCode", iemlcode);
	    g.appendChild(p);
    }
    
    function ShowPave(){
        
    	if(document.getElementById("DynaPaveCreaPoint").getAttribute("value")=="false"){
    		document.getElementById("DynaPaveCreaPoint").setAttribute("value","true");
    		document.getElementById("Pave_status").setAttribute("style","background-color:green");
    		
    	}else{
    		document.getElementById("DynaPaveCreaPoint").setAttribute("value","false");
    		document.getElementById("Pave_status").setAttribute("style","background-color:red")
    		var pave = window.parent.frames['webFrame'].document.getElementById("Pave");
       		while(pave.hasChildNodes())
				pave.removeChild(pave.firstChild);
    	
    	}
    }
    
    function ShowPoints(){
    	if(document.getElementById("creaPoint").getAttribute("value")=="true"){
    		document.getElementById("creaPoint").setAttribute("value","false");
    		document.getElementById("Points_status").setAttribute("style","background-color:red");
    		document.getElementById("ShowPoints").setAttribute("value","");
    		document.getElementById("menu").setAttribute("hidden",'true');
    		
       }else{
       	document.getElementById("creaPoint").setAttribute("value","true");
       	document.getElementById("Points_status").setAttribute("style","background-color:green")
       }
}
    function Trace(){
     if(document.getElementById("trace").getAttribute("value")=="true"){
        document.getElementById("Trace_status").setAttribute("style","background-color:red");
     	document.getElementById("trace").setAttribute("value","false");
     	
    } else
     	if(document.getElementById("trace").getAttribute("value")=="false"){
     	   document.getElementById("Trace_status").setAttribute("style","background-color:green");
     	   document.getElementById("trace").setAttribute("value","true");
     	    
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
 
function SelectionCycle(evt){
    
	if(!window.parent.document.getElementById('keyGrid'))
		return;

    var keyGrid = window.parent.document.getElementById('keyGrid').value;
	if(keyGrid=="")
		return;

    var idBranS=[];
    var idBranD=[];
	var tgt = evt.target;
	var iemlCode=tgt.getAttribute("iemlCode");
	
	//recuperation des primitives de la barnche
	
	InitGrille(iemlCode);
	
	iemlCode=iemlCode.replace('*','');
	iemlCode=iemlCode.replace('**','');
	
	//le cas ou l'id de la branche et de la forme **A:A:.*
	if(iemlCode.length > 3){
		idBranArr=iemlCode.split(":.");
		
		if(idBranArr[0].charAt(1) == ":"){
			idBranS=idBranArr[0].split(':');
			 	
		}else{
			idBranS[0]=idBranArr[0];
			idBranS[1]='';
		}
		
		if(idBranArr[1].charAt(1) == ":"){
			idBranD=idBranArr[1].split(':');
		}else{
			idBranD[0]=idBranArr[1].replace(":");
			idBranD[1]='';
		}
	}else{
		//le cas ou id et de la forme a.
		idBranS[0]=iemlCode.replace(':','').replace('.','');
		idBranS[1]='';
		idBranD[0]='';
		idBranD[1]='';
	}
	//les ids de la Grille	 
	
	row=window.parent.document.getElementById(keyGrid+'CycleRows').childNodes; 
	
	// mettre a jour les cellules de la grille

	
	
	for(var i=1 ; i < row.length; i++){
		rowChild=row[i].childNodes;
		
		//if(rowChild.length==0) break;
            for(k=0;k<rowChild.length;k++){		
				idLabel=rowChild[k].getAttribute("id");
				idNoeud=idLabel.substring(23);
				
		 		for(var j=0 ; j < 2 ; j++){
		 		
		           if(idBranS[j]!=''){
		           
		           //Expression reguliere = le mot recherché
		           
		           var ExprS = new RegExp(idBranS[j]);
		           
		           // Expression regulière de type wa,wo,we,wu
		           
		           var Exp=new RegExp("w[auoe]");
		           
		           //si le mot recherché contient w[auoe] on le remplace par le vide  
		           
		           if(Exp.test(idBranS[j])==false){
		           		chaine=idNoeud.replace(Exp," ");
		           		
		           	}else
		           		chaine=idNoeud;
		           	// deslectionne le case qui conteint le mot cherché
		           
					if(ExprS.test(chaine)==true){
			         	   
			         	   		window.parent.document.getElementById(idLabel).removeAttribute('class');
			         	 		window.parent.document.getElementById(idLabel).setAttribute('hidden',"true");
			         		
			       	}
			      }
			      
				    if(idBranD[j]!=''){
				    var ExprB = new RegExp(idBranS[j]);
				    if(Exp.test(idBranD[j])==false){
		           		chaine=idNoeud.replace(Exp," ");
		           		window.parent.console.log(chaine);
		           	}else
		           		chaine=idNoeud;
					if( ExprB.test(chaine)==true){
						    
			         	 		window.parent.document.getElementById(idNoeud).removeAttribute('class');
			         	 		window.parent.document.getElementById(idNoeud).setAttribute('hidden','true');
			         	    
			        }
			   }
			}
		
		}		
	}
}  

function InitGrille(iemlCode){
  
	if(!window.parent.document.getElementById('keyGrid'))
		return;

    var keyGrid = window.parent.document.getElementById('keyGrid').value;
	if(keyGrid=="")
		return;
    
  	row=window.parent.document.getElementById(keyGrid+'CycleRows').childNodes; 
	for(var i=1 ; i < row.length; i++){
		rowChild=row[i].childNodes;
            for(k=1;k<rowChild.length;k++){	
  		       idNoeud=rowChild[k].getAttribute("id")
			   window.parent.document.getElementById(idNoeud).setAttribute('class',"NoSelect");
			   window.parent.document.getElementById(idNoeud).setAttribute('hidden',"false");
			}
	}
}

function AfficheIeml(id){
    descp=document.getElementById(id).textContent;
    id=id.replace('*','').replace('**','');
    document.getElementById('code-trad-ieml').setAttribute('value',id);
	document.getElementById('lib-trad-ieml').setAttribute('value',descp);
	
}
function RequestIemlCycle(){
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=IemlCycle","GetCycle"," ","");
	

}
function GetCycle(result){
	console.log(result);
	
	
}
function ActiveMenu(){
    
    document.getElementById('Outils').setAttribute("visibility","hidden");
    window.parent.document.getElementById('webFrame1').setAttribute("src","library/svg/iemlMenuBoussole.xul");
	window.parent.document.getElementById('webFrame1').setAttribute("hidden","false");
}
function DesactiveMenu(){
   
   window.parent.frames['webFrame'].document.getElementById('Outils').setAttribute("visibility","visible");
 	window.parent.document.getElementById('webFrame1').setAttribute("src"," ");
	window.parent.document.getElementById('webFrame1').setAttribute("hidden","true");
}



 