<?php

session_start();
$allowbanned = 1;

require "configuration.php";

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];

$bantype = $_GET["type"];

if (!empty($bantype)) {
    if ($bantype === "badsearch") {
        $banmessage = $bad_search;
    }
} else {
    header("Location: " .$index_page);
}

$webpage_name = $application_name . " - " . $banned_title;

?>


<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title><?php echo $webpage_name; ?></title>
      <link rel="stylesheet" href="bootstrap.css">
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <link rel="icon" type="image/ico" href="<?php echo $favicon_path; ?>">
   </head>
   <body>
      <noscript>
         <div style="position: fixed; top: 0px; left: 0px; z-index: 30000000; 
            height: 100%; width: 100%;">
            <p style="margin-left: 10px"><?php echo $js_warning; ?></p>
         </div>
      </noscript>
      <br><img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $home_page; ?>';" height="80px"><br>
      <div style="background-color:#ffffff;" class="jumbotron">
         <div style="text-align:center;">
            <p class="lead"><i style="color:#bd0000; font-size:30px;" class="fa fa-exclamation-triangle"></i> <?php echo $banned_warning; ?></p>
            <span><?php echo $banmessage; ?></span>
            <br><br>
            <a href='<?php echo $translate_page; ?>?language=en&url=<?php echo $ban_page; ?>?type=<?php echo $bantype; ?>'><img src='media/flags/en.png' width='25px;' height='15px;'> English</a> |
            <a href='<?php echo $translate_page; ?>?language=it&url=<?php echo $ban_page; ?>?type=<?php echo $bantype; ?>'><img src='media/flags/it.png' width='25px;' height='15px;'> Italiano</a>
            <a href="<?php echo $translate_page; ?>?language=zh&url=<?php echo $ban_page; ?>?type=<?php echo $bantype; ?>"><img src="media/flags/zh.png" width="25px;" height="15px;"> 中国人</a>
         </div>
      </div>
   </body>