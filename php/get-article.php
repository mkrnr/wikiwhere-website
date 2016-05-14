<?php
  $handle = @fopen($article_url,'r');

  if($handle !== false){
     if($new_crawl == true){
       $article_path = exec($python.' get_article_data.py ' . $article_url . " " . $new_crawl);
     }else{
       $article_path = exec($python.' get_article_data.py ' . $article_url);
     }
  }
  else{
     echo "URL doesn't exist";
     return false;
  }
  if ($article_path == "busy"){
    echo "Currently the server is running too many calculations. Please try again later.";
    echo "</body>";
    echo "</html>";
    exit(1);
  }
  if ($article_path == "empty"){
    echo "No URLs found. Maybe the URL redirects to another article?";
    echo "</body>";
    echo "</html>";
    exit(1);
  }
  if ($article_path == "not wiki"){
    echo "This is not a Wikipedia URL: " . $article_url;
    echo "</body>";
    echo "</html>";
    exit(1);
  }
?>
