

function SetSession(id, val) {
	try {
		var url = urlExeAjax+"?f=SetSession&id="+id+"&val="+val;
		//met � jour les valeurs de session
		GetResult(url);

  	} catch(ex2){alert("interface:SetSession:"+ex2);}
}



function ShowTooltip(evt)
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


function HideTooltip(evt)
{
        evt.target.ownerDocument.getElementById("tooltip").setAttributeNS(null , "visibility", "hidden")
}

function GetFlux(getFlux){
    m=0;
    document.getElementById('label_Maj').setAttribute('value','Veuillez patienter la récupération du flux est en cours...');
    var meter=document.getElementById('Maj');
	meter.setAttribute("hidden","false");
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=GetFlux&lang="+lang+"&getFlux="+getFlux,'Xul_Ajax_ShowTreeTrad','');
    
}
  
function Xul_Ajax_ShowTreeTrad(result){

  try {
		document.documentElement.style.cursor = "wait";
	
		//pour les traduction faites
		var url = urlAjax+"library/php/ExeAjax.php?f=GetTreeTradUtis&lang="+lang;
		AppendResult(url,document.getElementById('tpSingleTrad'),false);

		//pour les non traduition
		url = urlAjax+"library/php/ExeAjax.php?f=GetTreeNoTradUti";
		AppendResult(url,document.getElementById('tpNoTrad'),false);

	  	document.getElementById('infosTrad').setAttribute("hidden","false");
	  	document.getElementById('treeDicoIeml').setAttribute("hidden","false");
	  	document.getElementById('contDonnee').setAttribute("hidden","false");

	    var meter=document.getElementById('Maj');
		meter.setAttribute("value","100");
		meter.setAttribute("hidden","true");
		document.getElementById('label_Maj').setAttribute("hidden","true");
		
  } catch(ex2){ alert("interface:ShowTreeTrad:"+ex2); }
	document.documentElement.style.cursor = "auto";

}

function Sem_AddTrad(){
	
	var libIeml=document.getElementById("lib-trad-ieml");
    var codeIeml=document.getElementById("code-trad-ieml");
    var codeFlux=document.getElementById("code-trad-flux");
    var message=document.getElementById("trad-message");
    
	if(codeIeml.value==""){
		alert("Veuillez s�lectionner une expression IEML");
		return
	}	
	if(codeFlux.value==""){
		alert("Veuillez s�lectionner un tag del.icio.us");
		return
	}	

	//v�rifie la saisie
	var verif = GetResult(urlAjax+"library/php/ExeAjax.php?f=VerifExpIEML&codeIeml="+codeIeml.value+"&libIeml="+libIeml.value);
	if(verif!="OK"){
		message.value = verif;
		return;
	}
	
	message.value = GetResult(urlAjax+"library/php/ExeAjax.php?f=AddTrad&codeIeml="+codeIeml.value+"&codeFlux="+codeFlux.value+"&libIeml="+libIeml.value+"&lang="+lang);
	
	Xul_Ajax_ShowTreeTrad();
	
}   

//Supression d'une traduction

function Sem_SupTrad()
{
	//r�cup�ration des valeurs
	
	var libIeml=document.getElementById("lib-trad-ieml");
    var codeIeml=document.getElementById("code-trad-ieml");
    var codeFlux=document.getElementById("code-trad-flux");
   
	if(codeIeml.value=="" || libIeml.value=="" ){
		alert("Veuillez s�lectionner une expression IEML");
		return
	}	
	if(codeFlux.value==""){
		alert("Veuillez s�lectionner un tag del.icio.us");
		return
	}	

	var message=document.getElementById("trad-message");
	message.value = GetResult(urlAjax+"library/php/ExeAjax.php?f=SupTrad&codeIeml="+codeIeml.value+"&libIeml="+libIeml.value+"&codeflux="+codeFlux.value);

	Xul_Ajax_ShowTreeTrad();
		
}
function SelectNoTrad(id,treecol){

	document.getElementById("trad-message").value = "";
	var tree = document.getElementById(id);
  	var txtcode_flux=document.getElementById("code-trad-flux");
	var txtcode_ieml = document.getElementById("code-trad-ieml");
	var txtlib_ieml=document.getElementById("lib-trad-ieml");
  	var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
  	IdTreeItem=selection.getAttribute('id');

  	var TreeRow=document.getElementById("TreeRow_"+IdTreeItem);

  	//label du tag del.icio.us
  	console.log(TreeRow.childNodes.length);
  	console.log(TreeRow.childNodes[3]);
    txtcode_flux.value=TreeRow.childNodes[3].getAttribute('label');
    txtlib_ieml.value = TreeRow.childNodes[7].getAttribute('label');
    //txtcode_ieml.value = TreeRow.childNodes[9].getAttribute('label');
   
    
}
function SelectDictio(id,treecol1,treecol2){
	
	var tree = document.getElementById(id);
	var selection = tree.contentView.getItemAtIndex(tree.currentIndex);
  	var txtcode_flux=document.getElementById("code-trad-flux");
  	var txtcode_ieml = document.getElementById("code-trad-ieml");
  	var txtlib_ieml=document.getElementById("lib-trad-ieml");
  	txtlib_ieml.value=tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn(treecol2));
    txtcode_ieml.value= tree.view.getCellText(tree.currentIndex,tree.columns.getNamedColumn(treecol1));
}
function SelectTrad(id,treecolTag,treecolTrad,treecolIeml,type){
  try{
	document.getElementById("trad-message").value = "";
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
		txtcode_ieml.value=iemlCode;
     }
     
     if(iemlCode!="" && tag=="" ){
       
		var parent=tree.contentView.getParentIndex(tree.currentIndex);
		var GrandParent=tree.contentView.getParentIndex(parent);
		
		txtcode_ieml.value= iemlCode;
		txtlib_ieml.value=GetTreeValSelect(id,treecolTrad);
		if(GrandParent==0 || GrandParent==-1 ){
			txtcode_flux.value=tree.view.getCellText(parent,tree.treeBoxObject.columns[treecolTag]);
		}else{
			txtcode_flux.value=tree.view.getCellText(GrandParent,tree.treeBoxObject.columns[treecolTag]);
		}		
     }
     if(txtlib_ieml.value=="" && txtcode_ieml.value=="")
		txtcode_flux.value="";
     	  
  }
  		 
 catch(e){
  console.log("interface:Select_Trad:"+e+""); 
 }
}

function ParserIemlExp(op,type){
  try {
    var tree = document.getElementById("Signl_Trad");
    //prise en compte de la s�lection multiple
    Iemlcode=GetIemlTreeExp("Signl_Trad", 3, op);
    if(Iemlcode==")"){
    	alert("Veuillez s�lectionner un ou plusieurs Tags traduits");
    	return;
	}    
	var url = urlAjax+"library/php/ExeAjax.php?f=ParserIemlExp&code="+Iemlcode+"&type="+type;
	url = GetResult(url);

	GoUrl(url,'bIemlStat');
	
	var tabStatIeml = document.getElementById('tabStatIeml')
	document.getElementById('tbIframe').selectedTab=tabStatIeml;	
  } catch(ex2){ alert("interface:Parser:"+ex2); }
}

function GetIemlUsl(){
  try {
  } catch(ex2){ alert("interface:GetIemlUsl:"+ex2); }
}


function GetIemlTreeExp(idTree, col, op){

  try {
    var start = new Object();
	var end = new Object();
	var tree;
	var IemlToParse = "";
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
	if(trace)
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
	IemlToParse += "";

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

function BookMark_AddPostIemlDelicious(){
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=AddPostIemlDelicious",'Ajax_Afficher','')
	var meter=document.getElementById('Maj');
	document.getElementById('label_Maj').setAttribute('value','Veuillez patienter la mise a jour est en cours...');
	meter.setAttribute("hidden","false");
	
}

function Ajax_SupprimeDelicious(result,prarm){
	
		var meter=document.getElementById('progmeter');
		meter.setAttribute("value","100");
	    alert(result);
		document.getElementById('Maj').setAttribute("hidden","true");
	    window.location.href = "exit.php";
}

function Ajax_Afficher(result,prarm){
	
	var meter=document.getElementById('progmeter');
	meter.setAttribute("value","100");
	if(result==''){
		alert("il n'y pas de Posts recents � mettre a jour");
		document.getElementById('Maj').setAttribute("hidden","true");
	}else{
		message='Les Posts suivants ont ete mis a jour: ';
		alert(message+result);
		document.getElementById('Maj').setAttribute("hidden","true");
	}
	window.open('http://delicious.com/ieml','IEML',1000,1000);
}
function BookMark_SupprimerCompteDelicious(){
    
    var meter=document.getElementById('Maj');
	meter.setAttribute("hidden","false");
	document.getElementById('label_Maj').setAttribute("value","La suppression est en cours ...");
	AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=DeletCompteDelicious",'Ajax_SupprimeDelicious','');
	
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
    
function Load(key){
	google.load("visualization", "1");
	google.setOnLoadCallback(Initialize);
}
 function LoadCycle(key,N){
	//v�rifie que l'onglet n'est pas d�j� rempli
		document.getElementById('keyGrid').value=key;
		document.getElementById('CycleLab_'+N).setAttribute('value',key);
		if(window.parent.frames['iemlCycle_'+key].document.getElementById(key+"CycleRows"))
			return;
	    document.getElementById('label_Maj').setAttribute('value','Veuillez patienter, le chargement du cycle est en cours...');
	    var meter=document.getElementById('Maj');
		meter.setAttribute("hidden","false");
		meter.setAttribute("value","50");
  
     	document.getElementById('iemlCycle_'+key).setAttribute("src","overlay/IemlCycle.php?key="+key)
     	
}
 function Initialize() {
	 //var key="p8PAs8y8e1x2YTS7Zgag7Nw&hl=en";
	
     var query = new google.visualization.Query("http://spreadsheets.google.com/tq?key="+key);
     query.send(Sem_CreaCycle);  // Send the query with a callback function
   }
  
   // Query response handler function.
   function Sem_CreaCycle(response) {
     if (response.isError()) {
       alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
       return;
     }

     var data = response.getDataTable();
     var html = [];
     var descp="";
     var id="";
     if(document.getElementById(key+"CycleRows"))
    	 return;
     // Header row
     json="[";
     var j=0;
     for (var row = 0; row < data.getNumberOfRows()-1; row++) {
      if(row%2 !=0){
    	 json+='{"row":'+j+','+'"key":"'+key+'",';
	     for (var col = 0; col < data.getNumberOfColumns()-1; col++) {
        	 if(data.getFormattedValue(row, col)!=" "){ 
	    		   descp=data.getFormattedValue(row, col);
	    		   id=key+"*"+ EscapeHtml(data.getFormattedValue(row-1, col))+"**";
	    		   if(data.getFormattedValue(row-1, col)=="")
	    			   code="vide";
	    		   else
	    			   code=EscapeHtml(data.getFormattedValue(row-1, col));
	    		   json+='"descp'+col+'":"'+descp+'","code'+col+'":"'+code+'",';
	    		   // html.push("<td id="+id+" ><a  id='a_"+row+col+ "' href='#' class='NoSelect'>"+descp+"</a></td>");
	    	  }
	     }
	     j++;
	     json+='},';
	     json=json.replace(/,}/g,'}')
      }
     }
     json+="]";
     json=json.replace(/,]/g,']').replace(/\n/g,' ');
     AjaxRequest("http://localhost/evalactisem/content/library/php/ExeAjax.php?f=CreaCycle&json="+json,'Ajax_AfficheCycle','');
     
    
}

   function EscapeHtml(text) {
     if (text == null)
       return '';

     return text.replace(/&/g, '&amp;')
       .replace(/</g, '&lt;')
       .replace(/>/g, '&gt;')
       .replace(/"/g, '&quot;');
}
   function Ajax_AfficheCycle(result){
	   var meter=window.parent.document.getElementById('Maj');
	   meter.setAttribute("value","100");
	   meter.setAttribute("hidden","true");
	   window.parent.document.getElementById('label_Maj').setAttribute("hidden","true");
	   document.getElementById(key+'_div').innerHTML = result;
   }
   // fonction qui est lanc�e � chaque changement du champs de recherche
	function lancer(e) {
	    tgt = e.target;
	    if(tgt.getAttribute("id")=='lib-trad-ieml'){
	    	var _query = document.getElementById("lib-trad-ieml").value;
	    	type="lib";
	    }else
	     if(tgt.getAttribute("id")=='code-trad-ieml'){
	    	var _query = document.getElementById("code-trad-ieml").value;
	    	type="code";
	    } else
	     if(tgt.getAttribute("id")=='code-trad-flux'){
	    	var _query = document.getElementById("code-trad-flux").value;
	    	type="tag";
	    }
		var keycode;
		
		
		if(window.event){							// d�terminer le code de la touche IE / autres navigateurs
			keycode = window.event.keyCode;
		} else if(e) {
			keycode = e.which;
		}
		
		if(keycode == 38 && resultats > 0) {				// touche "haut"
			
			marquer('up');
			
		} else if(keycode == 40 && resultats > 0) {			// touche "bas"
			
			marquer('down');
			
		} else if(keycode == 13 && resultats > 0) {			// touche "return"
			
			marquer('afficher');
			
		} else if(_query != "") {	
		    if(type=='code')
		    	Lan='ieml';
		    else
		    	Lan=lang;						// ou _query.length > 2 ?
			AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=recherche&query="+_query+"&type="+type+"&lang="+Lan,'afficher','');
		
		} else {
			
			box=document.getElementById('calque_'+type);
			while(box.hasChildNodes()){
				box.removeChild(box.lastChild);
			}
			box.setAttribute('hidden','true');
		}
	}
   
    function afficher(json) {
		box=document.getElementById('calque_'+type);
		box.setAttribute('hidden','false');
		//suppression des noeuds 
		while(box.hasChildNodes()){
				box.removeChild(box.lastChild);
		}
		Items=eval("("+json+")");
		
		for(i=0;i<Items.lib.length;i++){
			 item=document.createElement('listitem');
		 	 addListcell(item,Items.lib[i],Items.id[i]);

			 box.appendChild(item);
		}
	}	
	function addListcell(item,labelCell,idCell){
		listcell=document.createElement('listcell');
		listcell.setAttribute('label',labelCell);
		listcell.setAttribute('id',idCell);
		listcell.setAttribute('style',"min-width:100px;");
		item.appendChild(listcell);
	}
	function getSelectItemRech(select1,select2,typeSelect){
		
		listbox=document.getElementById('calque_'+type);
		selection=listbox.selectedIndex;
		ieml_lib=listbox.getItemAtIndex(selection).firstChild.getAttribute('label');
		idSelect=listbox.getItemAtIndex(selection).firstChild.getAttribute('id');
		document.getElementById(select1).value=ieml_lib;
		if(document.getElementById(select2)){
			selectCalque=select2;
		    if(typeSelect=='lib')
		    	AjaxRequest( urlAjax+"library/php/ExeAjax.php?f=getSelectItemRech&id="+idSelect+'&lang=ieml','Ajax_getSelectItemRech','');
		    else
		        AjaxRequest( urlAjax+"library/php/ExeAjax.php?f=getSelectItemRech&id="+idSelect+'&lang='+lang,'Ajax_getSelectItemRech','');
		}
		listbox.setAttribute('hidden','true');
		
	}	
	function Ajax_getSelectItemRech(result){
		document.getElementById(selectCalque).value=result;
	}
    function Sem_Evalactisem(){
   
    	login=document.getElementById('login_uti').value;
    	mdp=document.getElementById('mdp_uti').value;
    	lbLogin=document.getElementById('lab_login').setAttribute("value",login);
    	if(login=='' || mdp==''){
    		alert('Il faut saisir le login et le mot de passe del.icio.us');
    		return;
    	}
    	AjaxRequest( urlAjax+"library/php/ExeAjax.php?f=Evalactisem&login="+login+"&mdp="+mdp,'Ajax_Evalactisem','');
    }
    function Ajax_Evalactisem(result){
    	if(result==1)
    		ShowMsgErreur('Connection to del.icio.us failed.');
    	if(result==3)
    		ShowMsgErreur('Del.icio.us API access throttled.');
    	if(result==2)
    		ShowMsgErreur('Incorrect del.icio.us username or password');
    	if(result==0){
    		Evalactisem.openWindow("chrome://evalactisem/content/index.xul","Evalactisem",1500,1500 );
    	}
    }
    function Logout(){
    	Evalactisem.openWindow("chrome://evalactisem/content/login.xul","Evalactisem",300,500 );
    	window.opener.close();
    }
	function Sem_getLangLiveMetal(){
		AjaxRequest(urlAjax+"library/php/ExeAjax.php?f=getLangLiveMetal",'Ajax_getLangLiveMetal','');
		
	}
	function Ajax_getLangLiveMetal(result){
		boxRech=document.getElementById('Lang');
		while(boxRech.hasChildNodes()){
				boxRech.removeChild(boxRech.lastChild);
		}
		arrLang=eval('('+result+')');
		radioGroup=document.createElement('radiogroup');
		radioGroup.setAttribute('id','radioLangs');
		radioGroup.setAttribute('orient','horizontal');
		for(i=0;i<arrLang.length;i++){
			radio=document.createElement('radio');
			radio.setAttribute('id','radio_'+arrLang[i]);
			radio.setAttribute('label',arrLang[i]);
			radio.setAttribute('oncommand','selectRadio(this.label);');
			radioGroup.appendChild(radio);
		}
		boxRech.appendChild(radioGroup);
		document.getElementById('radio_fr').setAttribute('selected',"true");
		document.getElementById('fr').setAttribute('checked',"true");
		document.getElementById('radio_en').setAttribute('selected',"false");
		GetFlux(false);
	}
    function selectRadio(value){
    	lang=value;
    }
    function selectChekBox(select){
        arrSelect='[';
        if(document.getElementById('fr').getAttribute("checked"))
    		arrSelect+='"fr"';
    	if(document.getElementById('ar').getAttribute("checked"))
    		arrSelect+=',"ar"';
    	if(document.getElementById('ar').getAttribute("checked"))
    		arrSelect+=',"en"';
    	arrSelect+=']';
    	console.log(eval('('+arrSelect+')'));
    	return arrSelect;
    	
    	
    }
  