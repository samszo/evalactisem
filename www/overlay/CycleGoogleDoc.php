<?php
//http://code.google.com/apis/visualization/documentation/index.html
//header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
echo '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
?>

<box  xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

    <script  src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1");
      function initialize() {
        var query = new google.visualization.Query('http://spreadsheets.google.com/tq?key=p8PAs8y8e1x2YTS7Zgag7Nw&hl=en');
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
        var xul = [];
        xul.push('<listbox id="iemlCycle" >');

        // Header row
        xul.push('<listcols>')
        //xul.push('<tr><th>Seq</th>');
        for (var col = 0; col < data.getNumberOfColumns(); col++) {
          xul.push('<listcol flex="1">' + escapeHtml(data.getColumnLabel(col)) + '</listcol>');
        }
        xul.push('</listcols>');

        for (var row = 0; row < data.getNumberOfRows(); row++) {
          //xul.push('<tr><td align="right">' + (row + 1) + '</td>');
          xul.push("<listitem>")
          for (var col = 0; col < data.getNumberOfColumns(); col++) {
            xul.push(data.getColumnType(col) == 'number' ? '< align="right">' : '<listcell label="');
            xul.push(escapeHtml(data.getFormattedValue(row, col)));
            xul.push('" />');
          
          }
          xul.push('</listitem>');
        }
        xul.push('</listbox>');
        var parser=new DOMParser();
        var ListBox=parser.parseFromString(xul.join(''),"text/xml");
        alert(xul.join(''));
        document.getElementById('tablediv').appendChild(ListBox.documentElement);
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
    <box id='tablediv'>
   </box>
   </box>
   
    
  
  

 

