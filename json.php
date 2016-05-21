<?php
  $article_url_input = filter_input(INPUT_GET, 'url');

  $article_url = urldecode ($article_url_input);
  $article_url_encoded = urlencode ($article_url);

  $python=filter_input(INPUT_GET, 'python');
  if (!isset($python)){
    $python="/usr/bin/python2";
  }
?>
<?php
  include 'php/get-article.php';
  $current_url = "http://$_SERVER[HTTP_HOST]/";
  header("Location: " . $current_url . $article_path . "/analysis.json" );
?>
