<?php

session_start();

require "configuration.php";

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];

$webpage_name = $application_name . " - " . $privacy_title;

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
            height: 100%; width: 100%; background-color: #FFFFFF">
            <p style="margin-left: 10px"><?php echo $js_warning; ?></p>
         </div>
      </noscript>
      <div style="background-color:#ffffff;" class="jumbotron">
      <a href="<?php echo $index_page; ?>"><img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" height="80px"></a><br>
      <h1 style="text-align:center; font-size:25px;"><?php echo $privacy_title; ?></h1>
      <br>
      <?php echo $terms_conjunction ."<br><br>"; ?>
      <div style="display:block; text-align:center;">
         <?php echo $privacy_message; ?>
      </div>
      <a href='<?php echo $translate_page; ?>?language=en&url=<?php echo $privacy_page; ?>'><img src='media/flags/en.png' width='25px;' height='15px;'> English</a> |
      <a href='<?php echo $translate_page; ?>?language=it&url=<?php echo $privacy_page; ?>'><img src='media/flags/it.png' width='25px;' height='15px;'> Italiano</a> |
      <a href="<?php echo $translate_page; ?>?language=zh&url=<?php echo $privacy_page; ?>"><img src="media/flags/zh.png" width="25px;" height="15px;"> 中国人</a>
      <br>
   </body>
</html>