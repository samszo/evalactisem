<?php
require('../param/ParamPage.php');
	
?>
<html>
  <head>
    <title>Venn Tag Links</title>
    <script type="text/javascript" src="<?php echo PathWeb;?>library/js/protovis-3.2/protovis-r3.2.js" ></script>
    <script type="text/javascript" src="<?php echo PathWeb;?>library/js/ajax.js" ></script>
    <script type="text/javascript+protovis">

//définition des paramètres
var arrUsers = new Array("esterhasz","fennec_sokoko","luckysemiosis","samueld","wazololo");
var aggTag = "dhyp";

var data = [];

//récupération des valeurs de mise à jour
url = "<?php echo ajaxPathWeb;?>?f=GetUsersTagsDistrib&tag="+aggTag+"&users="+arrUsers.join(",");
var distrib = eval('(' + GetResult(url) + ')');

/* définition des couleurs */
var colorD = pv.Scale.log(distrib.data, function(d) d.nbtag).range("#c9ffff", "#40006f");

	function initSvg(){
		//affichage du svg
	    AppendSVG("Venn5dyna.svg",document.getElementById('Venn'), false);
		
		//mise à jour du svg
		var idSvg, nSvg, lib, c;
		for(var i= 0; i < distrib.data.length; i++)
		{
			//construction de l'identifiant et du libelle de l'élément svg
			idSvg="v";	
			lib="";
			for (var j=0; j<arrUsers.length; j++) {
				if(distrib.data[i][arrUsers[j]]!=""){
					idSvg +="_"+(j+1);
					lib += arrUsers[j]+" et "; 	
				}
			}
			lib = lib.substr(0,lib.length-3)+": "+distrib.data[i].nbtag+" TAG(s)";

			//récupération de l'élément svg
			nSvg = document.getElementById(idSvg);

			//suppression du style par défaut s'il existe
			if(nSvg.hasAttribute("style"))nSvg.removeAttribute("style");

			//calcul de la couleur
			c = colorD(distrib.data[i].nbtag).color;		

			//modification de la couleur de l'élément suivant le nombre de lien
			nSvg.setAttribute("fill", c);
			nSvg.setAttribute("stroke", "#ffffff");
			nSvg.setAttribute("stroke-width", 2);

			//ajout des événements
			nSvg.setAttribute("onmouseover", "document.getElementById('VennSelect').innerHTML='"+lib+"'");
			nSvg.setAttribute("onclick", "initGraph(this.id);");
		}
	}


function calcQuery(id){
	
	//décomposition de l'identifiant svg
	var reg=new RegExp("[_]+", "g");
	var arrQ = id.split(reg);

	//construction des paramètres de la requête
	var Q="";
	//ATTENTION le premier élément du tableau = "v"
	//on commence avec i= 1
	for (var i=1; i<arrQ.length; i++) {
 		Q += arrUsers[arrQ[i]-1] + ",";
	}
	Q = Q.substr(0,Q.length-1);
	
	return Q;	 
}

function initGraph(id){
  //calcul de la requête
  var Q = calcQuery(id);
 
  //récupération des nouvelles valeurs
  var url = "<?php echo ajaxPathWeb;?>?f=GetUsersTagLinks&tag=dhyp&users="+Q;
  data = GetResult(url);

  //mise en forme des données
  data = data.replace(/},{/gi, "}, {");

  //affichage des données
  document.getElementById('fig').innerHTML=data;
  document.getElementById('url').innerHTML='<a href="'+url+'" >'+url+'</a>';
}

</script>
    <style type="text/css">
body {
  margin: 0;
  height: 100%;
  width: 100%;
  font: 14px/134% Helvetica Neue, sans-serif;
}

#center {
  vertical-align: top;
}

#fig {
  vertical-align: top;
  margin: auto;
}

#Venn {
  width: 320px;
  height: 320px;
}

    </style>

  </head>
  <body onload="initSvg()" >

  <div id="center">
  <table>
  <tr>
  <td valign="top">
  	<div id="Venn" ></div>
  	<div id="VennSelect" >VEUILLEZ SELECTIONNER UN ENSEMBLE DE TAG</div>
  </td>
  <td valign="top">
  	<div id="url" ></div>
  	<div id="fig" style="height:420px;width:800px;overflow:auto;">
	</div>
  </td>
  </tr>  
  </table>  
  </div>
  </body>
</html>
