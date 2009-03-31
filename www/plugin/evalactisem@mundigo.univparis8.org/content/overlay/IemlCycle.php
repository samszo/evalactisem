<html>
  <head>
    <link rel="stylesheet" href="../CSS/iemlCycle.css" type="text/css" />
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript" src="../library/js/ajax.js"></script>
    <script type="text/javascript" src="../library/js/Interface.js"></script>
    <script src="../library/js/iemlBoussole.js"/>
    <script type="text/javascript">
    	var urlAjax = "<?php echo PathWeb; ?>";
    </script>
  </head>

  <body onload='' id='body'>
  <script type="text/javascript">
  	for(k=1;k<=2;k++){
	  if(window.parent.document.getElementById('CycleLab_'+k).getAttribute('value')!='')
	   var key=window.parent.document.getElementById('CycleLab_'+k).getAttribute('value');
	}
	body=document.getElementById('body');
	div=document.createElement('div');
	div.setAttribute('id',key+'_div');
	body.appendChild(div);
  	Load(key);
  </script>
    <div id="divCycle" style="height:auto;width:auto;"></div>
  </body>
</html>