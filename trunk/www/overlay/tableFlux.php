<?php
	
	$sTag=$_POST["tag"];
	$sDesc=$_POST["desc"];
	$sUrl=$_POST['url'];
	$sDate=$_POST['date'];
	$sNote=$_POST['note'];
	
	
	$aTag=explode("*",$sTag);
	$aDesc=explode(";",$sDesc);
	$aUrl=explode(";",$sUrl);
	$aDate=explode(";",$sDate);
	$aNote=explode(";",$sNote);
	
	

	

		echo'<listbox id="boxlist"  flex="1" >';
			
			echo'<listhead >';
				echo'<listheader label="Tag"></listheader>';
			    
				echo'<listheader label="desc"></listheader>';
				
				echo'<listheader label="url"></listheader>';
				
				echo'<listheader label="Note"></listheader>';
				
				echo'<listheader label="date"></listheader>';
					
							
			echo'</listhead>';
			echo'<listcols>';
				echo'<listcol flex="1">';
					echo'</listcol>';
				echo'<splitter />';
				echo'<listcol flex="1">';
				echo'</listcol>';
				echo'<splitter />';
				echo'<listcol flex="1">';
				echo'</listcol>';
				echo'<splitter />';
				echo'<listcol flex="1">';
				echo'</listcol>';
				echo'<splitter />';
				echo'<listcol flex="1">';
				echo'</listcol>';
			echo'</listcols>';
		
			for($i=0;$i<sizeof($aTag);$i++)
			{   
			    
				echo('<listitem>');
				echo('<listcell label="'.$aTag[$i].'"/>');
				echo('<listcell label="'.$aDesc[$i].'"/>');
				echo('<listcell label="'.$aUrl[$i].'"/>');
				echo('<listcell label="'.$aNote[$i].'"/>');
				echo('<listcell label="'.$aDate[$i].'"/>');
				
				echo('</listitem>');
			}		    			    
		
		echo'</listbox>';

	
	
?>