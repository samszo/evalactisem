var start = new Object();
var end = new Object();


function GetCellSelect(col){

  try {

	//récupère l'onglet sélectionné
  	if (window.parent != self) 
		tb = parent.document.getElementById("tabboxCompare");
	else
		tb = document.getElementById("tabboxCompare");
	
	tab = tb.selectedPanel;
	//récupère l'identifiant du tree
	idTree = "treeExtraction_"+tab.id.substring(3,tab.id.length);

  	if (window.parent != self) 
		tree = parent.document.getElementById(idTree);
	else
		tree = document.getElementById(idTree);
	
	//pour gérer la multisélection
	var numRanges = tree.view.selection.getRangeCount();
	for (var t = 0; t < numRanges; t++){
		tree.view.selection.getRangeAt(t,start,end);
		for (var v = start.value; v <= end.value; v++){
			c = tree.treeBoxObject.columns[col];
			cell = tree.view.getCellText(v,c);
		}
	}
	return cell;
  } catch(ex2){ alert("tree:SetCellSelect:"+ex2); }
}

function SetCellSelect(valeur,col){

  try {

	//récupère l'onglet sélectionné
	tb = document.getElementById("tabboxCompare");
	tab = tb.selectedPanel;
	//récupère l'identifiant du tree
	idTree = "treeExtraction_"+tab.id.substring(3,tab.id.length);

	tree = document.getElementById(idTree);
	//pour gérer la multisélection
	var numRanges = tree.view.selection.getRangeCount();
	for (var t = 0; t < numRanges; t++){
		tree.view.selection.getRangeAt(t,start,end);
		for (var v = start.value; v <= end.value; v++){
			//alert("Item " + tree.view.getCellText(v,c) + " sélectionné.");
			c = tree.treeBoxObject.columns[col];
			tree.view.setCellText(v,c,valeur);
		}
	}
  } catch(ex2){ alert("tree:SetCellSelect:"+ex2); }
}


function GetTreeSelect(idTree,idTrace,colTrace){

  try {
	tree = document.getElementById(idTree);
	//pour gérer la multisélection
	var numRanges = tree.view.selection.getRangeCount();
	for (var t = 0; t < numRanges; t++){
		tree.view.selection.getRangeAt(t,start,end);
		for (var v = start.value; v <= end.value; v++){
			//alert("Item " + tree.view.getCellText(v,c) + " sélectionné.");
			for (var i = 0; i < idTrace.length; i++){
				dump("GetTreeSelect colTrace[i] "+colTrace[i]+"\n");
				dump("GetTreeSelect idTrace[i] "+idTrace[i]+"\n");
				//alert("GetTreeSelect idTrace["+i+"]="+idTrace[i]+" colTrace["+i+"]="+colTrace[i]+"\n")
				c = tree.treeBoxObject.columns[colTrace[i]];
				if(idTrace[i].substring(0,6)=="iframe"){
					//alert(idTrace[i]+tree.view.getCellText(v,c));
					document.getElementById(idTrace[i]).setAttribute("src",tree.view.getCellText(v,c));
				}else
					document.getElementById(idTrace[i]).value=tree.view.getCellText(v,c);
			}
		}
	}
  } catch(ex2){ alert("tree:GetTreeSelect:"+ex2); }
}

function GetTreeIdSelect(idTree,idCol,arrVal){

  try {
	tree = document.getElementById(idTree);
	c = tree.treeBoxObject.columns[idCol];
	var numRanges = tree.view.selection.getRangeCount();
	for (var t = 0; t < numRanges; t++){
		tree.view.selection.getRangeAt(t,start,end);
		for (var v = start.value; v <= end.value; v++){
			var val = tree.view.getCellText(v,c).split("_");
			for (var i = 0; i <= val.length; i++){
				if(val[i]==arrVal)
					return val[i+1] ;			
			}			
		}
	}
  } catch(ex2){ alert("tree:GetTreeValSelect:"+ex2); }
}



function GetTreeValSelect(idTree,idCol){

  try {
  	if (window.parent != self) 
		tree = parent.document.getElementById(idTree);
	else
		tree = document.getElementById(idTree);

	c = tree.treeBoxObject.columns[idCol];
	//pour gérer la multisélection
	var numRanges = tree.view.selection.getRangeCount();
	for (var t = 0; t < numRanges; t++){
		tree.view.selection.getRangeAt(t,start,end);
		for (var v = start.value; v <= end.value; v++){
			var val = tree.view.getCellText(v,c);
		}
	}
	return val;
  } catch(ex2){ alert("tree:GetTreeValSelect:"+ex2); }
}


function Tree_AddItem(parentitem, cells)
{
  try {
	var parent = document.getElementById(parentitem);
	if (!parent){dump("tree:Tree_AddItem:"+parentitem+"\n");return null;}
	var item = document.createElement("treeitem");
	item.setAttribute("id", "treeitem" + cells[0]);
	var row = document.createElement("treerow");
	
	//création des colonnes du tree
	for (var i = 0; i < cells.length; i++) {
		var cell = document.createElement("treecell");
		cell.setAttribute("label", cells[i]);
		row.appendChild(cell);
	}

	item.appendChild(row);

	parent = GetItemOrChildren(parent,item,cells[0]);

	// set open status of the item
	parent.setAttribute("open", "true");
	
  } catch(ex2){ alert("tree:Tree_AddItem:"+ex2); }
 }

 function GetItemOrChildren(parent,item,id)
{
	// we distinguish the case that
	//		the container of the item is empty --> create new treechildren object and append item
	//		a treechildren-object already exists --> get the id and append new item to this one
	if (parent.getAttribute("container") != "true") {
		//alert('no conteneur');
		var children = document.createElement("treechildren");
		parent.setAttribute("container", "true");
		//item.setAttribute("container", "true");
		children.setAttribute("id", "treechildren" + id);
		children.appendChild(item);
		children.setAttribute("open", "true");
		parent.appendChild(children);
	} else {
		//alert('conteneur');
		//	deplacement du nouvel element en fin de liste					
		var container = parent.getElementsByTagName('treechildren')[0];
		try { container.removeChild(item) } catch(e) { }
		container.appendChild(item);
		
	}
	return parent;
}
 
 function GetTreeDom(file)
{
	
  try {
	var tree = document.getElementById(TreeId);
	
	var doc = "<rdf:RDF xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' xmlns:lex='http://lex#'>";
	doc += "<rdf:Description rdf:about='urn:roots'>";
	doc += "<lex:titre>"+file.path+"</lex:titre>";
	doc += "<lex:visible>check_yes</lex:visible>";
	doc += "<lex:icone>images/cell.png</lex:icone>";
	doc += "<lex:file>photo_001</lex:file>";
	doc += "<lex:ID>0</lex:ID>";
	doc += "</rdf:Description>";	

	cID = tree.treeBoxObject.columns[2];
	for (i=0; i<tree.treeBoxObject.view.rowCount; i++)
	{
		IDi = i;//tree.treeBoxObject.view.getCellText(i,cID);
		doc += "<rdf:Description rdf:about='urn:root:"+IDi+"'>";

		for (j=0; j<tree.treeBoxObject.columns.count; j++)
		{
			c = tree.treeBoxObject.columns[j];
			name =tree.treeBoxObject.columns[j].element.getAttribute('label');
			if(name=="visible")
				val = GetVisible(tree, i);
			else
				val = tree.treeBoxObject.view.getCellText(i,c);
			doc += "<lex:"+name+">"+val+"</lex:"+name+">";
		}
		doc += "</rdf:Description>";													   
	}
	//création des hiérarchies
	doc += "<rdf:Seq rdf:about='urn:roots'>";
	doc += "<rdf:li rdf:resource='urn:root:0'/>";
	doc += "</rdf:Seq>";	
	doc += GetHierarchie(tree, 0);
	
	doc += "</rdf:RDF>";

  } catch(ex){ dump(ex); }
	
	return doc;
}

function GetHierarchie(tree, i)
{
	doc = "";
	cID = tree.treeBoxObject.columns[2];
	if(i<tree.treeBoxObject.view.rowCount){
		niv = tree.treeBoxObject.view.getLevel(i);
		isContainer = tree.treeBoxObject.view.isContainer(i);
		dump("-- "+i+" Hiérarchie "+isContainer+" --> "+niv+"\n");
		if(isContainer){
			IDi = i;//tree.treeBoxObject.view.getCellText(i,cID);
			doc += "<rdf:Seq rdf:about='urn:root:"+IDi+"'>";
			//création de la liste du niveau
			for (j=0; j<tree.treeBoxObject.view.rowCount; j++)
			{
				if(tree.treeBoxObject.view.getLevel(j)==(niv+1)){
					//vérifie que la branche est un enfant
					if(tree.treeBoxObject.view.getParentIndex(j)==i){
						IDj = j;//tree.treeBoxObject.view.getCellText(j,cID);
						doc += "<rdf:li rdf:resource='urn:root:"+IDj+"'/>";
					}
				}
			}
			doc += "</rdf:Seq>";
		}
		
		doc += GetHierarchie(tree, i+1);	
	}
	return doc;	
}

function SaveTree(file)
{
	//http://developer.mozilla.org/fr/docs/Extraits_de_code:Fichiers_E/S
	//var tree = document.getElementById(TreeId);
	//var doc = tree.treeBoxObject.view;//GetTreeDom();
	var doc = GetTreeDom(file);

	dump("SaveTree lancée "+file.path+"\n");
  try {

	var serializer = new XMLSerializer();
	var foStream = Components.classes["@mozilla.org/network/file-output-stream;1"]
				   .createInstance(Components.interfaces.nsIFileOutputStream);
	foStream.init(file, 0x02 | 0x08 | 0x20, 0666, 0); // write, create, truncate
	//serializer.serializeToStream(doc, foStream, "");   // rememeber, doc is the DOM tree
	
	foStream.write(doc, doc.length);
	foStream.close();			   	

  } catch(ex){ dump(ex); }

	dump("SaveTree finite\n");
}
 
