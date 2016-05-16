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
      <h1>Contact</h1>

      <h2>Team</h2>
      <table>
        <tr>
          <td style="padding:0px 5px 0px 0px  ">Tatiana Sennikova:</td>
          <td><a href="mailto:tsennikova@uni-koblenz.de">tsennikova@uni-koblenz.de</a></td>
        </tr>
          <td style="padding:0px 5px 0px 0px  ">Florian Windhäuser:</td>
          <td><a href="mailto:fwindhaeuser@uni-koblenz.de">fwindhaeuser@uni-koblenz.de</a></td>
        </tr>
       <tr><td style="padding:0px 5px 0px 0px  "><a href="http://mkoerner.de">Martin Körner</a>:</td>
      <td><a href="mailto:mkoerner@uni-koblenz.de">mkoerner@uni-koblenz.de</a></td>
      </tr></table>
      <h2>Supervisors</h2>
      <table>
        <tr>
          <td style="padding:0px 5px 0px 0px  "><a href="https://west.uni-koblenz.de/en/ueber-uns/team/jprof-dr-claudia-wagner">Claudia Wagner</a>:</td>
          <td>clwagner at uni-koblenz dot de</td>
        </tr>
        <tr>
          <td style="padding:0px 5px 0px 0px  "><a href="http://www.gesis.org/en/institute/staff/?alpha=F&name=Fabian,Floeck">Fabian Flöck</a>: </td>
          <td>fabian dot floeck at gesis dot org</td>
        </tr>
      </table>
    </div>

<?php echo file_get_contents("templates/footer.php") ?>

<?php echo file_get_contents("templates/body-scripts.php") ?>
  </body>
</html>
