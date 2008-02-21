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
}
//fin ajout samszo
function Requette(){


	AjaxRequest("http://localhost/mundilogiweb/ieml/RecupFlux.php?login="+document.getElementById("login").value+"&pwd="+document.getElementById("pwd").value+"&tag="+document.getElementById("tag").value,'SetDonnees','');

}

function ieml_onto_Flux(dom_doc){

//Recupper le flux del.icio.us pour  instert dans la base de données

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
lien=lien+'&type='+escape(document.getElementById("type").value);
lien=lien+'&col1='+escape(document.getElementById("tb_01").value);
lien=lien+'&col2='+escape(document.getElementById("tb_02").value);
lien=lien+'&col3='+escape(document.getElementById("tb_03").value);
lien=lien+'&col4='+escape(document.getElementById("tb_04").value);
document.getElementById("webFrame").setAttribute("src",lien);
//alert(lien);
}

function pf_init(){
for (var i = 1 ; i < 5 ; i++)
document.getElementById("tb_0" + i.toString()).inputField.style.backgroundColor=document.getElementById("tb_0" + i.toString()).value;
pf_dessin()
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