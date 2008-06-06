<?php
        $ajax = true;
        require('../php-delicious/php-delicious.inc.php');
        require('../../param/Constantes.php');
        require_once ("../../param/ParamPage.php");
        session_start();
        
        
        //charge le fichier de param�trage
        $objSite->XmlParam = new XmlParam(PathRoot."/param/ParamXul.xml");


        $resultat = "";

        
        
        if(isset($_POST['f'])){
              $fonction = $_POST['f'];
              echo $fonction;
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
                        $resultat = AddTrad(stripslashes ($_GET['libIeml']),stripslashes ($_GET['codeFlux']),stripslashes ($_GET['codeIeml']));
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
        	        	$resultat=GetTreeTrad($_POST['flux'],$_POST['trad'],$_POST['descp'],$_POST['type'],$_POST['primary'],$_POST['bdd']);
                	   
        	        	break;
                case 'insert_ieml_onto':
                	   $resultat=insert_ieml_onto($_GET['Iemlcode'],$_GET['Iemllib'],$_GET['Imelparent']);
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
               
		}
        
        echo $resultat; 

        

        
        function GetTreeTrad($flux,$trad,$descp,$type,$primary,$bdd){
			
        	global $objSite;
			echo "flux=".$flux;
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
				$Activite= new Acti();
                	
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
		        $db->connect();   
		        
		        //recuperation des identifiants ieml_id et ieml_onto_flux
		        
		        $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax_recup_id']";
		        $Q = $objSite->XmlParam->GetElements($Xpath);
		        $from=str_replace("-codeFlux-", $codeflux, $Q[0]->from);
                $from=str_replace("-Iemllib-",utf8_decode($libIeml), $from);
                $from=str_replace("-iduti-", $iduti, $from);
                echo $sql = $Q[0]->select.$from;
                $result = $db->query($sql);
                $res=mysql_fetch_array($result);
                
                // insertion dans la table de traductions des identifiants
                
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-AddTrad-Insert']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $values=str_replace("-idflux-", $res[0], $Q[0]->values);
                $values=str_replace("-idIeml-", $res[1],$values);
                
                $sql = $Q[0]->insert.$values;
                $result = $db->query($sql);
                $resp=mysql_fetch_array($result);
                 echo "==||".utf8_decode($libIeml);
                //insertion de la traduction dans la table des utilisateurs
                
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ieml_uti_onto']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $values=str_replace("-idieml-", $res[1],$Q[0]->values);
                $values=str_replace("-iduti-", $iduti, $values);
                $sql = $Q[0]->insert.$values;
                $result = $db->query($sql);
                $res=mysql_fetch_array($result);
                $message = mysql_affected_rows()." traduction ajout�e";
                $db->close();
                $Activite->AddActi("AddTrad",$iduti);
                return $message;
                
 }
       
        function SupTrad($codeIeml,$libIeml,$codeflux){
        
                global $objSite;
                $Activite= new Acti();
                
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $from=str_replace("-codeFlux-",$codeflux, $Q[0]->from);
                $from=str_replace("-codeIeml-", $codeIeml, $from);
                $from=str_replace("-Iemllib-",utf8_decode($libIeml), $from);
                echo $sql = $Q[0]->select.$from.$Q[0]->where;
                $db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
                $db->connect();
                $result = $db->query($sql);
                $res=mysql_fetch_array($result);
                
                //requ�te pour Supprimer une traduction
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_Trad']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                //echo $Q;
                $where = str_replace("-idflux-", $res[0], $Q[0]->where);
                $where = str_replace("-idIeml-", $res[1], $where);
                
                $sql = $Q[0]->delete.$Q[0]->from.$where;
                $result = $db->query($sql);
                //suppression de la traduction de la tableExeAjax-SupTrad-Delete_ieml_uti_onto;
                $Xpath = "/XmlParams/XmlParam[@nom='GetOntoTrad']/Querys/Query[@fonction='ExeAjax-SupTrad-Delete_ieml_uti_onto']";
                $Q = $objSite->XmlParam->GetElements($Xpath);
                $sql = $Q[0]->delete.$Q[0]->from.$where;
                $result = $db->query($sql);
                $message = mysql_affected_rows()." traduction supprime";
                $db->close();
                 $Activite->AddActi("DelTrad",$iduti);
                return $message;
        }
                
        
        function SetOnto($type,$col,$id,$valeur){
        
                global $objSite;
                                
                // requ�te pour v�rifier l'existence de la traduction
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
                $message = mysql_affected_rows()." modifi�e";
                $db->close();
                return $message.$sql;
                
        }


        function SetFlux($type,$col,$id,$valeur){
        
                global $objSite;
                                
                // requ�te pour v�rifier l'existence de la traduction
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
                $message = mysql_affected_rows()." modifi�e";
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
        
        function insert_ieml_onto($Iemlcode,$Iemllib,$Imelparent){
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
     
        
        ?>
