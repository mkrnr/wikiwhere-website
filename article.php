<!DOCTYPE html>
<html>
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <script type="text/javascript" src="js/url-mod.js"></script>

    <script type="text/javascript" src="js/bar-chart.js"></script>
    <script type="text/javascript" src="js/heat-map.js"></script>
    <script type="text/javascript" src="js/analysis-table.js"></script>

    <?php
      $article_url_input = filter_input(INPUT_GET, 'url');

      $article_url = urldecode ($article_url_input);
      $article_url_encoded = urlencode ($article_url);

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
      <div id="heat-map" style="position: relative; "></div>
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
      <div id="general-classification-bar"></div>
      <h4>Country Classification</h4>
      <div id="country-classification-bar"></div>
      <h4>IP Location</h4>
      <div id="ip-location-bar"></div>
      <h4>TLD Location</h4>
      <div id="tld-location-bar"></div>
      <h4>Page Language</h4>
      <div id="page-language-bar"></div>
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

      $general_classification_counts_path = $article_path . "/counts-classification-general-fixed.json";
      $country_classification_counts_path = $article_path . "/counts-classification-fixed.json";
      $ip_location_counts_path = $article_path . "/counts-ip-location.json";
      $tld_location_counts_path = $article_path . "/counts-tld-location.json";
      $page_language_counts_path = $article_path . "/counts-website-language.json";

      $article_map_data_path = $article_path . "/map-data.json";
      $article_info_path = $article_path . "/info.json";

      $info_string = file_get_contents($article_info_path);
      $info_json=json_decode($info_string,true);
      $analysis_date =date_format(date_create($info_json["analysis-date"]), 'F d, Y');
    ?>

    <script>
      document.getElementById("analysis-date").innerHTML = "from <?php echo $analysis_date; ?>";

      generateHeatMap("heat-map","<?php echo $article_map_data_path; ?>");

      generateBarChart("#general-classification-bar","<?php echo $general_classification_counts_path; ?>");
      generateBarChart("#country-classification-bar","<?php echo $country_classification_counts_path; ?>");
      generateBarChart("#ip-location-bar","<?php echo $ip_location_counts_path; ?>");
      generateBarChart("#tld-location-bar","<?php echo $tld_location_counts_path; ?>");
      generateBarChart("#page-language-bar","<?php echo $page_language_counts_path; ?>");

      generateAnalysisTable("#table","<?php echo $article_analysis_path; ?>");
    </script>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
