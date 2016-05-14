<!DOCTYPE html>
<html lang="en">
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <title>wikiwhere - Contact</title>

  </head>

  <body>
<?php echo file_get_contents("templates/header.php") ?>

    <script>
        document.getElementById("contact").setAttribute("class", "active");
    </script>

    <div class="container">
      <h2>Contact</h2>
    </div>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
