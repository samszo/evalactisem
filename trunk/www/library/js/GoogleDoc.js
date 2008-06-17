function spreadsheets(container){
this.containerElement=container;
this.initialize=function () {
        var query = new google.visualization.Query('http://spreadsheets.google.com/tq?key=p8PAs8y8e1x2YTS7Zgag7Nw&hl=en');
        query.send(this.handleQueryResponse);  // Send the query with a callback function
}
this.handleQueryResponse= function(response) {

        if (response.isError()) {
          alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
          return;
        }
        alert('');
        var data = response.getDataTable(); 
        var html = [];
        html.push('<table border="1">');

        // Header row
        html.push('<tr><th>Seq</th>');
        for (var col = 0; col < data.getNumberOfColumns(); col++) {
          html.push('<th>' + this.escapeHtml(data.getColumnLabel(col)) + '</th>');
        }
        html.push('</tr>');

        for (var row = 0; row < data.getNumberOfRows(); row++) {
          html.push('<tr><td align="right">' + (row + 1) + '</td>');
          for (var col = 0; col < data.getNumberOfColumns(); col++) {
            data.getFormattedValue(row, col);
            html.push(data.getColumnType(col) == 'number' ? '<td align="right">' : '<td>');
            html.push(this.escapeHtml(data.getFormattedValue(row, col)));
            html.push('</td>');
          }
          html.push('</tr>');
        }
        html.push('</table>');
		this.containerElement.innerHTML = html.join('');
      }



// Utility function to escape HTML special characters
this.escapeHtml = function(text) {

  if (text == null)
    return '';
  return text.replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
this.spreadsheet=function(){
    
	google.load("visualization", "1");
	google.setOnLoadCallback(initialize);
	
}
}