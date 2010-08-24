<?php
require('../param/ParamPage.php');
?>
<html>
  <head>
    <title>Venn Matrix Tag Links</title>
    <script type="text/javascript" src="<?php echo PathWeb;?>library/js/protovis/protovis-r3.2.js" ></script>
    <script type="text/javascript" src="<?php echo PathWeb;?>library/js/ajax.js" ></script>
<script type="text/javascript+protovis">
	var ajaxPathWeb="<?php echo ajaxPathWeb;?>";
	var PathWeb="<?php echo PathWeb;?>";
	var arrUsers = new Array("esterhasz","fennec_sokoko","luckysemiosis","samueld","wazololo");
	var aggTag = "dhyp";

<?php require('../library/js/protovis/VennMatrix.js'); ?>

</script>
<link type="text/css" rel="stylesheet" href="<?php echo PathWeb;?>CSS/VennMatrix.css"/>

  </head>
  <body onload="initSvg()" >

  <div id="center">
  <table>
  <tr>
  <td valign="top">
  	<div>Cette page pr&eacute;sente les tags Delicious<br/>
  	utilis&eacute;s par 5 enseignants du d&eacute;partement <br/>
  	<a href="http://hypermedia.univ-paris8.fr/" target="_blank" >hyperm&eacute;dia de l'universit&eacute; Paris 8</a>
	<br/>
	pour informer leurs &eacute;tudiants.
	<br/><br/>
  	Pour visualiser les co-occurrences<br/>
    <div> veuillez choisir un type de visualisation : 
		<form>
			<select id="typeVisu" onchange="changeType(this.value);">
			  <option>arc</option>
			  <option>matrix</option>
			</select>
		</form>
    </div>
  	et cliquer sur le diagramme ci-dessus</div>
  	<div id="Venn" ></div>
  	<div id="VennSelect" ></div>
  	<div id= "DistribLegende">
  		<script type="text/javascript+protovis">
<?php require('../library/js/protovis/VennMatrixLegende.js'); ?>
		</script>
  	</div>
	<div id="distribData"></div>
  </td>
  <td valign="top">
    <div id="figTitre"></div>
    <div id="figLegende">
  		<script type="text/javascript+protovis">
<?php require('../library/js/protovis/VennMatrixVisLegende.js'); ?>
		</script>
    </div>
    <br/>

  	<div id="figM">
    <script type="text/javascript+protovis">
<?php require('../library/js/protovis/VennMatrixVis.js'); ?>

//pour les tests
//initGraph("v_1_2_3_4_5", "TEST");
</script>

  </div>
  </td>
  </tr>  
  </table>  
  </div>
  </body>
</html>
