<?php
//http://code.google.com/apis/visualization/documentation/index.html
?>
<html>
  <head>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1");
      function initialize() {
        var query = new google.visualization.Query("http://spreadsheets.google.com/tq?key=p8PAs8y8e1x2YTS7Zgag7Nw&hl=en");
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
        var idSrc;
        var idDes;
        var idPredArr=[];
        var idPred;
        var idS;
        var i=1;
        html.push('<table border="1" id="Cycle">');

        // Header row
        //html.push('<tr><th>Seq</th>');
        html.push('<tr>');
        for (var col = 0; col < data.getNumberOfColumns(); col++) {
          html.push('<th>' + escapeHtml(data.getColumnLabel(col)) + '</th>');
        }
        html.push('</tr>');

        for (var row = 0; row < data.getNumberOfRows(); row++) {
         // html.push('<tr><td align="right">' + (row + 1) + '</td>');
          tr="";
          td="";
          //html.push('<tr>');
          for (var col = 0; col < data.getNumberOfColumns(); col++) {
            if((escapeHtml(data.getFormattedValue(row, col))!="")&&((row%2)==0)){
            	id=escapeHtml(data.getFormattedValue(row, col)).split(":.");
           	    id[1]=id[1].replace('-','').replace('.','');
           	    idDes='*'+id[1]+':.**';
           	    td+=(data.getColumnType(col) == 'number' ? '<td id="'+idDes+'"  align="right">' : '<td id="'+idDes+ '"  >');
           	    
           	}else{
            	td+=(data.getColumnType(col) == 'number' ? '<td id="descp_'+idDes+ '"  align="right">' : '<td id="descp_'+idDes+ '" >');
            }
            td+=(escapeHtml(data.getFormattedValue(row, col)));
            td+=('</td>');
          }
          if(idPredArr.length!=0){
            idPred= idPredArr.pop();
            
            if(id[0]!=  idPred){
             	idS=id[0];
            }else{
             	idS=id[0]+'_'+i;
             	i++;
            }
          }else{
          	idS=id[0];
          }
         idPredArr.push(id[0]);
 	      if((row%2)==0){
 	      	alert(idSrc='*' + idS +':.**');
          }else{
          	alert(idSrc='descp_*' + idS + ':.**');
          }
          	tr='<tr id="'+idSrc+'">'+td+'</tr>';
          	html.push(tr);
      }
       
       
        html.push('</table>');
       	alert(html.join(''));
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