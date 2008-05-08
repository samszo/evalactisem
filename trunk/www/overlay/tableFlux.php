<?php
	
	$sTag=$_GET["tag"];
	$sDesc=$_GET["desc"];
	$sUrl=$_GET["url"];
	$sDate=$_GET["date"];
	$sNote=$_GET["note"];
	
	$aTag=explode(";",$sTag);
	$aDesc=explode(";",$sDesc);
	$aUrl=explode(";",$sUrl);
	$aDate=explode(";",$sDate);
	$aNote=explode(";",$sNote);
	//print_r($sNote) ;
	header('Content-type: application/vnd.mozilla.xul+xml');
	echo '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
	?>
	<box id="box" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
    <script >
    	function ChargeUrl(src){
	    	
	    	var iFrame =parent.document.getElementById('webFrame');
    		iFrame.setAttribute("src",src);
    	}
    </script>
    
		<listbox id="boxlist"  flex="1" onselect="ChargeUrl(this.selectedItem.childNodes[2].getAttribute('label'));">
			
			<listhead >
				<listheader label="Tag"></listheader>
				<listheader label="desc"></listheader>
				<listheader label="url"></listheader>
				<listheader label="date"></listheader>
				<listheader label="Note"></listheader>
					
							
			</listhead>
			<listcols>
				<listcol flex="1"></listcol>
				<splitter class="tree-splitter"/>
				<listcol flex="1"></listcol>
				<splitter class="tree-splitter"/>
				<listcol flex="1"></listcol>
				<splitter class="tree-splitter"/>
				<listcol flex="1"></listcol>
				<splitter class="tree-splitter"/>
				<listcol flex="1"></listcol>
				
			</listcols>
		
		<?php
			for($i=0;$i<sizeof($aUrl);$i++)
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
