<?php
        $ajax = true;
        require_once ("../../param/ParamPage.php");
        
        
        
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
                        $resultat = AddTrad(stripslashes($_GET['libIeml']),stripslashes($_GET['codeFlux']),stripslashes ($_GET['codeIeml']));
                        break;
                case 'SupTrad':
                        $resultat = SupTrad(stripslashes ($_GET['codeIeml']),stripslashes ($_GET['libIeml']),stripslashes ($_GET['codeflux']));
                        break;
                case 'SetProc':
                        $resultat = SetProc($_GET['id'],$_GET['code'],$_GET['desc']);
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
                case 'Recup_onto_trad':
        	        	$resultat=Recup_onto_trad();
                	    break;
                case 'GetTreeTrad':
        	        	//Pour le débugage
                		if($_GET['debug'])
        	        		$resultat=GetTreeTrad($_GET['flux'],$_GET['trad'],$_GET['descp'],$_GET['type'],$_GET['primary'],$_GET['bdd'],$GET['couche']);
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
               		
                case 'Table_Flux':
               		$resultat=Table_Flux($_GET['tag'],$_GET['desc'],$_GET['url'],$_GET['date'],$_GET['note']);
               		break;
               	
                case 'SavePalette':
                	$resultat=SavePalette(stripslashes ($_POST['color']));
                	break;
                
                case 'GetPalette':
                	$resultat=GetPalette();
                	break;
                case 'IemlCycle':
                	if($_GET['debug'])
                		$resultat=IemlCycle($_GET['key']);
                	else
	                	$resultat=IemlCycle($_POST['key']);
                	break;
               
       }
        
        echo $resultat; 

        

        
        function GetTreeTrad($flux,$trad,$descp,$type,$primary,$bdd,$couche){
			
        	global $objSite;
            $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
        	$arrBdd=explode(";",$bdd);		    
        	$arrFlux=explode(";",$flux);
        	
        	
        	if($type=="Signl_Trad"){
			
				$arrTrad=explode(";",$trad);
			    $arrDescp=explode(";",$descp);
			    $arrCouche=explode(";",$couche);
			    //vérifie le partage de traduction
			    for($i=0;$i<sizeof($arrTrad);$i++){
			    	//vérifie que l'on traite une trad auto
			    	if(!in_array($arrTrad[$i], $arrBdd)) {
				    	$idTrad = $sem->Add_Trad($arrDescp[$i],$arrFlux[$i],$arrTrad[$i],$objSite->infos["UTI_TRAD_AUTO"],true);
						if(!$sem->VerifPartageTrad($idTrad,$_SESSION['iduti'])){
							unset($arrTrad[$i]);
							unset($arrDescp[$i]);
							unset($arrFlux[$i]);
						}
			    	}
				}
			    
        	}
        	if($type=="Multi_Trad"){
        		$arrTrad=explode("*",$trad);
			    $arrDescp=explode("*",$descp);
			    $arrCouche=explode("*",$couche);
        	}
        	if($type=="No_Trad"){
        		//récupère les traduction automatiques supprimmées par l'utilisateur
        		$rows = $sem->GetAutoTradSup($objSite->infos["UTI_TRAD_AUTO"]);
        		$arrTrad = array();
   				while($r = mysql_fetch_assoc($rows))
				{
					//vérifie que le tag n'a pas été retraduit
					if(!$sem->VerifTradUtiFlux($_SESSION['iduti'],$r['onto_flux_id']))
						array_push($arrTrad, $r['onto_flux_code']);
				}
			    
        	}
        	
        	$objXul = new Xul($objSite);
        	
        	$ihm=$objXul->GetTreeTrad($arrFlux,$arrTrad,$arrDescp,$type,$primary,$arrBdd,$arrCouche);  
            
        	return stripslashes($ihm);
        }
        
        function GetTreeDictio(){
        	 global $objSite;
        	 
        	 $objXul = new Xul($objSite);
        	  $tree=$objXul->GetTree_ieml_onto("ieml");
			  return $tree;
        }
        
        // Ajouter une traduction dans la table ieml_onto et onto_trad
        function AddTrad($libIeml,$codeflux,$codeIeml){
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
                return $sem->Add_Trad($libIeml,$codeflux,$codeIeml);
                             
 		}
       
        function SupTrad($codeIeml,$libIeml,$codeflux){
        
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
			    //vérifie le partage de traduction
		    	$idTrad = $sem->Add_Trad($libIeml,$codeflux,$codeIeml,$objSite->infos["UTI_TRAD_AUTO"],true);
		    	if($idTrad){
					if($sem->VerifPartageTrad($idTrad,$_SESSION['iduti'])){
	                	$message = $sem->SupPartageTrad($idTrad,$_SESSION['iduti']);
					}else{
	                	$message = $sem->Sup_Trad($codeIeml,$libIeml,$codeflux);						
					}
		    	}else{
		    		$message = utf8_encode("Problème lors de la vérification du partage");
		    	}
                
                return $message;
                
        }
                
        
        function SetOnto($type,$col,$id,$valeur){
        
                global $objSite;
                                
                // requête pour vérifier l'existence de la traduction
                /*
                $Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-AddTrad-VerifExist']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $where = str_replace("-id10eF-", $id10eF, $Q[0]->where);
                $where = str_replace("-idIeml-", $idIeml, $where);
                $sql = $Q[0]->select.$Q[0]->from.$where;
                */
                //modifie le nom de la colonne du tree pour qu'il corresponde au nom de la colonne de la table
                $col = str_replace(preCol,$type,$col);
                $colId = $type."_id";
                $sql = "UPDATE ieml_onto SET 
                        ".$col."='".utf8_encode($valeur)."'
                        , ieml_date = now()
                        WHERE ".$colId."=".$id.""; 
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
                $message = mysql_affected_rows()." modifiée";
                $db->close();
                return $message.$sql;
                
        }


        function SetFlux($type,$col,$id,$valeur){
        
                global $objSite;
                                
                // requête pour vérifier l'existence de la traduction
                /*
                $Xpath = "/EvalActiSem/Querys/Query[@fonction='ExeAjax-AddTrad-VerifExist']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $where = str_replace("-id10eF-", $id10eF, $Q[0]->where);
                $where = str_replace("-idIeml-", $idIeml, $where);
                $sql = $Q[0]->select.$Q[0]->from.$where;
                */
                $sql = "INSERT INTO ieml_flux (flux_ieml, flux_date) 
                        VALUES ('".$type.$col.$id.$valeur."',now())";
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
                $message = mysql_affected_rows()." modifiée";
                $db->close();
                return $message;
                
        }

        function SetProc($id,$code,$desc){
        
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
                return $sem->SetSem($id,$code,$desc);                           
                
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
        
		function ieml_uti_onto($objSite,$uti_id,$ieml_id,$db){
		
			$Xpath="/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ieml_uti_onto']";
			$Q = $objSite->XmlParam->GetElements($Xpath);
			$values=str_replace("-iduti-",$uti_id,$Q[0]->values);
			$values=str_replace("-idieml-",$ieml_id,$values);
			$sql=$Q[0]->insert.$values;
			$reponse = $db->query($sql);
	}
	
        function GraphGet($mbook){
        	$bookmark=new BookMark($mbook);
        	if(TRACE)
        		echo "ExeAjax:GraphGet:bookmark".$bookmark."<br/>";
        	$AgentOnto= new AgentOnto($bookmark);
        	
        	//$bookmark->GetInfos();
        	return $AgentOnto->svgBookmark();
        	
        }
        
        function Recup_onto_trad(){
    	    global $objSite;
        	$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
        	return $sem->RecupOntoTrad();
               
     }     
        
        function InsertIemlOnto($Iemlcode,$Iemllib,$Imelparent){
	     	global $objSite;     
	     	$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
	     		     
	     		     $sem->InsertIemlOnto($Iemlcode,$Iemllib,$Imelparent);
     	}
        
	   	function AddPostIeml(){
	     	global $objSite;
	     	$oDelicious=$_SESSION['Delicious'];
	        $bmark=new  BookMark();
	         
	         $bmark->MajPostIeml($objSite,$oDelicious);
	         
	    }
    
	   	function Delet_Compte_Delicious(){
	     global $objSite;
	     $oDelicious=$_SESSION['Delicious'];
	     $bmark=new  BookMark();
	         
	         return $bmark->DeletCompteDelicious($objSite,$oDelicious,$_SESSION['iduti'],$_SESSION['loginSess']);
	   
	   }
       
	   	function Table_Flux($sTag,$sUrl,$sDesc,$sDate,$sNote){
       	
       	$objXul = new Xul($objSite);
       	return $objXul->TableFlux($sTag,$sUrl,$sDesc,$sDate,$sNote);
	}
       
	   	function SavePalette($color){
	   	 	global $objSite;
	   		$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
	   	 	
	   	 	$sem->CreatFileXml($color,'Color_'.$_SESSION['loginSess']);
	   	}
	   	
	   	function GetPalette(){
	   		global $objSite;
	   		$arrColor;
	   		$file=md5('Color_'.$_SESSION['loginSess']).".xml";
	   		 if(file_exists(Flux_PATH.$file)){
             	$xml=simplexml_load_file(Flux_PATH.$file);
                foreach($xml->xpath('color') as $color){
                	$arrColor.=$color['id'].';'.$color.'&';
                }
             	 
           }
	       print_r($arrColor);  
	   }
	   	function IemlCycle($key){
	   		global $objSite;
 			$sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
 			return $sem->GetCycle($key);                           
                
	   }
	   
       
        
        ?>
