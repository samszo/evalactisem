<?php
	//$flux=$_GET["result"];
$flux="ontologie; ieml; Xul; system; ieml; ieml; *)i( interstices - Ontologies informatiques; http://isdm.univ-tln

.fr/PDF/isdm28/isdm28.pdf; Argument Choix de Xul; Web Sémantique:PagePrincipale; IEML; ieml; *http:/

/interstices.info/display.jsp?id=c_17672; http://isdm.univ-tln.fr/PDF/isdm28/isdm28.pdf; http://ljouanneau

.com/blog/2008/02/14/757-adobe-air-vs-xulrunner-xulrunner-gagne-chez-flickr; http://websemantique.org

/PagePrincipale; http://www.ieml.org/; http://www.ieml.org/spip.php?rubrique33; *; ; ; ; ; 

; *2008-01-21 13:05:35; 2008-01-19 20:46:26; 2008-01-18 14:35:45; 2008-01-15 10:50:20; 2008-01-15 10

:21:59; 2008-01-15 10:20:55; ";
   
   	$chain=explode("*",$flux);
	
	$sTag=$chain[0];
	$sDesc=$chain[1];
	$sUrl=$chain[2];
	$sDate=$chain[3];
	$sNote=$chain[4];
	$aTag=explode(";",$sTag);
	$aDesc=explode(";",$sDesc);
	$aUrl=explode(";",$sUrl);
	$aDate=explode(";",$sDate);
	$aNote=explode(";",$sNote);
	
	header('Content-type: application/vnd.mozilla.xul+xml');
	?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
	<box id="box" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
		<listbox id="boxlist" onselect="startSelectTab();" flex="1">
			
			<listhead >
				<listheader label="Tag"></listheader>
				<listheader label="desc"></listheader>
				<listheader label="url"></listheader>
				<listheader label="date"></listheader>
				<listheader label="Note"></listheader>
					
							
			</listhead>
			<listcols>
				<listcol flex="1"></listcol>
				<listcol flex="1"></listcol>
				<listcol flex="1"></listcol>
				<listcol flex="1"></listcol>
				<listcol flex="1"></listcol>
				
			</listcols>
		
		<?php
			for($i=0;$i<sizeof($aDate);$i++)
			{   
			    
				echo('<listitem>');
				echo('<listcell label="'.$aTag[$i].'"/>');
				echo('<listcell label="'.$aDesc[$i].'"/>');
				echo('<listcell label="'.$aUrl[$i].'"/>');
				echo('<listcell label="'.$aNote[$i].'"/>');
				echo('<listcell label="'.$aDate[$i].'"/>');
				
				echo('</listitem>');
			}		    			    
		?>
		</listbox>
	</box>
