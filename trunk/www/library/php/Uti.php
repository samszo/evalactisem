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
	
  	public function GetUsersIds($users, $GetRs=false)
	{
				
		$sql = "SELECT uti_id 
			FROM ieml_uti 
			WHERE uti_login IN ('".implode("','", $users)."')";
				
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rs = $db->query($sql);
		$num=mysql_affected_rows(); 
		$db->close();

		if($GetRs){
			return $rs;
		}else{
			//création de la liste des ids
			$ids="";
			while($r=mysql_fetch_assoc($rs))
			{	
				$ids.= $r["uti_id"].",";	
			}
			$ids = substr($ids,0,-1);
			return $ids;
		} 
		
	}	

}


?>