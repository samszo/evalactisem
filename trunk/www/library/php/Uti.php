<?php

class Uti{
	Public $id;
	Public $login;
	Public $site;
	
	function __construct($site,$login,$id=false){
		
		$this->site = $site;
		if($login){
			$this->login = $login;
			$this->id = $this->GetId();
		}
		if($id){
			$this->id = $id;
			$this->login = $this->GetLogin();
		}
		
	}
	
	function GetId(){
		
		$req = $this->site->RequeteSelect('Verif_Exist_Utilisateur',array(array("-login-",$this->login)));
		$res=mysql_fetch_array($req);
		if(mysql_num_rows($req)==0){
			$uti_id = $this->site->RequeteInsert('Enrg_Utilisateur',array(array("-login-",$this->login)));
			return $uti_id;
		}

		return $res[0]  ;
	}
	
	function GetLogin(){
		
		$req = $this->site->RequeteSelect('Get_Login_Utilisateur',array(array("-id-",$this->id)));
		$res=mysql_fetch_array($req);
		return $res[0]  ;
	}
	
	

}


?>