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
      <h1>About</h1>

      <h2>The Project</h2>

      <h3>Usage</h3>
      <p>
        In the following we will give some examples of different ways to run the analysis and access the results.
      </p>
      <h4>Via the Web Interface</h4>
      <p>
        The easiest way is to use the web interface that we provide at our <a href="https://wikiwhere.west.uni-koblenz.de">homepage</a>.
        This is done by inserting a valid Wikipedia article URL in the input box and pressing "Get Analysis".
        If the option "Fresh crawl" is not selected and the article was analysed before, the previous results are displayed.
        Otherwise, a new analysis gets executed on the server.
        Currently we allow up to ten parallel analyses.
        Since we extract the content of all linked websites in the given article, the analysis can take several minutes, depending on the number of external links in the article.
        The plotted results are shown on a seperate webpage.
      </p>
      <h4>Via URL Parameters</h4>
      <p>
        It is possible to access the plotted results via URL parameters:<br>
        <code>http://wikiwhere.west.uni-koblenz.de/article.php?url=[article-url]</code><br>
        For example, the German Wikipedia article <a href="https://de.wikipedia.org/wiki/Test">Test</a> can be accessed with:<br>
        <a href="http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test">http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test</a><br>
        The URL parameter <code>new-crawl</code> allows to force a new analysis:<br>
        <a href="http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test&new-crawl=true">http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test&new-crawl=true</a><br>
        Again, the analysis can take several minutes, depending on the number of external links in the article.
      </p>
      <h4>Via the File Browser</h4>
      <p>
        Previous analyses can be accessed via the <a href="http://wikiwhere.west.uni-koblenz.de/articles">Articles</a> tab on the website.
        The results are stored in a folder according to the wikipedia language edition.<br>
        For example, the analysis results for the German Wikipedia article <a href="https://de.wikipedia.org/wiki/Test">Test</a> can be found at: <a href="http://wikiwhere.west.uni-koblenz.de/articles/de/Test/">http://wikiwhere.west.uni-koblenz.de/articles/de/Test/</a>
      </p>
      <h4>Via wget</h4>
      <p>
        In order to retrieve the analysis.json file with wget, the following command can be used:<br>
        <code>wget "http://wikiwhere.west.uni-koblenz.de/json.php?url=[article-url]" -O [file-name].json</code><br>
        Again, it is possible to use the <code>new-crawl</code> parameter to force a new analysis.<br>
        A concrete example for the German Wikipedia article <a href="https://de.wikipedia.org/wiki/Test">Test</a>:<br>
        <code>wget "http://wikiwhere.west.uni-koblenz.de/json.php?url=https://de.wikipedia.org/wiki/Test" -O de-Test.json</code><br>
      </p>

      <h3>Source Code</h3>
      <p>
        The source code for this website is on GitHub at: <a href="https://github.com/mkrnr/wikiwhere-website">https://github.com/mkrnr/wikiwhere-website</a><br>
        For the analysis we have written Python modules which are also on Github at: <a href="https://github.com/mkrnr/wikiwhere">https://github.com/mkrnr/wikiwhere</a>
      </p>
      <h2>The Team</h2>

    </div>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
