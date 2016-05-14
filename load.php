<!DOCTYPE html>
<html lang="en">
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <script type="text/javascript" src="js/url-mod.js"></script>

    <title>wikiwhere - Loading</title>

  </head>

  <body>
<?php echo file_get_contents("templates/header.php") ?>

    <div class="container">
      <h2>Loading...</h2>
      <p>This could take a while, depending on the size of the Wikipedia article.</p>
      <p>You can close this page without stopping the calculation and access it later via <a id="article-url" href="">this link</a>.</p>
    </div>

    <script>
      var load_url = window.location.href ;
      var article_url = load_url.replace("/load.php","/article.php");

      var url_removed = removeVariableFromURL(article_url, "new-crawl");

      document.getElementById("article-url").setAttribute('href', url_removed);

      window.location = article_url;
    </script>


<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
