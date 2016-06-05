<!DOCTYPE html>
<html lang="en">
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <title>wikiwhere - Error</title>

  </head>

  <body>
<?php echo file_get_contents("templates/header.php") ?>

    <div class="container">
      <h1>Error</h1>
      <p>
        <?php
          $error_type=filter_input(INPUT_GET, 'type');
          switch ($error_type) {
              case "empty":
                  print "The article did not contain any external URLs.";
                  break;
              case "busy":
                  print "Currently there are too many calculations running on the server. Please try again later.";
                  break;
              case "not-wiki":
                  print "The URL that you entered is not a Wikipedia URL.";
                  break;
              case "not-found":
                  print "We couldn't open the URL that you entered.";
                  break;
              default:
                  print "Something went wrong...";
          }
        ?>
      </p>
    </div>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
