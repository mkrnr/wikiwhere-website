<!DOCTYPE html>
<html>
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <script type="text/javascript" src="js/url-mod.js"></script>

    <?php
      $article_url = filter_input(INPUT_GET, 'url');

      if( urldecode($article_url) == $article_url){
        $article_url_encoded = urlencode ($article_url);
      }else{
        $article_url_encoded = $article_url;
        $article_url = urldecode($article_url);
      }

      $new_crawl=filter_input(INPUT_GET, 'new-crawl');

      $python=filter_input(INPUT_GET, 'python');
      if (!isset($python)){
        $python="/usr/bin/python2";
      }
    ?>


    <script type="text/javascript" src="js/d3.js"></script>

    <!-- source: http://d3js.org/topojson.v1.min.js -->
    <script type="text/javascript" src="js/topojson.v1.min.js"></script>

    <!-- source: http://datamaps.github.io/scripts/datamaps.world.min.js?v=1 -->
    <script type="text/javascript" src="js/datamaps.world.min.js"></script>

    <!-- source: http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js -->
    <script type="text/javascript" src="js/d3.tip.v0.6.3.js"></script>

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
      <h1>Article Analysis</h1>
      <div id="analysis-date"></div>
      <h2 id="analysis-url">Article: <a href="<?php echo $article_url; ?>"><?php echo $article_url; ?></a></h2>

      <a title="Click to go to JSON file"
        href="#" onclick="toJson();return false;">Get JSON file</a>

      <h3 class="m-t-30">Heatmap</h3>
      <p>Displays the general classification results.</p>
      <div id="map" style="position: relative; "></div>
      <div  id="gradient" >
        <table  id="gradient-table">
          <tbody>
            <tr>
              <td >
               low
              </td >
              <td style="text-align:right;">
                high
              </td >
            </tr>
            <tr>
              <td colspan="2">
                <img id="gradient-image" src="data/images/heatmap-gradient.png" />
              </td >
            </tr>
          </tbody>
        </table>
      </div>
      <h3 class="m-t-40">Barcharts</h3>
      <h4>General Classification</h4>
      <div id="bar"></div>
      <h3 class="m-t-40">Detailed Results</h3>
      <div id="table" class="table-responsive"></div>
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
      document.getElementById("analysis-date").innerHTML = "from <?php echo $analysis_date; ?>";
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
          responsive: true,
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
	    	});

        window.addEventListener('resize', function() {
          map.resize();
        });
      });
    </script>

    <script>
      var margin = {top: 10, right: 20, bottom: 30, left: 30};
      var width = 960 - margin.left - margin.right;
      var height = 500 - margin.top - margin.bottom;


      var x = d3.scale.ordinal()
        .rangeRoundBands([0, width], .1);

      var y = d3.scale.linear()
        .range([height, 0]);

      var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom");

      var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left")

      var tip = d3.tip()
        .attr('class', 'd3-tip')
        .offset([-10, 0])
        .html(function(d) {
          return "<strong>Frequency:</strong> <span style='color:red'>" + d.count + "</span>";
        })

      var svg = d3.select("#bar").append("svg")
        .attr("preserveAspectRatio", "xMinYMin meet")
        .attr("viewBox", "0 0 960 500")
        .append("g")
          .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

      svg.call(tip);

      d3.json("<?php echo $article_counts_path; ?>", function(data) {
        x.domain(data.map(function(d) { return d.label; }));
        y.domain([0, d3.max(data, function(d) { return d.count; })]);

        svg.append("g")
          .attr("class", "x axis")
          .attr("transform", "translate(0," + height + ")")
          .call(xAxis);

        svg.append("g")
          .attr("class", "y axis")
          .call(yAxis)

        svg.selectAll(".bar")
          .data(data)
          .enter().append("rect")
            .attr("class", "bar")
            .attr("x", function(d) { return x(d.label); })
            .attr("width", x.rangeBand())
            .attr("y", function(d) { return y(d.count); })
            .attr("height", function(d) { return height - y(d.count); })
      });

      function type(d) {
        d.count = +d.count;
        return d;
      }
    </script>

    <script>
      d3.json("<?php echo $article_analysis_path; ?>", function (error, data){

        function tabulate(data, columns) {
          var table = d3.select('#table').append('table').classed("table table-bordered table-condensed table-hover",true);
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
                 case "classification":
                   return "Country Classification";
                 case "classification-general":
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

          return table;
        }
        // render the table(s)
        var table = tabulate(data, ['url', 'classification', 'classification-general', 'ip-location', 'tld-location', 'website-language' ]); // 7 column table
      });
    </script>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
