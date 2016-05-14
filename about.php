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
      <h2>About</h2>
    </div>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
