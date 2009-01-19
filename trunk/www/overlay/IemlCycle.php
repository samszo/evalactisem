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

  <body onload=''>
  <script type="text/javascript">
    
  	var key="<?php echo $_GET['key']; ?>";
  	Load(key);
  </script>
    <div id="<?php echo $_GET['key']; ?>_div" style="height:auto;width:auto;"></div>
  </body>
</html>

