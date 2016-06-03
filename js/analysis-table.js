//id is the div id in which the chart will be placed, with a leading #
function generateAnalysisTable(id, jsonFile) {
  d3.json(jsonFile, function (error, data){

    function tabulate(data, columns) {
      var table = d3.select(id).append('table').classed("analysis-table table table-bordered table-condensed table-hover",true).attr("id","analysis-table");
      var thead = table.append('thead');
      var	tbody = table.append('tbody');

      // append the header row
      thead.append('tr')
        .selectAll('th')
        .data(columns).enter()
        .append('th')
         .text(function (column) {

           // custom mapping for thead text
           switch(column) {
             case "url":
               return "URL";
             case "classification-fixed":
               return "Country Classification";
             case "classification-general-fixed":
               return "General Classification";
             case "ip-location":
               return "IP Location";
             case "tld-location":
               return "TLD Location";
             case "website-language":
               return "Page Language";
             default:
               return column;
           }
         });

      // create a row for each object in the data
      var rows = tbody.selectAll('tr')
        .data(data)
        .enter()
        .append('tr');

      // create a cell in each row for each column
      var cells = rows.selectAll('td')
        .data(function (row) {
          return columns.map(function (column) {
            return {column: column, value: row[column]};
          });
        })
        .enter()
        .append('td')
          .text(function (d) { return d.value; });


      var analysisTable = document.getElementById("analysis-table");
      for(var rowId = 1; rowId < analysisTable.rows.length; rowId++){
        var firstCellInRow = analysisTable.rows[rowId].cells[0];
        cellUrl = firstCellInRow.innerHTML;
        firstCellInRow.innerHTML = "<a href=\"" + cellUrl + "\">"+cellUrl+"</a>";
      }

      return table;
    }
    // render the table(s)
    var table = tabulate(data, ['url', 'classification-fixed', 'classification-general-fixed', 'ip-location', 'tld-location', 'website-language' ]); // 7 column table
  });
}
