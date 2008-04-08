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
                $code = $_GET['code'];
        else
                $code = -1;
        if(isset($_GET['desc']))
                $desc = $_GET['desc'];
        else
                $desc = -1;
                
                

		switch ($fonction) {
                case 'AddDictio':
                        $resultat = AddDictio($_GET['libflux'],$_GET['idflux'],$_GET['codeIeml']);
                        break;
        }
        switch ($fonction) {
                case 'AddTrad':
                        $resultat = AddTrad($_GET['libflux'],$_GET['idflux'],$_GET['codeIeml'],$_GET['libIeml']);
                        break;
        }
        switch ($fonction) {
                case 'SupTrad':
                        $resultat = SupTrad($_GET['idIeml'],$_GET['idflux']);
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
                        
        }
        
        echo $resultat; 
        // Ajouter une traduction dans la table ieml_onto et onto_trad
        function AddTrad($libflux,$idflux,$codeIeml,$libIeml){
        $iduti=$_SESSION['iduti'];
                global $objSite;
                       
                // requête pour vérifier l'existence de la traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax_Trad_VerifExist']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $where = str_replace("-libflux-", $libflux, $Q[0]->where);
                $where = str_replace("-codeIeml-", $codeIeml, $where);
                $sql = $Q[0]->select.$Q[0]->from.$where;
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
                $db->close();
                $row = mysql_fetch_array($result);
               
                if($row[0]==0){
                	
                	$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='AddTrad_Insert_onto_flux']";
		        	$Q = $objSite->XmlParam->GetElements($Xpath);
		        	$values = str_replace("-codeIeml-", $codeIeml, $Q[0]->values);
		            $values = str_replace("-libIeml-", $libflux, $values);	
		        	$values = str_replace("-nivIeml-", 1, $values);
		        	$values = str_replace("-parentIeml-", -1, $values);
		        	$sql = $Q[0]->insert.$values;
		        	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		            $db->connect();
		            $result = $db->query($sql);
		            $row[0]=mysql_insert_id();
		            $message = mysql_affected_rows()." traduction ajoutée";
		            $db->close();
                }
                if($row[0]!=0){ 
                	    
                	$exist=VerifExist_onto_trad($idflux,$row[0],$iduti);
                    
                	if($exist=="true"){
                    	echo $exit;
                    	return "La traduction existe deja !";
                    }	
                }
                return AddTrad_onto_trad($idflux,$row[0],$iduti);	
                
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
        	
        function AddTrad_onto_trad($idflux,$idIeml,$idacteur){
        	 	global $objSite;
        		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-AddTrad-Insert']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $values = str_replace("-idflux-", $idflux, $Q[0]->values);
                $values = str_replace("-idIeml-", $idIeml, $values);
                $values= str_replace("-idacteur-",$idacteur,$values);
                $sql = $Q[0]->insert.$values;
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
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
                	
                	return VerifExist_onto_trad($idflux,$row[0],$iduti);
                	
                	return AddTrad_onto_trad($idflux,$row[0],$iduti);
                	
          
                }
      
        	$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='AddTrad_Insert_onto_flux']";
        	$Q = $objSite->XmlParam->GetElements($Xpath);
        	$values = str_replace("-codeIeml-", $codeIeml, $Q[0]->values);
            $values = str_replace("-libIeml-", $libIeml, $values);	
        	$values = str_replace("-nivIeml-", 1, $values);
        	$values = str_replace("-parentIeml-", -1, $values);
        	$sql = $Q[0]->insert.$values;
        	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
            $db->connect();
            $result = $db->query($sql);
            $idIeml=mysql_insert_id();
            $message = mysql_affected_rows()." traduction ajoutée";
            $db->close();
            return AddTrad_onto_trad($idflux,$idIeml,$idacteur);
            
       }
        function SupTrad($idIeml,$idflux){
        
                global $objSite;
                
                //requête pour Supprimer une traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                //echo $Q;
                $where = str_replace("-idflux-", $idflux, $Q[0]->where);
                $where = str_replace("-idIeml-", $idIeml, $where);
                //echo $where;
                $sql = $Q[0]->delete.$Q[0]->from.$where;
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
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
                return $sem->GetSvgBarre($code);                                
                
        }
        
        
        
        
        
        ?>