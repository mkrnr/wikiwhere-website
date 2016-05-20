<!DOCTYPE html>
<html lang="en">
  <head>
<?php echo file_get_contents("templates/head-scripts.php") ?>

    <?php
      $default="https://de.wikipedia.org/wiki/Krimkrise";

      $python=filter_input(INPUT_GET, 'python');
      if (isset($python)){
        $call.="?python=".$python;
      }
    ?>

    <title>wikiwhere - Home</title>

  </head>

  <body>
<?php echo file_get_contents("templates/header.php") ?>

    <script>
        document.getElementById("home").setAttribute("class", "active");
    </script>


    <div class="container" id="analysis-input">
      <!-- Main component for a primary marketing message or call to action -->
        <div class="row text-center">
          <div class="col-md-6 col-md-offset-3">
            <h1>Article Analysis</h1>
          </div>
        </div>
        <form action="load.php" method="get" class="text-center">

          <div class="row m-t-10">
            <div class="col-md-7 col-centered">
              <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Article URL</span>
                <input type="text" class="form-control" id="article-input" name="url" placeholder=<?php echo $default; ?>>
              </div><!-- /input-group -->
            </div>
          </div>

          <div class="row m-t-17 text-center">
            <div class="col-md-4 col-md-offset-4">
              <div class="input-group col-centered">
                <button type="submit" class="btn btn-default m-r-10">Get Analysis</button>
                <label>
                  <input type="checkbox" name="new-crawl" value="true">
                  Fresh crawl
                </label>
              </div><!-- /input-group -->
            </div>
          </div>
        </form>
        <script type="text/javascript">
          document.getElementById('article-input').value = "<?php echo $default; ?>";
        </script>
    </div> <!-- /container -->

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
