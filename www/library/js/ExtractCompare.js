function ExtractCompare(treeSelect, nomParent) {

  try {
  	 	
    this.treeSelect = treeSelect;
    this.boxParent = document.getElementById('box'+nomParent);

  	//vérifie que le tabbox existe
	t = document.getElementById("tabsCompare");
	if(!t){
		tabb = document.createElement("tabbox");
		tabb.setAttribute("flex",1);
		tabs = document.createElement("tabs");
		tabs.setAttribute("id","tabsCompare");
		tabb.appendChild(tabs);
		tabp = document.createElement("tabpanels");
		tabp.setAttribute("id","panelsCompare");
		tabb.appendChild(tabp);
		this.boxParent.appendChild(tabb);
	}

    this.popupset = document.getElementById('genPopUp');
    this.tabboxParent = document.getElementById('tabbox'+nomParent);
    this.tabsParent = document.getElementById('tabs'+nomParent);
    //alert(tabsParent+" "+this.tabsParent);
    this.panelsParent = document.getElementById('panels'+nomParent);
    //alert(panelsParent+" "+this.panelsParent);

	//récupération des valeurs sélectionnées
	var numRanges = this.treeSelect.view.selection.getRangeCount();
	this.treeSelect.view.selection.getRangeAt(0,start,end);
	for (var v = start.value; v <= end.value; v++){
		c = this.treeSelect.treeBoxObject.columns[0];
		this.idChoix = this.treeSelect.view.getCellText(v,c);
		c = this.treeSelect.treeBoxObject.columns[1];
		this.nomChoix = this.treeSelect.view.getCellText(v,c);
		c = this.treeSelect.treeBoxObject.columns[2];
		this.FicChoix = this.treeSelect.view.getCellText(v,c);
		c = this.treeSelect.treeBoxObject.columns[4];
		this.iframeBrute = this.treeSelect.view.getCellText(v,c);
	}

    this.GetControls = function() {
	  try {
		
		//ne fait rien si c'est racine de sélectionné
		if(this.idChoix==1 || this.idChoix.substring(0,4)!="tree"){
			alert('Veuillez choisir un fichier');
			return;			
		}
		//création du popup
		//this.AddNewPopUp();
		
		//vérifie que le panel n'existe pas déjà
		p = document.getElementById("tp_"+this.idChoix);
		if(p)return false;
		
		//création de la  tab	
		tab = document.createElement("tab");
		tab.setAttribute("label",this.nomChoix);
		this.tabsParent.appendChild(tab);

		//création de la box iframe
		bi = document.createElement("vbox");
		bi.setAttribute("flex","1");		
		bi.setAttribute("style","background-color: white");		
		i = document.createElement("iframe");
		i.setAttribute("flex","1");		
		i.setAttribute("src",this.iframeBrute);
		bi.appendChild(i);

		//création de la box conteneur
		b = document.createElement("vbox");
		b.setAttribute("flex","1");		

		//création des labels
		l = document.createElement("label");
		l.setAttribute("value","Fichier Choisi : ");
		b.appendChild(l);
		l = document.createElement("label");
		l.setAttribute("id","idChoix_"+this.idChoix);
		l.setAttribute("value",this.idChoix);
		b.appendChild(l);
		l = document.createElement("label");
		l.setAttribute("id","nomChoix_"+this.idChoix);
		l.setAttribute("value",this.nomChoix);
		b.appendChild(l);
		l = document.createElement("label");
		l.setAttribute("id","FicChoix_"+this.idChoix);
		l.setAttribute("value",this.FicChoix);
		b.appendChild(l);
		
		/*création du menulist
		l = document.createElement("label");
		l.setAttribute("value","Choisissez un modèle d'extraction : ");
		b.appendChild(l);		
		ml = document.createElement("menulist");
		ml.setAttribute("id","mnuModExtraction_"+this.idChoix);
		mpo = document.createElement("menupopup");
		mi = document.createElement("menuitem");
		mi.setAttribute("label","style");
		mi.setAttribute("value","style");
		mpo.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("label","texte");
		mi.setAttribute("value","texte");
		mpo.appendChild(mi);
		ml.appendChild(mpo);
		b.appendChild(ml);
		*/

		//création de la box des boutons
		bbt = document.createElement("hbox");
		//bbt.setAttribute("flex","1");		
		//création du bouton d'ajout du modèle
		bt = document.createElement("button");
		bt.setAttribute("label","Enregistrer le modèle");
		bt.setAttribute("oncommand","SauveModele('"+this.nomChoix+"','"+this.idChoix+"');");
		bbt.appendChild(bt);
		//création du bouton chargement du modèle
		bt = document.createElement("button");
		bt.setAttribute("label","Charger le modèle");
		bt.setAttribute("oncommand","ChargeModele();");
		bbt.appendChild(bt);
		//création du bouton exécuter du modèle
		bt = document.createElement("button");
		bt.setAttribute("label","Executer le modèle");
		bt.setAttribute("oncommand","AppliMotClef();");
		bbt.appendChild(bt);
		b.appendChild(bbt);

		//création du tree
		t = document.createElement("tree");
		t.setAttribute("flex","1");
		t.setAttribute("id","treeExtraction_"+this.idChoix);
		//t.setAttribute("context","cm_"+this.idChoix);
		t.setAttribute("context","clipmenu");
		
		tcs = document.createElement("treecols");
		tc = document.createElement("treecol");
		tc.setAttribute("id","id");
		tc.setAttribute("flex","1");
		tcs.appendChild(tc);
		sp = document.createElement("splitter");
		sp.setAttribute("class","tree-splitter");
		tcs.appendChild(sp);
		tc = document.createElement("treecol");
		tc.setAttribute("id","treecol_valeur");
		tc.setAttribute("flex","1");
		tc.setAttribute("label","valeur");
		tcs.appendChild(tc);
		sp = document.createElement("splitter");
		sp.setAttribute("class","tree-splitter");
		tcs.appendChild(sp);
		tc = document.createElement("treecol");
		tc.setAttribute("id","treecol_wstyle");
		tc.setAttribute("flex","1");
		tc.setAttribute("label","Word Style");
		tcs.appendChild(tc);
		sp = document.createElement("splitter");
		sp.setAttribute("class","tree-splitter");
		tcs.appendChild(sp);
		tc = document.createElement("treecol");
		tc.setAttribute("id","treecol_desc");
		tc.setAttribute("flex","1");
		tc.setAttribute("label","type");
		tcs.appendChild(tc);
		sp = document.createElement("splitter");
		sp.setAttribute("class","tree-splitter");
		tcs.appendChild(sp);
		tc = document.createElement("treecol");
		tc.setAttribute("id","treecol_nstyle");
		tc.setAttribute("flex","1");
		tc.setAttribute("label","New style");
		tcs.appendChild(tc);
		sp = document.createElement("splitter");
		sp.setAttribute("class","tree-splitter");
		tcs.appendChild(sp);
		tc = document.createElement("treecol");
		tc.setAttribute("id","treecol_nGM");
		tc.setAttribute("flex","1");
		tc.setAttribute("label","Groupe mot");
		tcs.appendChild(tc);
		sp = document.createElement("splitter");
		sp.setAttribute("class","tree-splitter");
		tcs.appendChild(sp);
		tc = document.createElement("treecol");
		tc.setAttribute("id","treecol_nMC");
		tc.setAttribute("flex","1");
		tc.setAttribute("label","Mot-clef");
		tcs.appendChild(tc);
		t.appendChild(tcs);
		
		//Ajout de la première valeur
		tch = document.createElement("treechildren");
		ti = document.createElement("treeitem");
		ti.setAttribute("id","treeRootExtraction_"+this.idChoix);
		tr = document.createElement("treerow");
		tcel = document.createElement("treecell");
		tcel.setAttribute("label","1");
		tr.appendChild(tcel);
		tcel = document.createElement("treecell");
		tcel.setAttribute("label","racine");
		tr.appendChild(tcel);
		tcel = document.createElement("treecell");
		tcel.setAttribute("label","");
		tr.appendChild(tcel);
		tcel = document.createElement("treecell");
		tcel.setAttribute("label","");
		tr.appendChild(tcel);
		tcel = document.createElement("treecell");
		tcel.setAttribute("label","");
		tr.appendChild(tcel);
		tcel = document.createElement("treecell");
		tcel.setAttribute("label","");
		tr.appendChild(tcel);
		tcel = document.createElement("treecell");
		tcel.setAttribute("label","");
		tr.appendChild(tcel);
		ti.appendChild(tr);
		tch.appendChild(ti);
		t.appendChild(tch);
		b.appendChild(t);

		//ajoute un splitter
		s = document.createElement("splitter");
		b.appendChild(s);

		//ajoute un textbox de trace
		txb = document.createElement("textbox");
		txb.setAttribute("id","traceExtra_"+this.idChoix);
		txb.setAttribute("hidden","true");
		txb.setAttribute("multiline","true");
		txb.setAttribute("row","16");
		b.appendChild(txb);
		
		//création du panel	conteneur
		pan = document.createElement("tabpanel");
		pan.setAttribute("id","tp_"+this.idChoix);
		//ajoute l'iframe
		pan.appendChild(bi);
		//ajoute un splitter
		pan.appendChild(s);
		//ajoute l'extact
		pan.appendChild(b);
		this.panelsParent.appendChild(pan);
		
		//active l'onglet
		this.tabsParent.selectedItem=tab;
		
	  	
	  } catch(ex2){alert("ExtractCompare:GetControls:"+ex2);dump("::"+ex2+"\n");}
    } 


    this.AddNewPopUp = function() {
	  try {

		pp = document.createElement("popup");
		pp.setAttribute("id","cm_"+this.idChoix);
		pp.setAttribute("onpopupshowing","javascript:;");

		mi = document.createElement("menuitem");
		mi.setAttribute("label","Titre principal");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		pp.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("label","Titre1");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		pp.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("label","Titre2");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		pp.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("label","Titre3");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		pp.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		mi.setAttribute("label","Titre4");
		pp.appendChild(mi);
		
		m = document.createElement("menu");
		m.setAttribute("label","Styles");
		m.appendChild(pp);

		this.popupset.appendChild(m);		

		pp = document.createElement("popup");
		pp.setAttribute("id","cm1_"+this.idChoix);
		pp.setAttribute("onpopupshowing","javascript:;");

		mi = document.createElement("menuitem");
		mi.setAttribute("label","Titre principal");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		pp.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("label","Titre1");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		pp.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("label","Titre2");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		pp.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("label","Titre3");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		pp.appendChild(mi);
		mi = document.createElement("menuitem");
		mi.setAttribute("oncommand","SetCellSelect('treeExtraction_"+this.idChoix+"',this.label,4);");
		mi.setAttribute("label","Titre4");
		pp.appendChild(mi);
		
		m = document.createElement("menu");
		m.setAttribute("label","Styles");
		m.appendChild(pp);

		this.popupset.appendChild(m);		

		
	  } catch(ex2){alert("ExtractCompare:GetControls:"+ex2);dump("::"+ex2+"\n");}
    } 


  } catch(ex2){alert("ExtractCompare::"+ex2);dump("::"+ex2+"\n");}
		
} 