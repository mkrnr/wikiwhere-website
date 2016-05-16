<!DOCTYPE html>
<html>
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <script type="text/javascript" src="js/url-mod.js"></script>

    <?php
      $article_url = filter_input(INPUT_GET, 'url');
      $new_crawl=filter_input(INPUT_GET, 'new-crawl');

      $python=filter_input(INPUT_GET, 'python');
      if (!isset($python)){
        $python="/usr/bin/python2";
      }
    ?>


    <script type="text/javascript" src="js/d3.js"></script>

    <script>
      function toJson() {
        var url = window.location.href ;
        var load = url.replace("/article.php","/json.php");
        window.location = load;
      }
    </script>


    <title>wikiwhere - <?php echo $article_url;?></title>
  </head>
  <body>
<?php echo file_get_contents("templates/header.php") ?>

    <div class="container">
      <h1><?php echo $article_url; ?></h1>

      <a title="Click to go to JSON file"
        href="#" onclick="toJson();return false;">Get JSON file</a>
    </div>
    <div id="pie" align="center"></div>
    <div id="table" align="center"></div>
    <?php include 'php/get-article.php';?>

    <script>
      var article_url = window.location.href ;
      var url_removed = removeVariableFromURL(article_url, "new-crawl");
      history.pushState(null, '', url_removed);
    </script>

    <script>
      var article_path = '<?php echo $article_path; ?>';
      var article_analysis_path = article_path.concat("/analysis.json");
      var article_counts_path = article_path.concat("/counts-classification-general.json");
    </script>
    <script>
	var data;

	//var article_counts_path = article_path;

	d3.json(article_counts_path, function(dataset) {
    data = dataset;

	var width = 1000,
	  height = 400,
	  radius = Math.min(width, height) / 2;

	var color = d3.scale.category20();

	var arc = d3.svg.arc()
	  .outerRadius(radius - 10)
	  .innerRadius(radius - 70);

	var pie = d3.layout.pie()
	  .sort(null)
	  .value(function(d) {
		return d.count;
	  });



	var svg = d3.select("#pie").append("svg")
	  .attr("width", width)
	  .attr("height", height)
	  .append("g")
	  .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

	var g = svg.selectAll(".arc")
	  .data(pie(data))
	  .enter().append("g")
	  .attr("class", "arc");

	g.append("path")
	  .attr("d", arc)
	  .style("fill", function(d) {
		return color(d.data.label);
	  });

	g.append("text")
	  .attr("transform", function(d) {
		return "translate(" + arc.centroid(d) + ")";
	  })
	  .attr("dy", ".35em")
	  .style("text-anchor", "middle")
	  .text(function(d) {
		return d.data.label+": "+d.data.count;
	  });
	  });
    </script>
    <script>
        d3.json(article_analysis_path, function (error, data){

	function tabulate(data, columns) {
		var table = d3.select('#table').append('table')
		var thead = table.append('thead')
		var	tbody = table.append('tbody');

		// append the header row
		thead.append('tr')
		  .selectAll('th')
		  .data(columns).enter()
		  .append('th')
		    .text(function (column) { return column; });

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

	  return table;
	}
	// render the table(s)
	var table = tabulate(data, ['url', 'classification', 'classification-general', 'ip-location', 'tld-location', 'website-language', 'wikipedia-language']); // 7 column table

	});

    </script>
    <script>
      //var article_path = '<?php echo $article_path; ?>';
      //document.write("<p>Loaded via Python, displayed via JS: </p>");
      //try {
      //  article_json_parsed = JSON.parse(article_json_string);
      //} catch (e) {
      //  document.write("JSON is not valid");
      //}
      //document.write(article_path);

      // print out json:
      //document.write('<pre id="json"></pre>');
      //document.getElementById("json").innerHTML = JSON.stringify(article_json_parsed, undefined, 2);
    </script>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
