<?php
	$FluxM=$_GET["FluxM"];
	$MultiTrad=$_GET["MultiTrad"];
	$FluxS=$_GET["FluxS"];
	$SignlTrad=$_GET["SignlTrad"];
	$FluxN=$_GET["FluxN"];
	$mFlux=explode(";",$FluxM);
	$mTrad=explode("*",$MultiTrad);
	$sFlux=explode(";",$FluxS);
	$sTrad=explode(";",$SignlTrad);
	$nFlux=explode(";",$FluxN);
    header('Content-type: application/vnd.mozilla.xul+xml');
    //print_r($nFlux);
	?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
	 <hbox xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"  style="background-color:blue;height:200px;width:200px;">
	   <?php
       
	   if(sizeof($sFlux)>1){
	     	Tree($sFlux,$sTrad,"Singl Trad","true");
         }
         if(sizeof($mFlux)>1){
	    	 Tree($mFlux,$mTrad,"Multi Trad","true");
         }
         if(sizeof($nFlux)>1){
	    	 Tree($nFlux,"","No Trad","false");
		}
        
	   ?>
	</hbox>	
	<?php
    function Tree($flux,$trad,$type,$primary){
    	echo'<hbox >';
	  	echo'<tree  class="Multi_trad">';
	  		echo'<treecols >';
	  			echo'<treecol label="'.$type.'" primary="'.$primary.'" width="120" />';
	  		echo'</treecols>';
	  		echo'<treechildren>';    
                    for($i=0;$i<sizeof($flux)-1;$i++){
                        echo'<treeitem container="true" open="false">';
                        	 	 echo'<treerow>';
                        	 		 echo'<treecell label="'.$flux[$i].'"/>' ;
                        	  	echo'</treerow>';
                        	  	if($type=="Singl Trad"){
                        	  		echo'<treechildren>';
                        	  			echo'<treeitem >';	
                        	  				echo'<treerow>';
                        	 		 			echo'<treecell label="'.$trad[$i].'"/>' ;
                        	  				echo'</treerow>';
                        	  			echo'</treeitem>';
                        	  		echo'</treechildren>';
                        	  	}
                        	  	if($type=="Multi Trad"){
                        	  		echo'<treechildren>';
                        	  		$Tradexp=explode(";",$trad[$i]);
                        	  		for($j=0;$j<sizeof($Tradexp)-1;$j++){
                        	  			echo'<treeitem >';	
                        	  				echo'<treerow>';
                        	 		 			echo'<treecell label="'.$Tradexp[$j].'"/>' ;
                        	  				echo'</treerow>';
                        	  			echo'</treeitem>';
                        	  		
                        	  	}
                        	  	
                        	  		
                        	  		echo'</treechildren>';
                        	  	}
                        	  	echo'</treeitem>';
                        }
	  					
	  			echo'</treechildren>';
	  		echo'</tree>';
	  echo'</hbox>';
      
    }
	?>
