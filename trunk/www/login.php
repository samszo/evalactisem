<?php

session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EvalActiSem</title>
<SCRIPT LANGUAGE="JavaScript">

    <!--
    if (window !=top ) {top.location=window.location;}
   //-->

</SCRIPT>
<style type="text/css">
#globalPass
	{
	position:absolute;
	left:50%; 
	top:50%;
	width:300px;
	height:416px;
	margin-top: -208px; /* moiti� de la hauteur */
	margin-left: -105px; /* moiti� de la largeur */
	border: 1px solid #FFFFFF;
	background-image:url(images/log5.gif);
	background-repeat:no-repeat;
	background-color:#FFFFFF;
	font-family:Helvetica, sans-serif;
	font-size:15px;
	color:#000000;
    }

.BlocTextePass
	{
	width:280px;
	margin: 5px;
	margin-top:80px;
	color:white
    }
</style>
</head>

<body bgcolor="#ffffff">
	<div id='globalPass'>
		<div class='BlocTextePass'>	
			<p align="center">Vous allez entrer dans une zone s�curis�e.</p>		
			<form name="formulaire" method="post" action="index1.php">
			<p align="center">login :<br /> 
			<input name="login_uti" type="text" id="login_uti">
			</p>
			<p align="center">mot de passe : <br />
			<input name="mdp_uti" type="password" id="mdp_uti">
			</p>
			<p align="center">
			<input type="submit" name="Submit" value="Connexion">
			</p>
			</form>
		</div>
	</div><!--Fin div globalPass-->
</body>
</html>
