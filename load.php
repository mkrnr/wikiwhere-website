<!DOCTYPE html>
<html lang="en">
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <title>wikiwhere - Loading</title>

  </head>

  <body>
<?php echo file_get_contents("templates/header.php") ?>

    <div class="container">
      <h2>Loading...</h2>
      <p>This could take a while, depending on the size of the Wikipedia article.</p>
      <p>You can close this page without stopping the calculation and access it again later.</p>
    </div>

    <script>
      var url = window.location.href ;
      var load = url.replace("/load.php","/article.php");
      window.location = load;
    </script>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
