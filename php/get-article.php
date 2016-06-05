<?php
  $handle = @fopen($article_url,'r');

  if($handle == true){
    if($new_crawl == true){
      $article_path = exec($python.' get_article_data.py ' . $article_url_encoded . ' ' . $new_crawl);
    }else{
      $article_path = exec($python.' get_article_data.py ' . $article_url_encoded);
    }
  }
  else{
    $error = "not-found";
  }
  if ($article_path == "busy"){
    $error = "busy";
  }
  if ($article_path == "empty"){
    $error = "empty";
  }
  if ($article_path == "not wiki"){
    $error = "not-wiki";
  }
?>

<script>
  var error = "<?php echo $error; ?>";
  if(error.length > 0){
    var error_url = "http://"+window.location.hostname +"/error.php?type="+error;
    window.location = error_url;
  }
</script>
