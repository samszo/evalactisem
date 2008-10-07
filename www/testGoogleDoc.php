<?php
//http://code.google.com/apis/visualization/documentation/index.html
?>
<html>
  <head>
	
	<link rel="stylesheet" href="CSS/iemlCycle.css" type="text/css">
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript" src="http://localhost/EvalActisem/library/js/iemlBoussole.js"></script>
    
    <script type="text/javascript">
      google.load("visualization", "1");
      function initialize() {
        var query = new google.visualization.Query("http://spreadsheets.google.com/pub?key=p8PAs8y8e1x3J43Fu2t0bDg");
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
	            	
	          	    td+=(data.getColumnType(col) == 'number' ? '<td id="td_'+id+'"  align="right"  ><div id="' +id+ '"  class="NoSelect" href=" " onclick="AfficheIeml(\''+id+'\');">' : '<td  id="td_'+ id +'"><div id="'+ id +'" class="NoSelect" href=" " onclick="AfficheIeml(\''+id+'\');">');
                    td+=(escapeHtml(data.getFormattedValue(row+1, col))) +'(' +(escapeHtml(data.getFormattedValue(row, col)))  + ') </div>';
                    td+=('</td>');
	           	}
           }else{
           		td+=(data.getColumnType(col) == 'number' ? '<td  align="right" ><div id=" "   href=""  onclick="AfficheIeml(\''+id+'\');" >' : '<td ><div id=""  href=" "  onclick="AfficheIeml(\''+id+'\');" >');
           		
           }
            
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
    
  </body>
</html>