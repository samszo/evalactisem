<?php

session_start();
	require_once ("Constantes.php");
	
	if(isset($_GET['user'])){
		$user = $_GET['user'];
		$_SESSION['loginSess']=$user;
		$_SESSION['mdpSess']="";
	}				
	if(isset($_SESSION['loginSess'])){
		$login = $_SESSION['loginSess'];
		$mdp = $_SESSION['mdpSess'];
	}else{
		if(isset($_GET['login'])){
			$login = $_GET['login'];
			$mdp = $_GET['mdp'];
		}else{
			$login = "evalactisem";
			$mdp = "delcious09";
			$login = "luckysemiosis";
			$mdp = "Samszo0";			
		}
		$_SESSION['loginSess']=$login;
		$_SESSION['mdpSess']=$mdp;
		$user = $_SESSION['loginSess'];
	}

	if(isset($_GET['users'])){
		$users = $_GET['users'];
	}
	
	if(isset($_GET['lang'])){
		$_SESSION['lang']=$_GET['lang'];
		$lang = $_GET['lang'];
	}else{
		$_SESSION['lang']='fr';
	}
	
	// v�rification du site en cours
	if(isset($_GET['site'])){
		$site = $_GET['site'];
	}else{
		$site = DEFSITE;
	}
	
	if(isset($_GET['type']))
		$type = $_GET['type'];
	else
		$type = 'ieml';
			
	if(isset($_GET['ParamNom']))
		$ParamNom = $_GET['ParamNom'];
	else
		$ParamNom = "GetOntoTree";
	
	if(isset($_GET['box']))
		$box = $_GET['box'];
	else
		$box = "singlebox";
	
	if(isset($_GET['UrlNom']))
		$UrlNom = $_GET['UrlNom'];
	else
		$UrlNom = "Traduction";
	
	if(isset($_GET['TempsVide']))
		if($_GET['TempsVide']=="true")
			$TempsVide = true;
		else
			$TempsVide = false;
	else
		$TempsVide = true;
	
	if(isset($_GET['ShowAll']))
		if($_GET['ShowAll']=="true")
			$ShowAll = true;
		else
			$ShowAll = false;
	else
		$ShowAll = false;
	
	if(isset($_GET['DateDeb']))
		$DateDeb = $_GET['DateDeb'];
	else
		$DateDeb = false;
		
	if(isset($_GET['DateFin']))
		$DateFin = $_GET['DateFin'];
	else
		$DateFin = false;

	if(isset($_GET['NbDeb']))
		$NbDeb = $_GET['NbDeb'];
	else
		$NbDeb = 0;
	
	if(isset($_GET['NbFin']))
		$NbFin = $_GET['NbFin'];
	else
		$NbFin = 1000000000000;
		
	if(isset($_GET['TC']))
		$TC = $_GET['TC'];
	else
		$TC = "posts";
		
	$scope = array(
			"site" => $site
			,"type" => $type
			,"ParamNom" => $ParamNom
			,"box" => $box
			,"UrlNom" => $UrlNom
			);	
	
	$objSite = new Site($SITES, $site, $scope, false);
	$objXul= new Xul($objSite);
	$objUti = new Uti($objSite,$_SESSION['loginSess']);
	$objUtiTradAuto = new Uti($objSite,false,$objSite->infos["UTI_TRAD_AUTO"]);
	$oDelicious = new PhpDelicious($_SESSION['loginSess'],$_SESSION['mdpSess'],CACHETIME);
	
	$_SESSION['Delicious']= $oDelicious;
	
	$_SESSION['iduti']=$objUti->id;
	

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
		
	if(isset($_GET['tag']))
		$tag = $_GET['tag'];
	else
		$tag = 'no_tag';
		
	if(isset($_GET['width']))
		$width = $_GET['width'];
	else
		$width = 800;
	
	if(isset($_GET['height']))
		$height = $_GET['height'];
	else
		$height = 300;
			
	if(isset($_GET['complet']))
		$complet = true;
	else
		$complet = false;
		
		
function ChercheAbo ()
	{
		// connexion a delicious
		global $con;
		
		$login=$_POST['login_uti'];
		$mdp=$_POST['mdp_uti'];
		if(TRACE)
			echo "index:ChercheAbo:login:".$_POST['login_uti']." mdp=".$_POST['mdp_uti']."<br/>";
   	   	
		if(($login!="")&&($mdp!="")){
			$oDelicious = new PhpDelicious($login, $mdp);
			$_SESSION['loginSess']=$login;
			$_SESSION['mdpSess']=$mdp;
			$_SESSION['Delicious']=$oDelicious;
			if(TRACE)
				echo "ParamPage:Debug:oDelicious=".$oDelicious->sUsername."<br/>";
			$oDelicious->DeliciousRequest('posts/delete', array('url' => $sUrl));
			$con=$oDelicious->LastError();
			
			if ($con==2)
			{
				echo "Incorrect del.icio.us username or password";
				include("login.php");
				exit;
			}
		}else{
			include("login.php");
			exit;
		}
}
		
		
//function pour le cache
	function cParse($code){
		global $objSite;
		$s = new Sem($objSite,$objSite->XmlParam,"");
		return $s->parse($code);
	}
	
	
	
?>
