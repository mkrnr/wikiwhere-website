<!DOCTYPE html>
<html>
  <head>
    <title>Loading</title>

  </head>
  <body>
    <p>Loading...</p>
    <p>This could take a while, depending on the size of the Wikipedia article.</p>
    <p>You can close this page without stopping the calculation and access it again later.</p>

    <script>
      var url = window.location.href ;
      var load = url.replace("/load.php","/article.php");
      window.location = load;
    </script>
  </body>
</html>

