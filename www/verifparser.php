<?php
	$xml = simplexml_load_file('http://evalactisem.ieml.org/entries/');

	//création du tableau html
	$tab = '<TABLE WIDTH=100% BORDER=1 BORDERCOLOR="#000000" CELLPADDING=4 CELLSPACING=0>
	<COL WIDTH=85*>
	<COL WIDTH=85*>
	<COL WIDTH=85*>
	<TR VALIGN=TOP>
		<TH WIDTH="30%">
			<P>IEML</P>
		</TH>
		<TH WIDTH="10%">
			<P>Valid</P>
		</TH>
		<TH WIDTH="60%">
			<P>Response</P>
		</TH>
	</TR>';
	//ajoute les expressions du dictionnaire
	foreach($xml->entry as $e){
		//$url = "http://www.mundilogiweb.com/evalactisem/library/php/ExeAjax.php?f=Parse&code=";
		$url = "http://localhost/evalactisem/library/php/ExeAjax.php?f=Parse&code=";
		$tab .= "<TR VALIGN=TOP>
		<TD WIDTH='30%'>
			<a href='".$e->link."'>".$e->expression."</a>
		</TD>
		<TD WIDTH='10%' >
			<P id='valid_".$e->id."' onclick=\"VerifExpression('".$url."',".$e->id.");\" >TEST</P>
		</TD>
		<TD WIDTH='60%' id='".$e->id."' ieml=\"".$e->expression."\" >
		</TD>
	</TR>";		
	}
 	$tab .= '</TABLE>';
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>VerifParser</title>
<SCRIPT LANGUAGE='JavaScript'>

	//appel du service 
	function VerifExpression(url,id) {
	  try {
		document.documentElement.style.cursor = "wait";	  
	  	
	  	var doc = document.getElementById(id);
		url += doc.getAttribute('ieml'); 

		var p = new XMLHttpRequest();
		p.onload = null;
		p.open("GET", encodeURI(url), false);
		p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		p.send(null);
	
		if (p.status != "200" ){
			document.getElementById("valid_"+id).value="<P STYLE='background: #ff0000'>KO</P>";
		    alert("Réception erreur " + p.status);
		}else{
		    var response = p.responseText;

		    //création de l'ihm;
			var ihm="<a href=\"javascript:toggle('show_" + id + "')\">Show/Hide</a>"
				+"<div id='show_"+id+"' style='display:block' >" + response + "</div>";

			//vide le conteneur
			while(doc.hasChildNodes())
				doc.removeChild(doc.firstChild);
			
			//ajoute l'ihm à la réponse
			doc.innerHTML=ihm;
			
			//vérifie s'il y a des erreurs
			var exp = new RegExp();
		  	exp.compile("ERRORS:");
		  	var r =	exp.exec(response);	
			if(r){
				document.getElementById("valid_"+id).innerHTML="<P STYLE='background: #ff0000'>KO</P>";
			}else{
				document.getElementById("valid_"+id).innerHTML="<P STYLE='background: #00ae00'>OK</P>";
			}			
		}
	   } catch(ex2){alert("verifparser:AppendResult:"+ex2);}
	   document.documentElement.style.cursor = "auto";
	}    

	// Affichage / masquage d'un objet spécifié par son attribut id
	function toggle(object_id){
		var obj=document.getElementById(object_id)	
	 	if(obj.style.display == 'block'){
	  		obj.style.display='none'
	 	}else{
	  		obj.style.display='block'
	  	}
	 }
    
</SCRIPT>

</head>
<body>
<?php echo $tab; ?>
</body>
</html>