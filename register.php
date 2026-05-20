<?php

session_start();

require "configuration.php";

if (isset($_SESSION["isloggedin"])) {
    header("Location: " . $home_page);
    exit();
}

$webpage_name = $register_to . " " . $application_name;

?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title><?php echo $webpage_name; ?></title>
      <link rel="stylesheet" href="bootstrap.css">
      <link rel="icon" type="image/ico" href="<?php echo $favicon_path; ?>">
      <script src="https://www.google.com/recaptcha/api.js"></script> 
   </head>
   <body>
      <br><img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $index_page; ?>';" height="80px">
      <div style="background-color:#ffffff; text-align:center;" class="jumbotron">
         <h1 style="font-size:35px; text-align:center;" class="display-4"><?php echo $new_account_title; ?></h1>
         <hr class="my-4">
         <p style="text-align:left;"><u><a style="color:blue;" href="<?php echo $index_page; ?>"><?php echo $already_have_account; ?></a></u> <br> <u><a style="color:blue;" href="<?php echo $recovery_page; ?>"><?php echo $forgot_password; ?></a></u></p><br>
         
         <form action="<?php echo $create_account_page; ?>" class="card p-3 bg-light" method="post">
            <p style="font-size:20px;"><?php echo $enter_name_surname; ?></p>
            <input type="text" name="membername" required="true"><br><br>
            <p style="font-size:20px;"><?php echo $enter_personal_code ." <br>" .$or_generate; ?></p>
            <span><input type="text" onkeyup="document.getElementById('generate').checked = false;" maxlength="20" name="personalcode"></span>
            <small style="font-size:17px;"><?php echo $code_warning; ?></small><br><br>
            <p style="font-size:20px;"><?php echo $enter_email; ?></p>
            <input type="email" name="useremail" required="true"><br><br>
            <p style="font-size:20px;"><?php echo $confirm_email; ?></p>
            <input type="email" name="confirmemail" required="true"><br><br>
            <p style="font-size:20px;"><?php echo $enter_new_password; ?></p>
            <input type="password" name="userpassword" required="true">
            <br><br>
            <div>
               <input type="checkbox" id="consent" name="consent" required="true">
               <label for="consent"><?php echo $terms_notice; ?></label>
            </div>
            <br>
            
            <div class="g-recaptcha" data-sitekey="6LeZcCMgAAAAANsWo1Ne4SMpSooR0LlLqH4NxMDZ"></div>
                        
            <br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="createaccount" value="Create Account">
            </form>
            
         <br><br>
         <div style="text-align:left;">
         <a href="<?php echo $translate_page; ?>?language=en&url=<?php echo $registration_landing_page; ?>"><img src="media/flags/en.png" width="25px;" height="15px;"> English</a> |
         <a href="<?php echo $translate_page; ?>?language=it&url=<?php echo $registration_landing_page; ?>"><img src="media/flags/it.png" width="25px;" height="15px;"> Italiano</a> |
         <a href="<?php echo $translate_page; ?>?language=zh&url=<?php echo $registration_landing_page; ?>"><img src="media/flags/zh.png" width="25px;" height="15px;"> 中国人</a>
         <br><br>
         <p style="text-align:left; display:inline;"><?php echo $copyright; ?></p>
         <a href="<?php echo $terms_page; ?>"><?php echo $terms_title;?></a> | <a href="<?php echo $privacy_page; ?>"><?php echo $privacy_title;?></a>
         </div>
      </div>
   </body>
</html>