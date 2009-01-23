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
               
                case 'AddTrad':
                        $resultat = AddTrad(stripslashes($_GET['codeFlux']),stripslashes ($_GET['codeIeml']));
                        break;
                case 'SupTrad':
                        $resultat = SupTrad(stripslashes ($_GET['codeIeml']),stripslashes ($_GET['codeflux']));
                        break;
              
                case 'Parse':
                        $resultat = Parse($code);
                        break;
                case 'ParserIemlExp':
                        $resultat = ParserIemlExp($code,$_GET['type']);
                        break;
               
                case 'AddPostIemlDelicious':
                	$resultat=AddPostIemlDelicious();
                	break;
                case 'Delet_Compte_Delicious':
               		$resultat=Delet_Compte_Delicious();
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
                case 'GetTreeDeliciousNetwork':
                	    $resultat=$objXul->GetTreeDeliciousNetwork();
                	    break;
                	    
                	    	    
       }
        
        echo $resultat;  

	function GetTreeNoTradUti(){
        global $objSite;
        $xul = new Xul($objSite);
		return $xul->Get_Tree_NoTrad_Uti($_SESSION['iduti']);
	}

	function GetTreeTradUtis(){
        global $objSite;
        $xul = new Xul($objSite);
		return $xul->Get_Tree_Trad_Utis(array($objSite->infos["UTI_TRAD_AUTO"],$_SESSION['iduti']));
		//return $xul->GetTreeTradUtis($_SESSION['iduti']);
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


        function ParserIemlExp($code,$type){
        
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
                
                $liens = $sem->Parser_Ieml_Exp($code);                                
                
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
       
      
	   	function AddPostIemlDelicious(){
	     	global $objSite;
	   		$oDelicious = new PhpDelicious($_SESSION['loginSess'],$_SESSION['mdpSess']);
	     	$bmark= new  BookMark();
	         
	         $bmark->Add_Post_Ieml_Delicious($objSite,$oDelicious);
	         
	    }
    
	   	
	   function CreaCycle($json){
	   		global $objSite;
 			$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
 			return $sem->Crea_Cycle($json);                              
       }
	  
?>
