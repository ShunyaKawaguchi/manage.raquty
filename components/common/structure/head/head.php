<?php 
//タイトル表示関数呼び出し
require_once(dirname(__FILE__).'/global_css_roader.php');

if($template !== 'login'){
  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
  $host = $_SERVER['HTTP_HOST'];
  $uri = $_SERVER['REQUEST_URI'];
  $full_url = $protocol . "://" . $host . $uri;
  $_SESSION['request_url'] = $full_url;
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title><?php echo $post_data['post_title'];?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"content="raquty-Make tennis management easier-"> 
    <meta name="robots" content="noindex">
    <link rel="icon" href="/files/material/raquty_fabicon.ico" id="favicon">
    <link rel="stylesheet" href="/components/common/global-css.min.css">
    <?php overwrite_global_css($template); ?>
    <link rel="stylesheet" href="/components/templates/<?php echo  $template.'/' .$template.'.min.css' ;?>">
    <?php require_once(dirname(__FILE__).'/../structure-css_loder.php') ; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    <script src="/components/common/global-js.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  </head>
<body>