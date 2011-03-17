<?php
        $ajax = true;
        require_once ("../../param/ParamPage.php");
        //charge le fichier de param�trage
        $objSite->XmlParam = new XmlParam(PathRoot."/param/ParamXul.xml");

        $resultat = "";
        
     
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
					GetTagCloud($objSite,$oDelicious,$TC,$user,$NbDeb,$NbFin,$lang,$ShowAll,$TempsVide,$DateDeb,$DateFin);
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
                 case 'SetTagsLinks':
                	    SetTagsLinks($login);
                	    break;
                 case 'SetTagLinks':
                	    SetTagLinks($login,$tag);
                	    break;
                 case 'GetTagLinks':
                	    GetTagLinks($login,$tag);
                	    break;
                 case 'UpdateUserFluxPoids':
                	    UpdateUserFluxPoids();
                	    break;
                 case 'GetUsersTagsDistrib':
                	    GetUsersTagsDistrib($users);
                	    break;
                 case 'GetUsersTagLinks':
                	    GetUsersTagLinks($users,$tag,$_GET['all']);
                	    break;
                 case 'SetTagPosts':
                	    SetTagPosts($tag);
                	    break;
                 case 'SetUserTagsPosts':
                	    SetUserTagsPosts();
                	    break;
                 case 'DelUserTags':
                	    DelUserTags();
                	    break;
                 case 'GetStatVisu':
                	    GetStatVisu($users,$tag);
                	    break;
                	    
		}
        
        echo $resultat;  

    function GetStatVisu($users,$tag){
		global $objSite;
       	global $oDelicious;
       	global $objUti;
   		$Activite= new Acti();
		$oTG = new TagCloud($objSite,$oDelicious,"",$login);
		//esterhasz,fennec_sokoko,luckysemiosis,samueld,wazololo
		$arrUsers = split(",",$users);

		$oCache = new Cache("json/GetStatVisu_".$users."_".$tag.".js",CACHETIME);   
        if (!$oCache->Check()) {
        	$jsTL = json_encode($oTG->GetStatVisu($arrUsers,$tag));
        	$oCache->Set($jsTL,true);
		}
		echo $oCache->Get(true);
		$Activite->AddActi('GetStatVisu',$objUti->id);		
   	}        
            
    function DelUserTags(){
     	// end start benchmark
     	$start = microtime(); 
    	
     	global $objSite;
       	global $objUti;
   		$Activite= new Acti();
		$oSaveFlux= new SauvFlux($objSite); 
   		$oSaveFlux->DelUserTags($objUti);
		$Activite->AddActi('DUT',$objUti->id);		

		// end benchmark timing
		$end = microtime(); 
		$t2 = ($this->site->getmicrotime($end) - $this->site->getmicrotime($start)); 
		echo "<p>Total DelUserTags Time: <b>$t2</b>";
		$mem=memory_get_usage(true);$mem=$mem/1048576;
		echo "<br/>$mem M <br/>";
    }
        
    function SetUserTagsPosts(){
     	// end start benchmark
     	$start = microtime(); 

     	ini_set("memory_limit","320M");
    	
     	global $objSite;
       	global $oDelicious;
       	global $objUti;
   		$Activite= new Acti();
		$oSaveFlux= new SauvFlux($objSite); 
   		$oSaveFlux->aSetUserTagsPosts($oDelicious,$objUti);
		$Activite->AddActi('SUTP',$objUti->id);		

		// end benchmark timing
		$end = microtime(); 
		$t2 = ($this->site->getmicrotime($end) - $this->site->getmicrotime($start)); 
		echo "<p>Total SetTagsPosts Time: <b>$t2</b>";
		$mem=memory_get_usage(true);$mem=$mem/1048576;
		echo "<br/>$mem M <br/>";
    }
        
    function SetTagPosts($tag){
		global $objSite;
       	global $oDelicious;
       	global $objUti;
   		$Activite= new Acti();
		$oSaveFlux= new SauvFlux($objSite); 
   		$oSaveFlux->aSetTagsPosts($oDelicious,$objUti,$tag);
		$Activite->AddActi('STP',$objUti->id);		
    }
        
        
   	function GetUsersTagLinks($users,$tag,$all){
		global $objSite;
       	global $oDelicious;
       	global $objUti;
   		$Activite= new Acti();
		$oTG = new TagCloud($objSite,$oDelicious,"",$login);
		//esterhasz,fennec_sokoko,luckysemiosis,samueld,wazololo
		$arrUsers = split(",",$users);

		//$jsTL = json_encode($oTG->GetUsersTagLinks($arrUsers,$tag,$all));
   		//echo $jsTL;

		$oCache = new Cache("json/TagLinks_".$users."_".$tag."_".$all.".js",CACHETIME);   
        if (!$oCache->Check()) {
        	$jsTL = json_encode($oTG->GetUsersTagLinks($arrUsers,$tag,$all));
        	$oCache->Set($jsTL,true);
		}
		echo $oCache->Get(true);
		$Activite->AddActi('GetUsersTagLinks',$objUti->id);		
   	}        
        
  	function GetUsersTagsDistrib($users){
		global $objSite;
       	global $oDelicious;
       	global $objUti;
   		$Activite= new Acti();
		$oTG = new TagCloud($objSite,$oDelicious,"",$login);
		//esterhasz,fennec_sokoko,luckysemiosis,samueld,wazololo
		$arrUsers = split(",",$users);
		echo $oTG->GetUsersTagsDistrib($arrUsers);
		$Activite->AddActi('GetUsersTagsDistrib',$objUti->id);		
  	}        

   	function UpdateUserFluxPoids(){
		global $objSite;
       	global $objUti;
   		$Activite= new Acti();
		$oSaveFlux= new SauvFlux($objSite); 
   		$oSaveFlux->UpdateUserFluxPoids();
		$Activite->AddActi('UpdateUserFluxPoids',$objUti->id);		
	}        
        
        
	function GetTagLinks($login,$tag){
		global $objSite;
       	global $oDelicious;
       	global $objUti;
   		$Activite= new Acti();
		$oTG = new TagCloud($objSite,$oDelicious,"",$login);
   		$arrTL = $oTG->GetTagLinks($objUti,$tag);
		//nécéssaire pour les gros bookmark
		ini_set("memory_limit",'16M');
   		$jsTL = json_encode($arrTL);
   		echo $jsTL;
		//enregistrement du fichier
		$objSite->SaveFile(CACHE_PATH."json/TagLinks_".$oUti->login."_".$tag.".js", "var data = ".$jsTL);
   		$Activite->AddActi('GTL',$objUti->id);		
	}
        
   	function SetTagLinks($login,$tag){
		global $objSite;
       	global $oDelicious;
       	global $objUti;
    	set_time_limit(9000);
    	ini_set("memory_limit","100M");
       	$Activite= new Acti();
		$oSaveFlux= new SauvFlux($objSite); 
   		$oSaveFlux->aSetTagLinks($oDelicious,$objUti,$tag);
		$Activite->AddActi('STL',$objUti->id);		
	}        
	        
	function SetTagsLinks($login){
		global $objSite;
       	global $oDelicious;
       	global $objUti;
   		$Activite= new Acti();
		$oSaveFlux= new SauvFlux($objSite); 
   		$oSaveFlux->aSetTagsLinks($oDelicious,$objUti);
		$Activite->AddActi('STL',$objUti->id);		
	}
	
	
	function GetTagCloud($objSite,$oDelicious,$TC,$user,$NbDeb,$NbFin,$lang,$ShowAll,$TempsVide,$DateDeb,$DateFin){

		if($TC=="bulles"){
			$url = "http://localhost/evalactisem/overlay/BubbleChartDelicious.php";
			$params = array ('user'=>$user,'NbDeb'=>$NbDeb,'NbFin'=>$NbFin);
			$url .= "?json=".urlencode(json_encode($params));
			echo ('<iframe id="fBulles_'.$user.'" flex="1"  src="'.$url.'"  />');
		}else{
			$oTC = new TagCloud($objSite,$oDelicious,$lang,$user);
			//$oTC->SauveBookmarkNetwork($_GET['login'],$mdp);
	
			//header("Content-type: image/svg+xml");
			if($TC=="posts")
				$oTC->GetSvgPost($user,$ShowAll,$TempsVide,$DateDeb,$DateFin,$NbDeb,$NbFin);
			if($TC=="tags")
				$oTC->GetSvgTag($user,$ShowAll,$NbDeb,$NbFin);
			if($TC=="roots")
				$oTC->GetSvgRoot($user,$ShowAll,$NbDeb,$NbFin);
		}                                

		
	}
        
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
