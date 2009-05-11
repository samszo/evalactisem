<?php
        $ajax = true;
        require_once ("../../param/ParamPage.php");
        //charge le fichier de param�trage
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
				case 'GetTreeTradAuto':
					$resultat = GetTreeTradAuto($_GET['codeFlux'],$lang);
					break;
				case 'GetExaIEML':
			        if(isset($_GET['r']))
			                $r = $_GET['r'];
			         else
			         		$r=10000;
					$resultat = GetExaIEML($_GET['codeIeml'],$r,$ShowAll);
					break;
				case 'VerifExpIEML':
					$resultat = VerifExpIEML(stripslashes($_GET['codeIeml']),stripslashes ($_GET['libIeml']));
					break;
                case 'AddTrad':
                        $resultat = AddTrad(stripslashes($_GET['codeFlux']),stripslashes($_GET['codeIeml']),stripslashes($_GET['libIeml']),$_GET['lang']);
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
                	    $resultat=GetTreeTradUtis($_GET['lang']);
                	    break;  
                case 'GetTreeNoTradUti':
                	    $resultat=GetTreeNoTradUti($_GET['lang']);
                	    break;
                case 'GetTreeDeliciousNetwork':
  
                	    $resultat=$objXul->GetTreeDeliciousNetwork($oDelicious,$_GET['login']);
                	    break;
                case 'GetTreeIemlOnto':
                	    $resultat= GetTreeIemlOnto();
                	    break;
                case 'recherche':
                	    $resultat= recherche($_GET['query'],$_GET['type'],$_GET['lang']);
                	    break;
                case 'Evalactisem':
                	    $resultat= Evalactisem($_GET['login'],$_GET['mdp']);
                	    break;
                case 'GetFlux':
                	    $resultat= GetFlux($_GET['lang'],stripslashes($_GET['getFlux']));
                	    break;
                 case 'getLangLiveMetal':
                	    $resultat= getLangLiveMetal();
                	    break;
                 case 'getSelectItemRech':
                	    $resultat= getSelectItemRech($_GET['id'],$_GET['lang']);
                	    break;
                 case 'SetSession':
                	    SetSession($_GET['lib'],$_GET['val']);
                	    break;
   }
        
        echo $resultat;  

    function GetTreeTradAuto($tag,$lang){

        global $objSite;
        $xul = new Xul($objSite);
		return $xul->GetTreeTradAuto($tag,$lang);    	
    	
    }
        
	function SetSession($lib,$val){
		$_SESSION[$lib] = $val;
		return "_SESSION[".$lib."]=".$_SESSION[$lib];
	}
        
        
	function GetExaIEML($code,$r,$ShowAll){
        global $objSite;
		//construction un document svg exagramme 
		$exa = new Exagramme($objSite,$code,$ShowAll,$r,true); 	
		$exa->GetSequence();
        		
	}
        
	function VerifExpIEML($code,$lib){
        global $objSite;
        $sem = new Sem($objSite,$objSite->XmlParam,"");
        return $sem->VerifExpIEML($code,$lib);
		
	}
        
	function GetTreeNoTradUti($lang){
        global $objSite;
        $xul = new Xul($objSite);
		return $xul->Get_Tree_NoTrad_Uti($_SESSION['iduti'],$lang);
	}

	function GetTreeTradUtis($lang){
        global $objSite;
        global $objUti;
        global $objUtiTradAuto;
        $xul = new Xul($objSite);
		return $xul->Get_Tree_Trad_Utis(array($objUti,$objUtiTradAuto),$lang);
	}
        
       
        // Ajouter une traduction dans la table ieml_onto et onto_trad
        function AddTrad($codeflux,$codeIeml,$libIeml,$lang){

                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
                return $sem->Add_Trad($codeflux,$codeIeml,$libIeml,-1,false,-1,$lang);
                             
 		}
       
        function SupTrad($codeIeml,$codeflux){
        
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
			    //vérifie le partage de traduction
		    	$idTrad = $sem->Add_Trad($codeflux,$codeIeml,'',$_SESSION['iduti'],true,-1,"");
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
      function GetTreeIemlOnto(){
          global $objSite;       
          $objXul = new Xul($objSite);
          $tree=$objXul->GetTreeIemlOnto("ieml");
          return $tree;
       }
       function recherche($query,$type,$lang){
       		global $objSite;
 			$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
 			return $sem->recherche($query,$type,$_SESSION['iduti'],$lang); 	       	
       }
        function Evalactisem($login,$mdp){
       		global $objSite;
 			$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
 			$oDelicious = new PhpDelicious($login, $mdp);
 			return $sem->Evalactisem($oDelicious,$login,$mdp); 	       	
       }
       function GetFlux($lang,$getFlux){
       	global $objSite;
       	global $oDelicious;
       	global $objUti;
       		$Activite= new Acti();
   			$oSaveFlux= new SauvFlux(); 
   			$oSaveFlux->aGetAllTags($objSite,$oDelicious,$objUti,$lang,$getFlux);
   			$Activite->AddActi('RAT',$objUti->id);
       	}
       	
       	function getLangLiveMetal(){
       		global $objSite;
       		$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
 			return $sem->getLangLiveMetal();
       	}
       	
       	function getSelectItemRech($id,$lang){
       		global $objSite;
       		$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
 			return $sem->LiveMetalRequest($lang,$id,'getEntryRech');
       	}
	  
?>
