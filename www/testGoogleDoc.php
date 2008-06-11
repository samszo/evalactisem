<?php
//http://code.google.com/apis/visualization/documentation/index.html
?>
<html>
  <head>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1");
      function initialize() {
        var query = new google.visualization.Query("http://spreadsheets.google.com/tq?key=pqDK7wzuzrlQIFZAWEDZJAQ&hl=en");
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
        html.push('<table border="1">');

        // Header row
        html.push('<tr><th>Seq</th>');
        for (var col = 0; col < data.getNumberOfColumns(); col++) {
          html.push('<th>' + escapeHtml(data.getColumnLabel(col)) + '</th>');
        }
        html.push('</tr>');

        for (var row = 0; row < data.getNumberOfRows(); row++) {
          html.push('<tr><td align="right">' + (row + 1) + '</td>');
          for (var col = 0; col < data.getNumberOfColumns(); col++) {
            html.push(data.getColumnType(col) == 'number' ? '<td align="right">' : '<td>');
            html.push(escapeHtml(data.getFormattedValue(row, col)));
            html.push('</td>');
          }
          html.push('</tr>');
        }
        html.push('</table>');

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
