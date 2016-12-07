<!DOCTYPE html>
<html lang="en">
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <title>wikiwhere - About</title>

  </head>

  <body>
<?php echo file_get_contents("templates/header.php") ?>
    <script>
        document.getElementById("about").setAttribute("class", "active");
    </script>

    <div class="container">
      <h1>About Wikiwhere</h1>
      <p>
        <a href="http://wikiwhere.west.uni-koblenz.de/">Wikiwhere</a> is the result of the research lab 2015-2016 at the <a href="https://www.uni-koblenz-landau.de/de">University of Koblenz-Landau</a> in cooperation with the <a href="http://www.gesis.org/">GESIS – Leibniz Institute for the Social Sciences</a>.
      </p>
      <p>
        Wikipedia articles about the same event in different language editions have different sources of information.
        Wikiwhere helps to answer the question where this information comes from by analyzing and visualizing the geographic location of external links that are displayed on a given Wikipedia article.
        Instead of relying solely on the IP location of a given URL, our machine learning models additionally consider the top level domain and the website language.
      </p>
      <p>
        Publication: Körner, M., Tatiana, T., Windhäuser, F., Wagner, C., &amp; Flöck, F. (2016) <a href="https://arxiv.org/abs/1612.00985">Wikiwhere: An interactive tool for studying the geographical provenance of Wikipedia references</a>. arXiv e-prints. (Updated version of the accepted abstract for the <a href="http://www.gesis.org/css-wintersymposium/program/accepted-posters/">CSS Winter Symposium 2016 poster session</a>.)
      </p>
      <h2>Why should I use wikiwhere?</h2>
		<p>
        You might just want a starting point for checking the sources, but there may be other interesting things you can find out.
		For instance you could compare different language versions of the same article to find out whether the different version have a geographical bias.
		<div class="well text-center">
			<span>Comparison between the German and Russian version of an article:</span>
      <div class="row">
			  <div class="col-md-6">
		  		<img class="screenshot" id="en-crimea" src="data/images/de-crimea.png" />
		  	</div>
			  <div class="col-md-6">
		  		<img class="screenshot" id="ru-crimea" src="data/images/ru-crimea.png" />
			  </div>
			</div>
		</div>
		<p>You could even do larger investigations on Wikipedia sources. Do national, cross-national patterns exist? Do certain language versions of Wikipedia use more diverse sources?
		There is still more you could find out. Get creative!
      </p>
	  <p>
        In the following we will explain our approach and the different ways of how to use our web service. Additionally we provide the source code for this website and the underlying python code that runs the analysis and data extraction under a MIT license.
      </p>
	    <h2>Approach</h2>
	    <p>
        We use the term reference to refer to an URL that leads from a given Wikipedia article to another web page that is not associated with the Wikimedia foundation.
        To obtain a training set for the machine learning model, we retrieved geo-location information on websites from <a href="http://dbpedia.org/">DBpedia</a> SPARQL endpoints.
        In order to evaluate the accuracy of the ground truth we manually checked 255 locations for references that we extracted from the English DBpedia.
        The resulting accuracy was 95% (see <a href="#dbpedia-location-extraction">DBpedia Location Extraction</a>).
        We then randomly extracted URLs from Wikipedia articles which link to websites for which we have the geo-location.
        For this list of URLs we computed the IP-location, top level domain, and website language and used these features in combination with the DBpedia geo-location on the country level to train the model.
	    <p>
	    </p>
        The following subsections provide some more details on the individual steps.
	    </p>
	    <h2>Data Collection</h2>
      <p>
        In the prediction model we used three features:
        The IP location of the URL, top level domain (TLD) location of the URL and language of its content.
        The target variable is the country that we retrieved from the DBpedia geo-location that was linked to the given website.
        We built separate prediction models for the following languages:
        English, German, French, Italian, Spanish, Ukrainian, Slovak, and Dutch.
        These are the languages for which there currently exist DBpedia knowledge bases.
        We also built a general model that combines the data from all DBpedia knowledge bases.
        The Python modules for the data collection can be found in the <a href="https://github.com/mkrnr/wikiwhere/tree/master/wikiwhere/feature_extraction">feature_extraction</a> and <a href="https://github.com/mkrnr/wikiwhere/tree/master/wikiwhere/location_extraction">location_extraction</a> packages in our <a href="https://github.com/mkrnr/wikiwhere">wikiwhere Github repository</a>.
	    </p>
	    <h3>IP Location Extraction</h3>
      <p>
        There exist several public APIs that return the geo-coordinates for a given IP.
        Our first implementation relied on the <a href="https://developers.google.com/maps/">Google Maps API</a>.
        Yet, due to the high number of requests that we run for the analysis of larger Wikipedia articles, we quickly reached the <a href="https://developers.google.com/maps/premium/usage-limits#google-places-api">daily limits</a>.
        Our final solution uses the <a href="https://pypi.python.org/pypi/geoip2"><code>geoip2</code> Python library</a> in combination with the GeoLite2 data created by MaxMind, available from
<a href="http://www.maxmind.com">http://www.maxmind.com</a>.
        This allows us to locally calculate IP-locations on a country level.
        One potential source of wrong IP-locations could result from websites that use content delivery networks such as <a href="https://www.akamai.com/">Akamai</a> since their servers are globally distributed.
        Thereby, the localization of the content server can be missleading.
        One goal of the future work could be a better handling of such cases.
      </p>
	    <h3>Top Level Domain Extraction</h3>
      <p>
        As a first step we created 2 datasets by parsing HTML tables.
        The first, taken from the <a href="http://www.iana.org/domains/root/db">IANA</a> website, contains information on whether a TLD is generic or a country-code.
        The second, from the <a href="https://www.cia.gov/library/publications/the-world-factbook/appendix/appendix-d.html">CIA</a>, gives us the top-level domains corresponding to different country codes.
        With help from the Python package <code>tld</code> we extracted the TLD from a URL.
        If it's a TLD with a "." inside, for example "co.uk", we only considered the part after the final "." for further analysis.
        We then used our first dataset to find out whether the TLD is a country-code.
        If that's the case we took the corresponding ISO 2 character code from the second dataset.
        If it's not a country-code we set the TLD as the parameter, for example "COM".
        Errors in this process can be a "bad URL" or no found TLD within the usage of the TLD package or cases unknown to the IANA dataset.
        Empty values can be a result of a TLD which is a country-code but not corresponding to any specific country, for example ".eu".
	    </p>
	    <h3>Website Language Extraction</h3>
	    <p>
        In order to determine the language of a given Website, we first request the website content using the <a href="https://docs.python.org/2/library/urllib.html"><code>urllib</code> package</a>.
        The next step is extracting the actual textual content of the website out of the HTML code.
      <div id="dbpedia-location-extraction"></div><!--sorry-->
        This is done by first generating Markdown text using the <a href="https://pypi.python.org/pypi/html2text"><code>html2text</code> package</a> and then using the <a href="https://pypi.python.org/pypi/beautifulsoup4"><code>beautifulsoup</code> package</a> to extract the text from Markdown via another conversion to HTML.
        After extracting the text we use the <a href="https://github.com/Mimino666/langdetect"><code>langdetect</code> package</a> for detecting the language.
	    </p>
	    <h3>DBpedia Location Extraction</h3>
        In order to gather a large amount of geo-locations for websites we used the <a href="http://dbpedia.org/sparql">DBpedia SPARQL endpoints</a>.
        The SPARQL requests were made with the <a href="https://pypi.python.org/pypi/SPARQLWrapper/1.7.6"><code>SPARQLWrapper</code> package</code></a>.
        The first part of getting a location is to associate a given URL to a DBpedia entity.
        For this entity we then query for it's location, location city, or the location of it's parent company.
        The SPARQL query that we used can be found <a href="http://wikiwhere.west.uni-koblenz.de/data/dbpedia-locations.rq">here</a>.
        It is possible to copy this query into the field in the <a href="http://dbpedia.org/sparql">English DBpedia SPARQL endpoint</a>.
        In cases where we retrieve more than one location for a URL we perform a majority voting.
        For example, the SPARQL query returns four locations for the URL <a href="http://www.treasury.gov.au/">http://www.treasury.gov.au/</a>.
        The geo-coordinates for all four locations only differ in the second digit after the comma.
        With our current threshold, geo-coordinates are considered the same if they differ by less than 0.1.
        Another example is <a href="http://www.bangladesh.gov.bd/maps/images/pabna/Chatmohar.gif">http://www.bangladesh.gov.bd/maps/images/pabna/Chatmohar.gif</a> for which the geo-coordinates two of the four locations differ by 0.2.
        In this case a majority voting takes place.
        Since there are two different locations which both appear two times, one of them is selected at random.
        For the English DBpedia, a majority voting was necessary for 5179 out of the 162827 URLs that we extracted from the SPARQL endpoint.
        In addition, we do not consider URLs that contain "web.archive.org" and "webcitation.org" since they usually reference to another website and the DBpedia location also refers to the referenced website.
	    <p>
        Since the quality of the geo-location that we extract from DBpedia is crucial for the performance of the machine learning and thereby the predictions of our service, we performed a manual evaluation.
        In the following we use the term entity to refer to the subject the website belongs to, for example companies, schools, or the government.
        The location result (LR) is the geo-coordinate acquired from DBpedia queries.
        For the manual evaluation we used the following rules:
	      <ol>
          <li>
            If the entity is surely based and active (for companies, etc.) within only one country, we evaluated every LR within the same country as correct, anything else as wrong.
          </li>
          <li>
            If the entity is active internationally and has publicly available information about the location of its headquarter we evaluated every LR within the country of the headquarter as correct, anything else as wron
          </li>
          <li>
            If the entity is active internationally and has no publicly available information about the location of its headquarter we evaluated every LR that can be related to an office as true.
          </li>
          <li>
            If website was not reachable (offline, etc.) we set not found as our result which was handled during evaluating by removing these cases from our statistics.
          </li>
	      </ol>
        The evaluation showed 95% accuracy of the ground truth.
        The web site was not reachable in 8 out of 255 cases.
	    </p>
	    <h2>Learning model</h2>
	    <p>
        The result of the data collection was a total of 233932 URLs with a location from DBpedia and for which we extracted the IP location, TLD location, and website language.
        On this data we applied a variety of statistical models including logistic regression, random forests, and support vector machines (SVMs).
        SVMs consistently provided the most accurate prediction of a location.
        We used a one vs. one multiclass classifier.
        We trained the models separately for each of our Wikipedia language editions.
        We also trained a general prediction model based on merged data from all DBpedia knowledge bases.
        We use this model as a model for all the languages.
        To evaluate the performance of our model, we used 10-cross fold validation.
      </p>
      <p>
        Table 1 shows the accuracy of the models. First we checked the accuracy over all the data we have.
		    It is represented in the entry "All data - Model".
		    Then we checked how well the models can handle difficult cases, when all the parameters disagree.
		    It is represented in the entry "Difficult cases - Model".
		    As the baseline we used the IP location.
      </p>
      <div class="row">
        <div class="col-md-9 table-responsive">
		      <table class="table table-hover table-bordered" >
		      <caption>Table 1. Accuracy of the models</caption>
		      	<tr>
		      		<th>Method</th>
		      		<th>General</th>
		      		<th>EN</th>
		      		<th>FR</th>
		      		<th>DE</th>
		      		<th>ES</th>
		      		<th>UK</th>
		      		<th>IT</th>
		      		<th>NL</th>
		      		<th>SV</th>
		      		<th>CS</th>
		      	</tr>
		      	<tr>
		      		<td>All data - Model</td>
		      		<td>81%</td>
		      		<td>81%</td>
		      		<td>91%</td>
		      		<td>90%</td>
		      		<td>75%</td>
		      		<td>96%</td>
		      		<td>91%</td>
		      		<td>96%</td>
		      		<td>92%</td>
		      		<td>98%</td>
		      	</tr>
		      	<tr>
		      		<td>All data - IP only (Baseline)</td>
		      		<td>61%</td>
		      		<td>30%</td>
		      		<td>62%</td>
		      		<td>77%</td>
		      		<td>29%</td>
		      		<td>86%</td>
		      		<td>73%</td>
		      		<td>86%</td>
		      		<td>81%</td>
		      		<td>80%</td>
		      	</tr>
		      	<tr>
		      		<td>Difficult cases - Model</td>
		      		<td>77%</td>
		      		<td>78%</td>
		      		<td>86%</td>
		      		<td>80%</td>
		      		<td>71%</td>
		      		<td>89%</td>
		      		<td>85%</td>
		      		<td>91%</td>
		      		<td>85%</td>
		      		<td>93%</td>
		      	</tr>
		      	<tr>
              <td>Difficult cases - IP only (Baseline)</td>
              <td>30%</td>
              <td>57%</td>
              <td>64%</td>
              <td>25%</td>
              <td>81%</td>
              <td>66%</td>
              <td>80%</td>
              <td>74%</td>
              <td>79%</td>
              <td>53%</td>
		      	</tr>
          </table>
        </div>
        <div class="col-md-3"></div>
      </div>
		  <p>
		  Table 2 presents the importance of each parameter of the learning models.
		  The number in each cell reflects how well a particular parameter can describe the variance of the ground truth.
		  To obtain these data we calculated how often a particular parameter agrees with the ground truth.
		  </p>
      <div class="row">
        <div class="col-md-6 table-responsive">
		      <table class="table table-hover table-bordered" >
		        <caption>Table 2. Parameter contribution over all data</caption>
		        	<tr>
		        		<th>Model</th>
		        		<th>IP<br> location</th>
		        		<th>TLD <br>location</th>
		        		<th>Website<br> Language</th>
		        	</tr>
		        	<tr>
		        		<td>General</td>
		        		<td>61%</td>
		        		<td>58%</td>
		        		<td>25%</td>
		        	</tr>
		        	<tr>
		        		<td>EN</td>
		        		<td>30%</td>
		        		<td>13%</td>
		        		<td>2%</td>
		        	</tr>
		        	<tr>
		        		<td>FR</td>
		        		<td>62%</td>
		        		<td>73%</td>
		        		<td>23%</td>
		        	</tr>
		        	<tr>
		        		<td>DE</td>
		        		<td>77%</td>
		        		<td>68%</td>
		        		<td>42%</td>
		        	</tr>
		        	<tr>
		        		<td>ES</td>
		        		<td>29%</td>
		        		<td>30%</td>
		        		<td>7%</td>
		        	</tr>
		        	<tr>
		        		<td>UK</td>
		        		<td>86%</td>
		        		<td>89%</td>
		        		<td>29%</td>
		        	</tr>
		        	<tr>
		        		<td>IT</td>
		        		<td>73%</td>
		        		<td>70%</td>
		        		<td>27%</td>
		        	</tr>
		        	<tr>
		        		<td>NL</td>
		        		<td>86%</td>
		        		<td>76%</td>
		        		<td>47%</td>
		        	</tr>
		        	<tr>
		        		<td>SV</td>
		        		<td>81%</td>
		        		<td>82%</td>
		        		<td>29%</td>
		        	</tr>
		        	<tr>
		        		<td>CS</td>
		        		<td>80%</td>
		        		<td>78%</td>
		        		<td>34%</td>
		        	</tr>
		        </table>
        </div>
        <div class="col-md-6"></div>
      </div>
      <h3>Classification Fix</h3>
      <p>
        Due to the poor data quality for some of the DBpedia language editions we decided to include one exception in our final classification.
        If the classification from our machine learning model predicts a country that appears in none of the three features we instead use the IP-location as the classification.
        One concrete example where this is helpful is, for the German model, the case where IP-location equals "US", the TLD is "COM", and the website language is "EN".
        In this case, our training data contains 776 URLs with the DBpedia location "FR" and only 460 URLs with the location "US".
        One goal of a future work thereby should be to further improve the input data by either modifying the SPARQL queries or switching to another source for website locations.
      </p>

      <h2>Usage</h2>
      <p>
        In the following we will give some examples of different ways to run the analysis and access the results.
      </p>
      <h3>Via the Web Interface</h3>
      <p>
        The easiest way is to use the web interface that we provide at our <a href="https://wikiwhere.west.uni-koblenz.de">homepage</a>.
        This is done by inserting a valid Wikipedia article URL in the input box and pressing "Get Analysis".
        If the option "Fresh crawl" is not selected and the article was analysed before, the previous results are displayed.
        Otherwise, a new analysis gets executed on the server.
        Currently we allow up to ten parallel analyses.
        Since we extract the content of all linked websites in the given article, the analysis can take several minutes, depending on the number of external links in the article.
        The plotted results are shown on a separate webpage.
      </p>
      <h3>Via URL Parameters</h3>
      <p>
        It is possible to access the plotted results via URL parameters:<br>
        <code>http://wikiwhere.west.uni-koblenz.de/article.php?url=[article-url]</code><br>
        For example, the German Wikipedia article <a href="https://de.wikipedia.org/wiki/Test">Test</a> can be accessed with:<br>
        <a href="http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test">http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test</a><br>
        The URL parameter <code>new-crawl</code> allows to force a new analysis:<br>
        <a href="http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test&new-crawl=true">http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test&new-crawl=true</a><br>
        Again, the analysis can take several minutes, depending on the number of external links in the article.
      </p>
      <h3>Via the File Browser</h3>
      <p>
        Previous analyses can be accessed via the <a href="http://wikiwhere.west.uni-koblenz.de/articles">Articles</a> tab on the website.
        The results are stored folders based on the wikipedia language edition and the article title.<br>
        For example, the analysis results (<code>analysis.json</code>) for the German Wikipedia article <a href="https://de.wikipedia.org/wiki/Test">Test</a> can be found at: <a href="http://wikiwhere.west.uni-koblenz.de/articles/de/Test/">http://wikiwhere.west.uni-koblenz.de/articles/de/Test/</a>
        In addition to the analysis results we also provide a file called <code>visualization-redirect.php</code> that performs a redirect to the visualization page of the according article.
      </p>
      <h3>Via wget</h3>
      <p>
        In order to retrieve the analysis.json file with wget, the following command can be used:<br>
        <code>wget "http://wikiwhere.west.uni-koblenz.de/json.php?url=[article-url]" -O [file-name].json</code><br>
        Again, it is possible to use the <code>new-crawl</code> parameter to force a new analysis.<br>
        A concrete example for the German Wikipedia article <a href="https://de.wikipedia.org/wiki/Test">Test</a>:<br>
        <code>wget "http://wikiwhere.west.uni-koblenz.de/json.php?url=https://de.wikipedia.org/wiki/Test" -O de-Test.json</code><br>
      </p>
      <h2>Source Code</h2>
      <p>
        The source code for this website is on GitHub at <a href="https://github.com/mkrnr/wikiwhere-website">https://github.com/mkrnr/wikiwhere-website</a>.<br>
        For the analysis we have written Python modules which are also on Github at <a href="https://github.com/mkrnr/wikiwhere">https://github.com/mkrnr/wikiwhere</a>.<br>
        The code in both repositories is available under a MIT license.
      </p>
    </div>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
