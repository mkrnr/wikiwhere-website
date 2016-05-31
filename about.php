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
        In the following we will explain our approach and the different ways of how to use our web service. Additionally we provide the source code for this website and the underlying python code that runs the analysis and data extraction under a MIT license.
      </p>
	    <h2>Approach</h2>
	    <p>
        We use the term reference to refer to an URL that leads from a given Wikipedia article to another web page that is not associated with the Wikimedia foundation.
        To obtain a training set for the machine learning model, we retrieved geo-location information on websites from <a href="http://dbpedia.org/">DBpedia</a> SPARQL endpoints.
        In order to evaluate the accuracy of the ground truth we manually checked 255 locations for references that we extracted from the English DBpedia.
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
        This is done by first generating Markdown text using the <a href="https://pypi.python.org/pypi/html2text"><code>html2text</code> package</a> and then using the <a href="https://pypi.python.org/pypi/beautifulsoup4"><code>beautifulsoup</code> package</a> to extract the text from Markdown via another conversion to HTML.
        After extracting the text we use the <a href="https://github.com/Mimino666/langdetect"><code>langdetect</code> package</a> for detecting the language.
	    </p>
	    <h3>DBpedia Location Extraction</h3>
        In order to gather a large amount of geo-locations for websites we used the <a href="http://dbpedia.org/sparql">DBpedia SPARQL endpoints</a>.
        The SPARQL requests were made with the <a href="https://pypi.python.org/pypi/SPARQLWrapper/1.7.6"><code>SPARQLWrapper</code> package</code></a>.
        The first part of getting a location is to associate a given URL to a DBpedia entity.
        For this entity we then query for it's, location, location city, or location of it's parent company.
        In cases where we retrieve more than one location for a URL we perform a majority voting.
        Using this method, we were able to retrieve, for example, locations for 149731 URLs using the English DBpedia SPARQL endpoint.
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
        The case when the web site was not reachable happened in 8 out of 255 cases.
	    </p>
	    <h2>Learning model</h2>
	    <p>
        We used a variety of statistical models including logistic regression, random forests, and support vector machines (SVMs).
        SVMs consistently provided the most accurate prediction of a location.
        We used a one vs. one multiclass classifier.
        We trained the models separately for each of our Wikipedia language editions.
        We also trained a general prediction model based on merged data from all DBpedia knowledge bases.
        We use this model as a model for all the languages.
        To evaluate the performance of our model, we used 10-cross fold validation.
        The accuracy of the general model is 77%.
        As the baseline we used the IP location which has an accuracy of 56%.
	      After obtaining the results, we are checking if the prediction agrees with at least one feature, if not than we use IP location as a predictor.
      </p>
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
        The plotted results are shown on a seperate webpage.
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
