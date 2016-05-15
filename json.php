<?php
  $article_url = filter_input(INPUT_GET, 'url');
  $new_crawl=filter_input(INPUT_GET, 'new-crawl');

  $python=filter_input(INPUT_GET, 'python');
  if (!isset($python)){
    $python="/usr/bin/python2";
  }
?>
<?php
  include 'php/get-article.php';
  $current_url = "http://$_SERVER[HTTP_HOST]/";
  header("Location: " . $current_url . $article_path );
?>
