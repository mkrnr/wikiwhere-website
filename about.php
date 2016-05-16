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
        It is possible to access the plotted results via URL parameters.
      </p>
      <p>
        For the German Wikipedia article called <a href="https://de.wikipedia.org/wiki/Test">Test</a>, the URL look like this: <a href="http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test">http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test</a>
      </p>
      <p>
        The URL parameter "new-crawl" allows to force a new analysis: <a href="http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test&new-crawl=true">http://wikiwhere.west.uni-koblenz.de/article.php?url=https://de.wikipedia.org/wiki/Test&new-crawl=true</a>
      </p>
      <p>
        Again, the analysis can take several minutes, depending on the number of external links in the article.
      </p>



      <h3>Source Code</h3>
      <p>
        The source code for this website is on GitHub at <a href="https://github.com/mkrnr/wikiwhere-website">https://github.com/mkrnr/wikiwhere-website</a>.
        For the analysis we have written Python modules which are also on Github at <a href="https://github.com/mkrnr/wikiwhere">https://github.com/mkrnr/wikiwhere</a>.
      </p>
      <h2>The Team</h2>

    </div>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
