<?php

session_start();

require "configuration.php";

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];

$webpage_name = $application_name . " - " . $missing_chat_title;

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
      <br>
      <img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $home_page; ?>';" height="80px">
      <div style="background-color:#ffffff;" class="jumbotron">
         <div style="text-align:center;">
            <p class="display-4" style="font-size:35px;"><?php echo $missing_chat_title; ?></p>
            <p class="lead"><?php echo $missing_chat; ?></p>
                     <script>
            function showChatHint(query) {
              if (query.length == 0) {
                document.getElementById("chatsuggestions").innerHTML = "";
                return;
              } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                  if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("chatsuggestions").innerHTML = this.responseText;
                  }
                }
                xmlhttp.open("GET", "<?php echo $chathint_page; ?>?search=" + query, true);
                xmlhttp.send();
              }
            }
         </script>
         <form action="<?php echo $chat_page; ?>" style="text-align:center;">
            <input type="text" required="true" name="interlocutor" id="interlocutor" style="width:100%;" placeholder="<?php echo $search_chat; ?>" onkeyup="showChatHint(this.value)">
            <br><p style="text-align:center;"><span id="chatsuggestions"></span></p>
            
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; float:center; font-size:13px;" name="search" id="search" value="<?php echo $chat; ?>">
         </form>
         <br>
            </div>
      </div>
   </body>