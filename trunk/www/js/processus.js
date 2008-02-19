
function mPressed(evt) {
  //alert('Le bouton a été pressé !');
  //dsc =	document.getElementById(event.currentTarget.id).label;
  dsc = evt["target"]["lbl"];
  ShowProc(1,evt.currentTarget.tagName,dsc,evt.currentTarget.value);
}

function ShowProc(id,code,desc,trad)
{
	try  {
		//alert(id+','+code+','+desc);
		//récupération des valeurs
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
	//récupération des valeurs
	id = document.getElementById("proc-id").value;
	code = document.getElementById("proc-code").value;
	desc = document.getElementById("proc-desc").value;

	//construction de la requete
	url = urlExeAjax+"?f=SetProc&id="+id+"&code="+code+"&desc="+desc;

	//vérification des valeurs
	if(code=="" || desc=="")
		document.getElementById("proc-message").value = "Veuillez saisir une valeur pour chaque champ";
	else
		AjaxRequest(url,"RefreshResult","proc-message,overlay/menu.php?box=box1,box1");

}