<?php

session_start();

require "configuration.php";

$webpage_name = $application_name . " - " . $bad_request;
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
      <link rel="icon" type="image/ico" href="<?php echo $favicon_path; ?>">
   </head>
   <body>
      <noscript>
         <div style="position: fixed; top: 0px; left: 0px; z-index: 30000000; 
            height: 100%; width: 100%; background-color: #FFFFFF">
            <p style="margin-left: 10px"><?php echo $js_warning; ?></p>
         </div>
      </noscript>
      <br>
      <img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $home_page; ?>';" height="80px">
      <div style="background-color:#ffffff;" class="jumbotron">
         <h1><?php echo $bad_request_error; ?> - 400</h1>
         <p class="lead"><?php echo $bad_request_error_message; ?></p>
         <p style="text-align:center; font-size:25px;"><a href="<?php echo $home_page; ?>"><?php echo $go_home; ?></a></p>
      </div>
   </body>
</html>