<?php	
require_once ("param/ParamPage.php");   
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
	$T=array();      
	      $chaine=explode("*",$objXul->VerifExist_onto_trad($iduti));
		  $Desc=explode(";",$chaine[1]);
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
	           $mTra[$r][$j]=$M[$j];
	           $mDesc[$r][$j]=$D[$j];
	         }
             $r++;
          }
        }else{
		    
        	for($i=0;$i<sizeof($mFlux)-1;$i++){
	           		$sT=explode(";",$mTrad[$i]);
	                $sD=explode(";",$mDescp[$i]);
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
          	if(!in_array($mFlux[$i],$Desc)) {
          		$mflux[$j]= $mFlux[$i]; 
          		$j++;       
          	}
          }
          
          $j=0;
          for($i=0;$i<sizeof($nFlux)-1;$i++){
          	if(!in_array($nFlux[$i],$Desc)) {
          		$nflux[$j]= $nFlux[$i]; 
          		$j++;       
          	}
          }
         
          array_pop($sTrad);
		  array_pop($sDescp);
		  $Des=array_merge($sDescp,$Desc);
		  $sTra=array_merge($sTrad,$T);
		  $sflux= array_merge($sFlux,$Desc);
		  array_pop($sflux);
	header('Content-type: application/vnd.mozilla.xul+xml');
    
	?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<?xul-overlay href="overlay/popupset.php?f=1"?>
<?xul-overlay href="overlay/tree.php?box=box1&ParamNom=GetOntoTree"?>
<?xml-stylesheet rel="stylesheet" href="xbl/editableTree/demo.css" type="text/css" title="css"?>
<?xml-stylesheet rel="stylesheet" href="tree.css" type="text/css" title="css"?>

<window id="ieml-global" title="IEML-10eF v0.1 - information economy meta language - Dixième Famille" orient="horizontal" left="0" top="0" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<script language="JavaScript" type="application/x-javascript" src="js/ajax.js"/>
    <script src="js/TradTagIeml.js"/>
    <script src="js/histogrammes.js"/>
    <popupset id="popupset">
	</popupset>
	<vbox id="traduction"  style="height:600px;width:1000px;"> 
		<hbox >
			<hbox xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"  style="background-color:blue;">
	      		<?php
					if(sizeof($sflux)>=1){
	   					$objXul->Tree($sflux,$sTra,$Des,"Signl_Trad","true",$T);  
					}
        
                   	if(sizeof($mflux)>=1){
	    	 			$objXul->Tree($mflux,$mTra,$mDesc,"Multi_Trad","true",$T);
        		   	}
        		  	if(sizeof($nflux)>1){
	    				$objXul->Tree($nflux,"","","No_Trad","false",$T);
				  	}
	     
		        ?>
			</hbox>	
	        <splitter collapse="before" resizeafter="farthest">
				<grippy/>
			</splitter>
	        <vbox id="box3">
			<hbox >
				<vbox >
					<vbox >
						<label value="Langage : ieml"/>
					    <label id="id-trad-ieml" hidden="true"/>
						<label value="code :"/><label id="code-trad-ieml"  />
						<label value="descriptif : "/><label id="lib-trad-ieml"  />
					</vbox>
					</vbox>
					<vbox >
						<vbox>
							<label id="trad-Sup-message" />			
							<label id="trad-message" />
						</vbox>
						<vbox  >
							<button label="Ajouter une traduction" oncommand="AddTrad();"/>	
							<button label="Supprimer une traduction" oncommand="SupTrad();"/>				
							
						</vbox>
					</vbox>
				<vbox >
				<vbox flex="1">
					<label value="Langage : flux"/>
				    <label id="id-trad-flux" hidden="true"/>
					<label value="code :"/><label id="code-trad-flux"  />
				    <label value="descriptif : "/><label id="lib-trad-flux"  />
				</vbox>
			</vbox>
		</hbox>
	 </vbox>
	<splitter collapse="before" resizeafter="farthest">
			<grippy/>
	</splitter>
	<box id="box1" style="height:400px;width:300px;" ></box>
	</hbox>
</vbox>
		
</window>