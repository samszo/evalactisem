<?php
        $ajax = true;
        require_once ("../../param/ParamPage.php");
        //require('../php-delicious/php-delicious.inc.php');
        //charge le fichier de paramètrage
        $objSite->XmlParam = new XmlParam(PathRoot."/param/ParamXul.xml");


        $resultat = "";
        
        if(isset($_POST['f'])){
              $fonction = $_POST['f'];
            
        }else
        if(isset($_GET['f']))
                $fonction = $_GET['f'];
        else 
        		$fonction ='';
        if(isset($_GET['id']))
                $id = $_GET['id'];
        else
                $id = -1;
        if(isset($_GET['code']))
                $code = stripslashes ($_GET['code']);
        else
                $code = -1;
        if(isset($_GET['desc']))
                $desc = $_GET['desc'];
        else
                $desc = -1;
        if(isset($_GET['bookmark']))
                $mbook = stripslashes($_GET['bookmark']);
         else
         		$mbook="toto";
       
		switch ($fonction) {
                case 'AddDictio':
                        $resultat = AddDictio($_GET['libflux'],$_GET['idflux'],$_GET['codeIeml']);
                        break;
                case 'AddTrad':
                        $resultat = AddTrad(stripslashes($_GET['codeFlux']),stripslashes ($_GET['codeIeml']));
                        break;
                case 'SupTrad':
                        $resultat = SupTrad(stripslashes ($_GET['codeIeml']),stripslashes ($_GET['codeflux']));
                        break;
                case 'SetOnto':
                        $resultat = SetOnto($_GET['type'],$_GET['col'],$_GET['id'],$_GET['value']);
                        break;
                case 'Parse':
                        $resultat = Parse($code);
                        break;
                case 'GetGraph':
                        $resultat = GetGraph($code,$_GET['type']);
                        break;
                case 'GraphGet':
                	    $resultat=GraphGet($mbook);
                	    break;
                case 'GetTreeTrad':
        	        	//Pour le débugage
                		if($_GET['debug'])
        	        		$resultat=GetTreeTrad($_GET['flux'],$_GET['trad'],$_GET['descp'],$_GET['type'],$_GET['primary'],$_GET['bdd'],$_GET['couche']);
                		else
        	        		$resultat=GetTreeTrad($_POST['flux'],$_POST['trad'],$_POST['descp'],$_POST['type'],$_POST['primary'],$_POST['bdd'],$_POST['couche']);
        	        	break;
                case 'InsertIemlOnto':
                	   $resultat=InsertIemlOnto($_GET['Iemlcode'],$_GET['Iemllib'],$_GET['Imelparent']);
                	   break;
                case 'GetTreeDictio':
                	   $resultat=GetTreeDictio();
                	   break;
                case 'AddPostIeml':
                	$resultat=AddPostIeml();
                	break;
                case 'Delet_Compte_Delicious':
               		$resultat=Delet_Compte_Delicious();
               		break;
               		
                case 'IemlCycle':
                	if($_GET['debug'])
                		$resultat=IemlCycle($_GET['key']);
                	else
	                	$resultat=IemlCycle($_POST['key']);
                	break;
                case 'TagCloud':
	                	$tg = new TagCloud();
	                	$tg->GetSvg();
                	break;
                case 'CreaCycle':
                	    $resultat=CreaCycle($_GET['json']);
                	    break;  
                case 'GetTreeTradUtis':
                	    $resultat=GetTreeTradUtis();
                	    break;  
                case 'GetTreeNoTradUti':
                	    $resultat=GetTreeNoTradUti();
                	    break;
                	    	    
       }
        
        echo $resultat;  

	function GetTreeNoTradUti(){
        global $objSite;
        $xul = new Xul($objSite);
		return $xul->GetTreeNoTradUti($_SESSION['iduti']);
	}

	function GetTreeTradUtis(){
        global $objSite;
        $xul = new Xul($objSite);
		return $xul->GetTreeTradUtis(array($objSite->infos["UTI_TRAD_AUTO"],$_SESSION['iduti']));
		//return $xul->GetTreeTradUtis($_SESSION['iduti']);
	}
        
        function GetTreeDictio(){
        	 global $objSite;
        	 
        	 $objXul = new Xul($objSite);
        	  $tree=$objXul->GetTree_ieml_onto("ieml");
			  return $tree;
        }
        
        // Ajouter une traduction dans la table ieml_onto et onto_trad
        function AddTrad($codeflux,$codeIeml){

                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
                return $sem->Add_Trad($codeflux,$codeIeml);
                             
 		}
       
        function SupTrad($codeIeml,$codeflux){
        
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
			    //vérifie le partage de traduction
		    	$idTrad = $sem->Add_Trad($codeflux,$codeIeml,$_SESSION['iduti'],true);
		    	if($idTrad){
		    		//vérifie s'il existe une traduction automatique
					if($sem->VerifPartageTrad($idTrad,$objSite->infos["UTI_TRAD_AUTO"])){
	                	$message = $sem->SupPartageTrad($idTrad,$_SESSION['iduti']);
					}else{
	                	$message = $sem->SupPartageTrad($idTrad,$_SESSION['iduti']);
						$message = $sem->Sup_Trad($codeIeml,$codeflux);						
					}
		    	}else{
		    		$message = utf8_encode("Problème lors de la vérification du partage");
		    	}
                
                return $message;
                
        }
                
        
       function Parse($code){
        
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
                return $sem->Parse($code);                              
                
        }


        function GetGraph($code,$type){
        
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
                
                $liens = $sem->GetSvgPie($code);                                
                
                //$svg = $objSite->GetCurl($liens["GraphPrimitive"]);
                //header("Content-Type: image/svg+xml");
                
                return $liens["Graph".$type];

        }
        
		function GraphGet($mbook){
        	$bookmark=new BookMark($mbook);
        	if(TRACE)
        		echo "ExeAjax:GraphGet:bookmark".$bookmark."<br/>";
        	$AgentOnto= new AgentOnto($bookmark);
        	
        	//$bookmark->GetInfos();
        	return $AgentOnto->svgBookmark();
        	
        }
       
       function InsertIemlOnto($Iemlcode,$Iemllib,$Imelparent){
	     	global $objSite;     
	     	$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
	     		     
	     		     $sem->InsertIemlOnto($Iemlcode,$Iemllib,$Imelparent);
     	}
        
	   	function AddPostIeml(){
	     	global $objSite;
	   		$oDelicious = new PhpDelicious($_SESSION['loginSess'],$_SESSION['mdpSess']);
	     	$bmark= new  BookMark();
	         
	         $bmark->MajPostIeml($objSite,$oDelicious);
	         
	    }
    
	   	function Delet_Compte_Delicious(){
	     global $objSite;
	     $oDelicious=$_SESSION['Delicious'];
	     $bmark=new  BookMark();
	         
	         return $bmark->DeletCompteDelicious($objSite,$oDelicious,$_SESSION['iduti'],$_SESSION['loginSess']);
	   
	   }
       
	   function CreaCycle($json){
	   		global $objSite;
 			$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
 			return $sem->CreaCycle($json);                              
       }
	  
?>
