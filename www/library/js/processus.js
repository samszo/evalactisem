function mPressed(evt) {
  //alert('Le bouton a �t� press� !');
  //dsc =	document.getElementById(event.currentTarget.id).label;
  ShowProc(
	evt["target"]["sqlId"]
	,evt["target"]["code"]
	,evt["target"]["desc"]
	,evt["target"]["trad"]
	);
	evt.stopPropagation();
}

function ShowProc(id,code,desc,trad)
{
	try  {
		//alert(id+','+code+','+desc);
		//r�cup�ration des valeurs
		document.getElementById("proc-id").value = id;
		document.getElementById("proc-code").value = code;
		document.getElementById("proc-desc").value = desc;
		document.getElementById("proc-trad").value = trad;
		document.getElementById("proc-trace").value += " "+desc;	
	} catch (e) {
	}	
}

function SetProc()
{
	//r�cup�ration des valeurs
	id = document.getElementById("proc-id").value;
	code = document.getElementById("proc-code").value;
	desc = document.getElementById("proc-desc").value;

	//construction de la requete
	url = urlExeAjax+"?f=SetProc&id="+id+"&code="+code+"&desc="+desc;

	//v�rification des valeurs
	if(code=="" || desc=="")
		document.getElementById("proc-message").value = "Veuillez saisir une valeur pour chaque champ";
	else
		AjaxRequest(url,"RefreshResult","proc-message,overlay/menu.php?box=box1,box1");

}
