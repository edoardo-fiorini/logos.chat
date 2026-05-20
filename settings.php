<?php

session_start();

require "configuration.php";

if (!isset($_SESSION["isloggedin"])) {
    header("Location: " . $access_page);
    exit();
}

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];

if (isset($_POST["update"])) {
    if (!empty($_POST["membername"])) {
        $membername = $_POST["membername"];

        $changenamesql = "UPDATE Member SET membername = ? WHERE memberid = ?";
        $changenamestatement = $database_connection->prepare($changenamesql);
        $changenamestatement->bind_param("ss", $membername, $member);

        $changenamestatement->execute();

      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
      
        $_SESSION["membername"] = $membername;
    }

    if (
        file_exists($_FILES["PFP"]["tmp_name"]) &&
        is_uploaded_file($_FILES["PFP"]["tmp_name"]) &&
        $_FILES["PFP"]["size"] < 3000000
    ) {
        
        $filename = basename($_FILES["PFP"]["name"]);
        
        $upload_path = "media/PFP/" . $member . "-" . $membercode . ".png";
    
        $fileType = pathinfo($filename, PATHINFO_EXTENSION); 
         
        $allowTypes = array("jpg", "png", "jpeg", "gif"); 
        
        if(in_array($fileType, $allowTypes)) { 

            $imageTemp = $_FILES["PFP"]["tmp_name"]; 
            $imageSize = $_FILES["PFP"]["size"];

            $compressedImage = compressImage($imageTemp, $upload_path, 75); 
             
            if($compressedImage) { 
                
                $compressedImageSize = filesize($compressedImage);
            }
        }
    }

    if (!empty($_POST["memberinfo"])) {
        $memberinfo = $_POST["memberinfo"];

        $changeinfosql = "UPDATE Member SET memberinfo = ? WHERE memberid = ?";
        $changeinfostatement = $database_connection->prepare($changeinfosql);
        $changeinfostatement->bind_param("ss", $memberinfo, $member);
        $changeinfostatement->execute();

      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
      
        $_SESSION["memberinfo"] = $memberinfo;
    }

    if ($_POST["private"] === "on") {
        $changeprivatesql =
            "UPDATE Member SET memberprivate = 1 WHERE memberid = ?";
        $changeprivatestatement = $database_connection->prepare(
            $changeprivatesql
        );
        $changeprivatestatement->bind_param("s", $member);
        $changeprivatestatement->execute();

        $_SESSION["memberprivate"] = 1;
        
              $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
      
    } else {
        $changeprivatesql =
            "UPDATE Member SET memberprivate = 0 WHERE memberid = ?";
        $changeprivatestatement = $database_connection->prepare(
            $changeprivatesql
        );
        $changeprivatestatement->bind_param("s", $member);
        $changeprivatestatement->execute();

        $_SESSION["memberprivate"] = 0;
    }
}

if (
    isset($_POST["changepassword"]) &&
    isset($_POST["newpassword"]) &&
    isset($_POST["newpasswordconfirm"])
) {
    $newpassword = $_POST["newpassword"];
    $newpasswordconfirm = $_POST["newpasswordconfirm"];

    if ($newpassword === $newpasswordconfirm) {
        $changepasswordsql =
            "UPDATE Member SET memberpassword = ? WHERE memberid = ?";
        $changepasswordstatement = $database_connection->prepare(
            $changepasswordsql
        );
        $changepasswordstatement->bind_param(
            "ss",
            password_hash($newpassword, PASSWORD_DEFAULT),
            $member
        );

        if ($changepasswordstatement->execute()) {
            $password_warning =
                "<mark>" . $successful_password_modified . " </mark><br><br>";
                
      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
        }
    } else {
        $password_warning = "<mark>" . $passwords_no_match . " </mark><br><br>";
    }
}

if (isset($_POST["delete"])) {
    if (file_exists("media/PFP/" . $member . "-" . $membercode . ".png")) {
        unlink("media/PFP/" . $member . "-" . $membercode . ".png");
    }
}

$webpage_name = $application_name . " - " . $settings;

?>

<!DOCTYPE html>
<html lang="en">
      <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title><?php echo $webpage_name; ?></title>
      <link rel="stylesheet" href="bootstrap.css">
      <meta name="format-detection" content="telephone=no">
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
      <img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $home_page; ?>';" height="80px"><br>
      <script>
         $(document).ready(function() {
         var pageRefresh = 3000;
             setInterval(function() {
                 refresh();
             }, pageRefresh);
         });
         
         function refresh() {
             $("#menubar").load(" #menubar > *");
         }
      </script>
          <div id="menubar" style="text-align:center;">
            <a href="<?php echo $home_page; ?>" style="color:#0210a8;"><?php echo $home ." " .$notificate ." "; ?> <img src="<?php echo $home_icon_path; ?>" width="20px;" height="20px;"> </a><span>|</span><b><a href="<?php echo $settings_page; ?>" style="color:#0210a8;"> <?php echo $settings; ?> <img src="<?php echo $settings_icon_path; ?>" width="20px;" height="20px;"></b> </a><span>|</span><a href="<?php echo $logout_page; ?>" style="color:#0210a8;"> <?php echo $logout; ?> <img src="<?php echo $exit_icon_path; ?>" width="20px;" height="20px;"></a><br>
            </div><br>

      <div id="settingsframe" style="display:table; width:100%;">
         
         <h1 style="text-align:center; font-size:25px;"><?php echo $settings; ?></h1>
         <br>
         
         <div>
      
         <form method="post" class="form-check" enctype="multipart/form-data" id="userinfo">

           <br><input type="text" name="membername" required="true" value="<?php echo htmlspecialchars($_SESSION["membername"]); ?>"><br><br>

            <textarea style="width:70%; max-width:300px;" rows="4" cols="50" form="userinfo" placeholder="<?php echo $biography_thrust; ?>" name="memberinfo" id="memberinfo"><?php echo htmlspecialchars($_SESSION["memberinfo"]); ?></textarea><br><br>
            <input type="checkbox" <?php if($_SESSION["memberprivate"] == "1") { ?>checked="true"<?php } ?> value="on" id="private" name="private"> <span><?php echo $private_account; ?></span><br><br>

            <label for="PFP"><span style="color:#3761d4;"><img style="border-radius:50%;" src="<?php if(file_exists("media/PFP/" .$member ."-" .$membercode .".png")) {
               echo "media/PFP/" .$member ."-" .$membercode .".png"; 
               } else {
               echo "media/PFP/default.png"; 
               }
               ?>" width="100px;" height="100px;"> <br> <?php echo $select_PFP; ?></span></label><input type="file" accept="image/png, image/gif, image/jpeg, image/jpg" id="PFP" name="PFP" style="display:none;" accept="image/*"><br>
            <br><input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="delete" value="<?php echo $delete_PFP; ?>"><br>

            <br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="update" value="<?php echo $confirm_edits; ?>">
         </form>
         <hr>
         <h1 style="font-size:20px;"><?php echo $change_password; ?></h1><br>
         <form method="post">
            <?php if(!empty($password_warning)) { 
               echo $password_warning;
               } ?>
            <input type="password" name="newpassword" placeholder="<?php echo $new_password; ?>" required="true"><br><br>
            <input type="password" name="newpasswordconfirm" placeholder="<?php echo $new_password_confirm; ?>" required="true"><br><br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="changepassword" value="<?php echo $change_password; ?>">
         </form>
         </div>
         <br>
         <a href="<?php echo $translate_page; ?>?language=en&url=<?php echo $settings_page; ?>"><img src="media/flags/en.png" width="25px;" height="15px;"> English</a> |
         <a href="<?php echo $translate_page; ?>?language=it&url=<?php echo $settings_page; ?>"><img src="media/flags/it.png" width="25px;" height="15px;"> Italiano</a> |
         <a href="<?php echo $translate_page; ?>?language=zh&url=<?php echo $settings_page; ?>"><img src="media/flags/zh.png" width="25px;" height="15px;"> 中国人</a>
         <br><br>
         <br><br>
      </div>
      </div>
      <br>
   </body>
</html>