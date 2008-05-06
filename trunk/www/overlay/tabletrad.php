<?php	
require_once ("../param/ParamPage.php");   
    $FluxM=$_GET["FluxM"];
	$MultiTrad=$_GET["MultiTrad"];
	$FluxS=$_GET["FluxS"];
	$SignlTrad=$_GET["SignlTrad"];
	$FluxN=$_GET["FluxN"];
	$DescpM=$_GET["descpM"];
	$DescpS=$_GET["descpS"];
	$mFlux=explode(";",$FluxM);
	$mTrad=explode("*",$MultiTrad);
	$sFlux=explode(";",$FluxS);
	$sTrad=explode(";",$SignlTrad);
	$nFlux=explode(";",$FluxN);
    $sDescp=explode(";",$DescpS);
    $mDescp=explode("*",$DescpM);
	$iduti=$_SESSION['iduti'];
	array_pop ($sFlux);
	$D=array();
	$Desc=array();
	$Del=array();
	header('Content-type: application/vnd.mozilla.xul+xml');
	echo '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
	
	?>
	 <hbox xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"  style="background-color:blue;height:200px;width:200px;">
      <script type="text/javascript" src="../xbl/editableTree/functions.js" />
		<?php
        
       //________________________________________________

		  
		  $chaine=explode("*",VerifExist_onto_trad($iduti));
		  $Desc=explode(";",$chaine[1]);
		  $Tag=explode(";",$chaine[2]);
		  $Trad=array();
		  $Trad=explode(";",$chaine[0]);
		  $j=0;
		  for($i=0;$i<sizeof($Trad);$i++){
		  	if($Trad[$i]!=""){
		  		$T[$j]=$Trad[$i];
		  		$j++;
		  	}
		  	
		  }
		 
		  $r=0;
          
		  if(sizeof($T)==0){
          	for($i=0;$i<sizeof($mFlux)-1;$i++){
	           $sT=explode(";",$mTrad[$i]);
	           $sD=explode(";",$mDescp[$i]);
          	   $mT="";
          	   $mD="";
	           for($j=0;$j<sizeof($sT)-1;$j++){
	                $mT.=$sT[$j].";";
		            $mD.=$sD[$j].";";
		            
		     }
          	 $M=explode(";",$mT);
	         $D=explode(";",$mD);
	         for($j=0;$j<sizeof($M)-1;$j++){
	           $mTra[$i][$j]=$M[$j];
	           $mDesc[$i][$j]=$D[$j];
	         }
          
        }
        }else{
		    
        	for($i=0;$i<sizeof($mFlux)-1;$i++){
	           		$sT=explode(";",$mTrad[$i]);
	                $sD=explode(";",$mDescp[$i]);
	                $mT="";
          	        $mD="";
	                for($j=0;$j<sizeof($sT)-1;$j++){
	                	if(!in_array($sT[$j],$T)){
	                		    $mT.=$sT[$j].";";
		                		$mD.=$sD[$j].";";
		                		$exist="false";
	           			        
	                    }else{ 
	                    		$In[$r]=$mFlux[$i];	
	                    		$exist="true";
	                    		$mT="";
	                            $mD="";
	                    		$r--;
	                    	    break;
	                    	}  
                     
	                }
	           
	           if($exist=="false"){
	           	$M=explode(";",$mT);
	           	$D=explode(";",$mD);
	           	for($j=0;$j<sizeof($M)-1;$j++){
	           		$mTra[$r][$j]=$M[$j];
	           		$mDesc[$r][$j]=$D[$j];
	           	}
	           }
	        $r++;
		  
		 }
        
        }
          
          $j=0;
		 for($i=0;$i<sizeof($mFlux)-1;$i++){
          	if(!in_array($mFlux[$i],$Tag)) {
          		$mflux[$j]= $mFlux[$i]; 
          		$j++;       
          	}
          }
          
          $j=0;
          for($i=0;$i<sizeof($nFlux)-1;$i++){
          	if(!in_array($nFlux[$i],$Tag)) {
          		$nflux[$j]= $nFlux[$i]; 
          		$j++;       
          	}
          }
         
          array_pop($sTrad);
		  array_pop($sDescp);
		  $Des=array_merge($sDescp,$Desc);
		  $sTra=array_merge($sTrad,$T);
		  $sflux= array_merge($sFlux,$Tag);
		  //echo implode(";",$sFlux)."*".implode(";",$sTra)."*".implode(";",$Des);
           print_r($mDesc) ;
    //_________________________________________________
       
	   if(sizeof($sflux)>=1){
	   	
	   	Tree($sflux,$sTra,$Des,"Signl_Trad","true",$T);  
	   }
        
         if(sizeof($mflux)>=1){
	    	 Tree($mflux,$mTra,$mDesc,"Multi_Trad","true",$T);
         }
         if(sizeof($nflux)>=1){
	    	Tree($nflux,"","","No_Trad","false",$T);
		}
        
	   ?>
	</hbox>	
	<?php
	
	
 function Tree($flux,$trad,$descp,$type,$primary,$bdd){
    	echo'<vbox  style="background-color:blue;" align="center">';	
    	echo'<label value="'.$type.'" style="font:arial;size:10;color:yellow" />';
    	echo'<box style="height:400px;width:100px;">';
    	echo'<tree context="clipmenu"			
			enableColumnDrag="true"
			fctStart="startEditable"
			fctSave="saveEditable"
			fctInsert="startInsert"
			fctDelete="startDelete"
			fctSelect="startSelect"
			typesource="'.$type.'"	
			id="'.$type.'" ';
			if($type=="No_Trad"){
				echo ' onselect="Select_NoTrad(\''.$type.'\');">'; 
			}else{
				echo ' onselect="Select_Trad(\''.$type.'\');">';
			}
			
    		if($type=="No_Trad"){
    			echo'<treecols >';
	  			 echo'<treecol id="treecol_Tagdel"  primary="'.$primary.'" label="Tag Delicious"  width="120" />';
	  		     echo'<splitter class="tree-splitter"/>';
                 echo'</treecols>';
	  		   echo'<treechildren>';  
	  		   for($i=0;$i<sizeof($flux);$i++){
                    echo'<treeitem >';
                        echo'<treerow>';
                        echo'<treecell label="'.$flux[$i].'"/>' ;
                        echo'</treerow>';
	  		        echo'</treeitem>'; 
	  		   }
	  		   echo'</treechildren>';
	  		   echo'</tree>';
            }else{
	  		echo'<treecols >';
	  			 echo'<treecol id="treecol_Tagdel"  primary="'.$primary.'" label="Tag Delicious"  width="120" />';
	  		     echo'<splitter class="tree-splitter"/>';
	  			 echo'<treecol id="treecol_descp"  label="Description"  width="120" />';
	  			 echo'<splitter class="tree-splitter"/>';
	  			 echo'<treecol id="treecol_'.$type.'"  label="Traduction" width="120" />';
	  		    echo'</treecols>';
	  		echo'<treechildren>';    
                    for($i=0;$i<sizeof($flux);$i++){
                    	echo'<treeitem container="true" open="true">';
                        	 	 echo'<treerow>';
                        	 	 echo'<treecell label="'.$flux[$i].'"/>' ;
                        	  	 echo'</treerow>';
                        	  	 if($type=="Signl_Trad"){
                        	  		//echo $type;
                        	  		echo'<treechildren>';
                        	  			echo'<treeitem >';	
                        	  			  if(in_array($trad[$i],$bdd)){
                        	  			  	$prop="utilisateur";
                        	  			  }else{
                        	  			  	$prop="dictio";
                        	  			  }
                        	  			  echo'<treerow properties="'.$prop.'">';
                        	  					echo'<treecell label=""  />' ;	
                        	  				    echo'<treecell label="'.$descp[$i].'"/>' ;	
                        	  				  	echo'<treecell label="'.$trad[$i].'"/>' ;
                        	  				  echo'</treerow>';
                        	  			echo'</treeitem>';
                        	  		echo'</treechildren>';
                        	  	}
                        	  	if($type=="Multi_Trad"){
                        	  		echo'<treechildren>';
                        	  		for($j=0;$j<sizeof($trad[$i]);$j++){
                        	  			echo'<treeitem >';	
                        	  				echo'<treerow>';
                        	 		 			echo'<treecell label=""/>' ;	
                        	  				    echo'<treecell  label="'.$descp[$i][$j].'" />' ;	
                        	  					echo'<treecell  label="'.$trad[$i][$j].'"/>' ;
                        	  			    echo'</treerow>';
                        	  			echo'</treeitem>';
                        	  		
                        	  		}
                        	  		echo'</treechildren>';
                        	  	}
                        	  	echo'</treeitem>';
                        }
	  					
	  			echo'</treechildren>';
	  		echo'</tree>';
            }
	    echo'</box>';
	  	echo'</vbox>';
      
    }
	
    
    
    function VerifExist_onto_trad($iduti){
    	global $objSite;	
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		        $db->connect();   
                	// requête pour vérifier l'existence de la traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='Tree_dynamique']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from = str_replace("-iduti-", $iduti, $Q[0]->from);
                $sql = $Q[0]->select.$from;
               
                $result = $db->query($sql);
                $db->close();
    			while($reponse=mysql_fetch_array($result)){
    				$Trad.=$reponse[1].";";
    				$Desc.=$reponse[2].";";
    				$Tag.=$reponse[0].";";
    			}
    			
    			return $Trad."*".$Desc."*".$Tag;
               
     }     

    
    
    ?>