function pf_couleur(num, color){
    document.getElementById("colorpicker" + num).hidePopup();
    document.getElementById("tb_0" + num).value =color;
    document.getElementById("tb_0" + num).inputField.style.backgroundColor=color; 
}

//ajout samszo
function SetDonnees(result,param){
	
	arr = result.split("*");
	document.getElementById("donnees").value = arr[1];
	document.getElementById("noms").value = arr[0];
	res=result;
	
   
}

//fin ajout samszo
function Requette(){

    var req= document.getElementById("selctreq").value;
	
	AjaxRequest("http://localhost/evalactisem/library/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&requette="+req,'SetDonnees','');
}

function pf_dessin(dom_doc)
{




lien='stats.php?large='+escape(document.getElementById("large").value);
lien=lien+'&haut='+escape(document.getElementById("haut").value);
lien=lien+'&titre='+escape(document.getElementById("titre").value);

//deb ajout samszo

//fin ajout samszo
lien=lien+'&donnees='+escape(document.getElementById("donnees").value);
lien=lien+'&noms='+escape(document.getElementById("noms").value);
lien=lien+'&type=nuage';
lien=lien+'&col1='+escape(document.getElementById("tb_01").value);
lien=lien+'&col2='+escape(document.getElementById("tb_02").value);
lien=lien+'&col3='+escape(document.getElementById("tb_03").value);
lien=lien+'&col4='+escape(document.getElementById("tb_04").value);
document.getElementById("webFrame").setAttribute("src",lien);
alert(lien);
}
function pf_init(){
for (var i = 1 ; i < 5 ; i++)
document.getElementById("tb_0" + i.toString()).inputField.style.backgroundColor=document.getElementById("tb_0" + i.toString()).value;

}

function show_tooltip(evt)
{
	var matrix = evt.target.ownerDocument.getElementById("root").getScreenCTM()
	var  decale_x = matrix.e 
	var  decale_y = matrix.f
	var values = document.getElementById("donnees").value.split(";")
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
function StartSelectMenu(id){
var menu= document.getElementById(id);
var selc=menu.selectedItem.value;

var req=document.getElementById("selctreq");
req.value=selc;
alert(selc);
Requette();


}
function DelIiciousTree(){
	var menu= document.getElementById("requette");
	var selct=menu.selectedItem.value;
	
	
	flux = res.split("*");
	
	if((selct=="GetAllBundles")||(selct=="GetAllTags")){
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","overlay/tree.php?box=box2&ParaNom=GetOntoTree&type=flux");
	}else
	if((selct=="GetAllPosts")||(selct=="GetRecentPosts")||(selct=="GetPosts")){
	
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","overlay/tableFlux.php?tag="+flux[0]+"&desc="+flux[1]+"&url="+flux[2]+"&date="+flux[3]+"&note="+flux[4]);
	}else{
		Tree= document.getElementById("treeReq");
		Tree.setAttribute("src","http://www.msn.fr");
	}
}
function DeliciousRepres(){
    var menu= document.getElementById("requette");
	var selct=menu.selectedItem.value;
	flux = res.split("*");
	
}