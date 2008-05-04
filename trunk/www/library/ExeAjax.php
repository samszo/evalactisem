<?php
        $ajax = true;
        require_once ("../param/ParamPage.php");
        session_start();
        
        
        //charge le fichier de paramètrage
        $objSite->XmlParam = new XmlParam(PathRoot."/param/ParamXul.xml");


        $resultat = "";
        if(isset($_GET['f']))
                $fonction = $_GET['f'];
        else
                $fonction = '';
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
                        $resultat = AddTrad($_GET['libIeml'],$_GET['codeFlux'],$_GET['codeIeml']);
                        break;
                case 'SupTrad':
                        $resultat = SupTrad($_GET['codeIeml'],$_GET['libIeml'],$_GET['codeflux']);
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
                        $resultat = GetGraph($code);
                        break;
                case 'GraphGet':
                	    $resultat=GraphGet($mbook);
                	    break;
                case 'Recup_onto_trad':
        	        	$resultat=Recup_onto_trad();
                	    break;
                case 'GetTreeTrad':
        	        	$resultat=GetTreeTrad($_GET['flux'],$_GET['trad'],$_GET['descp'],$_GET['type'],$_GET['primary'],$_GET['bdd']);
                	    break;
                case 'insert_ieml_onto':
                	   $resultat=insert_ieml_onto($_GET['Iemlcode'],$_GET['Iemllib'],$_GET['Imelparent']);
                	   break;
                case 'GetTreeDictio':
                	   $resultat=GetTreeDictio();
                	   break;
		}
        
        echo $resultat; 

        

        
        function GetTreeTrad($flux,$trad,$descp,$type,$primary,$bdd){
			
        	global $objSite;

        	if($type=="Signl_Trad"){
			
				$arrTrad=explode(";",$trad);
			    $arrDescp=explode(";",$descp);
        	}elseif($type=="Multi_Trad"){
        		$arrTrad=explode("*",$trad);
			    $arrDescp=explode("*",$descp);
        	}
		    
        	$arrBdd=explode(";",$bdd);
		    
        	$arrFlux=explode(";",$flux);
        	$objXul = new Xul($objSite);
        	
        	$ihm=$objXul->GetTreeTrad($arrFlux,$arrTrad,$arrDescp,$type,$primary,$arrBdd);  
            return $ihm;
        }
        
        function GetTreeDictio(){
        	 global $objSite;
        	 
        	 $objXul = new Xul($objSite);
        	  $tree=$objXul->GetTree_ieml_onto("ieml");
			  return $tree;
        }
        
        // Ajouter une traduction dans la table ieml_onto et onto_trad
        function AddTrad($libIeml,$codeflux,$codeIeml){

        		$iduti=$_SESSION['iduti'];
                global $objSite;

                	
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		        $db->connect();   
                // requête pour vérifier l'existence de la traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax_Trad_VerifExist']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $where = str_replace("-iemllib-", $libIeml, $Q[0]->where);
                $from = str_replace("-iduti-", $iduti, $Q[0]->from);
                $sql = $Q[0]->select.$from.$where;
                if(trace)
                	echo "ExeAjax:AddTrad:$sql.<br/>";
                $result = $db->query($sql);
                $db->close();
                $row=mysql_fetch_row($result);
                if($row[0]==0){
                	
                	$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='AddTrad_Insert_onto_flux']";
		        	$Q = $objSite->XmlParam->GetElements($Xpath);
		        	$values = str_replace("-codeIeml-", $codeIeml, $Q[0]->values);
		            $values = str_replace("-libIeml-", $libIeml, $values);	
		        	$values = str_replace("-nivIeml-", 1, $values);
		        	$values = str_replace("-parentIeml-", -1, $values);
		        	$sql = $Q[0]->insert.$values;
	                if(TRACE)
	                	echo "ExeAjax:AddTrad:$sql.<br/>";
		        	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		            $db->connect();
		            $result = $db->query($sql);
		            $idieml=mysql_insert_id();
		            ieml_uti_onto($objSite,$iduti,$idieml,$db);
		            $message = mysql_affected_rows()." traduction ajoutée";
		            $db->close();
                    return AddTrad_onto_trad( $codeflux,$idieml);	
                }
                if($row!=0){ 
                    	echo $exit;
                    	return "La traduction existe deja !";
                    	
                }
                
                
 }
        //verifier si une traduction existe deja dans onto_trad
 		function VerifExist_onto_trad($idflux,$idIeml,$idacteur){ 
 				global $objSite;
 				$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-AddTrad-VerifExist']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $where = str_replace("-idflux-", $idflux, $Q[0]->where);
                $where = str_replace("-idIeml-", $idIeml, $where);
                $where = str_replace("-idacteur-", $idacteur, $where);
                $sql = $Q[0]->select.$Q[0]->from.$where;
				$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
                $db->close();
                $r = mysql_fetch_row($result);
                if($r[0]>0)
                	   
                	return "true";
                             
               }
        	
        function AddTrad_onto_trad( $codeflux,$idIeml){
        	 	global $objSite;
        		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='Get_Id_Flux']";
        		$Q = $objSite->XmlParam->GetElements($Xpath);
        		$where = str_replace("-codeflux-", $codeflux, $Q[0]->where);
        	 	$sql = $Q[0]->select.$Q[0]->from.$where;
        	 	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
                $idflux=mysql_fetch_array($result);
        		echo "idflux=".$idflux[0];
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-AddTrad-Insert']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $values = str_replace("-idflux-", $idflux[0], $Q[0]->values);
                $values = str_replace("-idIeml-", $idIeml, $values);
                $sql = $Q[0]->insert.$values;
                
                $result = $db->query($sql);
                $message = mysql_affected_rows()." traduction ajoutee";
                $db->close();
                
                return $message;
        }
        
        function AddDictio($idflux,$libIeml,$codeIeml){
        		$iduti=$_SESSION['iduti'];
        		global $objSite;
        		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax_Trad_VerifExist_P']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $where = str_replace("-codeieml-", $codeIeml, $Q[0]->where);
                $sql = $Q[0]->select.$Q[0]->from.$where;
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
                $db->close();
                $row = mysql_fetch_array($result);
                if($row[0]!=0){
                	
                	 VerifExist_onto_trad($idflux,$row[0],$iduti);
                	
                	return AddTrad_onto_trad($idflux,$row[0]);
                	
          
                }
      
        	$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='AddTrad_Insert_onto_flux']";
        	$Q = $objSite->XmlParam->GetElements($Xpath);
        	$values = str_replace("-codeIeml-", $codeIeml, $Q[0]->values);
            $values = str_replace("-libIeml-", $libIeml, $values);	
        	$values = str_replace("-nivIeml-", 1, $values);
        	$values = str_replace("-parentIeml-",0, $values);
        	$sql = $Q[0]->insert.$values;
        	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
            $db->connect();
            $result = $db->query($sql);
            $idIeml=mysql_insert_id();
            $message = mysql_affected_rows()." traduction ajoutée";
            $db->close();
            ieml_uti_onto($objSite,$iduti,$idIeml,$db);
            return AddTrad_onto_trad($idflux,$idIeml);
            
       }
        function SupTrad($codeIeml,$libIeml,$codeflux){
        
                global $objSite;
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from=str_replace("-codeFlux-", $codeflux, $Q[0]->from);
                $from=str_replace("-codeIeml-", $codeIeml, $from);
                $from=str_replace("-Iemllib-", $libIeml, $from);
                $sql = $Q[0]->select.$from.$Q[0]->where;
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
                $res=mysql_fetch_array($result);
                //requête pour Supprimer une traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_Trad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                //echo $Q;
                $where = str_replace("-idflux-", $res[0], $Q[0]->where);
                $where = str_replace("-idIeml-", $res[1], $where);
                
                $sql = $Q[0]->delete.$Q[0]->from.$where;
                $result = $db->query($sql);
                //suppression de la traduction de la table ieml_onto;
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_onto']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $where = str_replace("-idIeml-", $res[1], $Q[0]->where);
                $sql = $Q[0]->delete.$Q[0]->from.$where;
                echo $sql;
                $result = $db->query($sql);
                //suppression de la traduction de la tableExeAjax-SupTrad-Delete_ieml_uti_onto;
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_uti_onto']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $sql = $Q[0]->delete.$Q[0]->from.$where;
                $result = $db->query($sql);
                $message = mysql_affected_rows()." traduction supprime";
                $db->close();
                
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


        function GetGraph($code){
        
                global $objSite;
                $sem = New Sem($objSite, $objSite->infos["XML_Param"], "");
                
                $liens = $sem->GetSvgPie($code);                                
                
                //$svg = $objSite->GetCurl($liens["GraphPrimitive"]);
                //header("Content-Type: image/svg+xml");
                
                return $liens["GraphPrimitive"];

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
    	
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		        $db->connect();   
                	// requête pour vérifier l'existence de la traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='Tree_dynamique']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from = str_replace("-iduti-", $_SESSION['iduti'],$Q[0]->from);
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
        
     	function insert_ieml_onto($Iemlcode,$Iemllib,$Imelparent){
     		    global $objSite;	
     			$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		        $db->connect();   
                	// requête pour vérifier l'existence de la traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='AddTrad_Insert_onto_flux']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $values = str_replace("-codeIeml-", $Iemlcode,$Q[0]->values);
                $values = str_replace("-libIeml-", $Iemllib,$values);
                $values = str_replace("-parentIeml-", -1,$values);
                if($Imelparent=="relations"){
                	 $values = str_replace("-nivIeml-", 3 ,$values);
                }elseif($Imelparent=="ideas"){
                	$values = str_replace("-nivIeml-", 4 ,$values);
                }elseif($Imelparent=="events"){
                	$values = str_replace("-nivIeml-", 2 ,$values);
                }

                $sql = $Q[0]->insert.$values;
                $result = $db->query($sql);
                $idieml=mysql_insert_id();
                
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoFlux']/Querys/Query[@fonction='Insert_ieml_foret']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                print_r($Q);
                $values = str_replace("-idparent", -1,$Q[0]->values);
                $values = str_replace("-idieml-", $idieml,$values);
                $sql = $Q[0]->insert.$values;
                $result = $db->query($sql);
                $db->close();
     		
     	}
        
        
        ?>