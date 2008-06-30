<?php
//http://code.google.com/apis/visualization/documentation/index.html
?>
<html>
  <head>
	
	<link rel="stylesheet" href="CSS/iemlCycle.css" type="text/css">
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1");
      function initialize() {
        var query = new google.visualization.Query("http://spreadsheets.google.com/tq?key=p8PAs8y8e1x3J43Fu2t0bDg&hl=en");
        query.send(handleQueryResponse);  // Send the query with a callback function
      }
      google.setOnLoadCallback(initialize); // Set callback to run when API is loaded

      // Query response handler function.
      function handleQueryResponse(response) {
       
        if (response.isError()) {
          alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
          return;
        }

        var data = response.getDataTable(); 
        var html = [];
        var td="";
        var tr="";
        var id;
        var i=1;
        html.push('<table border="1" id="**t:t:.*" >');
	 	 // Header row
        //html.push('<tr><th>Seq</th>');
        html.push('<tr id="header">');
        for (var col = 0; col < data.getNumberOfColumns()-1; col++) {
          html.push('<th>' + escapeHtml(data.getColumnLabel(col)) + '</th>');
        }
        html.push('</tr>');

        for (var row = 0; row < data.getNumberOfRows()-1; row++) {
         // html.push('<tr><td align="right">' + (row + 1) + '</td>');
          tr="";
          td="";
          //html.push('<tr>');
          for (var col = 0; col < data.getNumberOfColumns()-1; col++) {
            id="*"+escapeHtml(data.getFormattedValue(row, col))+"**";
            if(id!="***"){
	            
	           if((row%2)==0){
	            	
	          	    td+=(data.getColumnType(col) == 'number' ? '<td  align="right" ><a id="'+ id + '"  class="NoSelect" href="http://www.ieml.org/french/ooom_1.html">' : '<td ><a id="'+ id + '" class="NoSelect" href="http://www.ieml.org/french/ooom_1.html" >');
	           	    
	           	
	           	}else{
	           		Descpid="descp_*"+escapeHtml(data.getFormattedValue(row-1, col))+"**";
	            	td+=(data.getColumnType(col) == 'number' ? '<td  align="right" ><a id="'+Descpid+ '"  class="NoSelectDesc" href="http://www.ieml.org/french/ooom_1.html">' : '<td ><a id="'+Descpid+ '"  class="NoSelectDesc"  href="http://www.ieml.org/french/ooom_1.html" >');
	            }
           }else{
           	
           		td+=(data.getColumnType(col) == 'number' ? '<td ><a a id="" href="http://www.ieml.org/french/ooom_1.html">' : '<td ><a id=""  href="http://www.ieml.org/french/ooom_1.html">');

			}
            td+=(escapeHtml(data.getFormattedValue(row, col)))+'</a>';
            td+=('</td>');
          }
               	tr='<tr >'+td+'</tr>';
          	html.push(tr);
      }
       
       
        html.push('</table>');
       	//alert(html.join(''));
        document.getElementById('tablediv').innerHTML = html.join('');
      }

      function escapeHtml(text) {
        if (text == null)
          return '';

        return text.replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;');
      }

    </script>
  </head>

  <body>
    <div id="tablediv">Loading...</div>
<script src="http://spreadsheets.google.com/gpub?url=http%3A%2F%2F4b08ep9s-a.gmodules.com%2Fig%2Fifr%3Fup__table_query_url%3Dhttp%253A%252F%252Fspreadsheets.google.com%252Ftq%253Frange%253DA2%25253AB5%2526key%253Dp9ISv2bT_puZmAV340lRUAQ%2526gid%253D0%2526pub%253D1%26up_title%3Dma%2520carte%26up_show_tooltip%3D1%26up_enable_wheel%3D1%26up__table_query_refresh_interval%3D0%26url%3Dhttp%253A%252F%252Fwww.google.com%252Fig%252Fmodules%252Fmap.xml&height=281&width=450"></script>    
  </body>
</html>