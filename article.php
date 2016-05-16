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

    <!-- TODO host ourself -->
    <script src="http://d3js.org/topojson.v1.min.js"></script>
    <script src="http://datamaps.github.io/scripts/datamaps.world.min.js?v=1"></script>

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
      <div id="analysis-date"></div>

      <a title="Click to go to JSON file"
        href="#" onclick="toJson();return false;">Get JSON file</a>

    <div id="map" align="center"></div>
    <div id="pie" align="center"></div>
    <div id="table" align="center"></div>
    </div>
    <?php include "php/get-article.php";?>

    <script>
      var article_url = window.location.href ;
      var url_removed = removeVariableFromURL(article_url, "new-crawl");
      history.pushState(null, '', url_removed);
    </script>

    <?php
      $article_analysis_path = $article_path . "/analysis.json";
      $article_counts_path = $article_path . "/counts-classification-general.json";
      $article_map_data_path = $article_path . "/map-data.json";
      $article_info_path = $article_path . "/info.json";

      $info_string = file_get_contents($article_info_path);
      $info_json=json_decode($info_string,true);
      $analysis_date =date_format(date_create($info_json["analysis-date"]), 'F d, Y');
    ?>

    <script>
      document.getElementById("analysis-date").innerHTML = "Analysis from <?php echo $analysis_date; ?>";
    </script>

     <script>
       //basic map config with custom fills, mercator projection
	var data
	var countries = {}
	var fillCols = {}
	fillCols["defaultFill"] = '#808080'
	d3.json("<?php echo $article_map_data_path; ?>", function(dataset) {
		data = dataset
		//console.log(data)
		data.forEach(function(d, i) {
			//
			fillCols[data[i].label] = data[i].color
			//console.log(fillCols)
			//create Label
			tmp1 = data[i].label
			//console.log(tmp1)
			//create fillKey
			tmp2 = {fillKey: data[i].label, count: data[i].count}
			//console.log(tmp2)
			//add label : {fillKey: key} to object
			countries[tmp1] = tmp2
			//console.log(countries)
		});

	   //creates map with generated fill and data
		var map = new Datamap({
			scope: 'world',
			element: document.getElementById('map'),
			projection: 'mercator',
      //TODO fix responsive behavior
      responsive: true,
      height: null,
			fills: fillCols,
			data: countries,
			geographyConfig: {
				popupTemplate: function(geo, data) {
				 var count
				 if (data == null) {count = 0}
				 else {count = data.count}
					return ['<div class="hoverinfo"><strong>',
							'Links from ' + geo.properties.name,
							': ' + count,
							'</strong></div>'].join('');
            }
			}
		})

      })

    // resize map
    window.addEventListener('resize', function() {
        map.resize();
    });
     </script>

    <script>
	var data;

	//var article_counts_path = article_path;

	d3.json("<?php echo $article_counts_path; ?>", function(dataset) {
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
        d3.json("<?php echo $article_analysis_path; ?>", function (error, data){

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
