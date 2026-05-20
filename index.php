<?php

session_start();

require "configuration.php";

if (isset($_SESSION["isloggedin"])) {
    header("Location: " . $home_page);
    exit();
}
 
if (
    isset($_POST["access"]) &&
    !empty($_POST["email"]) &&
    !empty($_POST["userpassword"])
) {
    $userpassword = $_POST["userpassword"];
    $email = $_POST["email"];

    $findusersql =
        "SELECT * FROM Member WHERE memberemail = ? AND memberblocked < NOW() AND memberverified = 1";
    $finduserstatement = $database_connection->prepare($findusersql);
    $finduserstatement->bind_param("s", $email);
    $finduserstatement->execute();
    $finduserresult = $finduserstatement->get_result();
    if (mysqli_num_rows($finduserresult) > 0) {
        while ($finduserrow = $finduserresult->fetch_assoc()) {
            $password_found = $finduserrow["memberpassword"];

            if (password_verify($userpassword, $password_found)) {
                $_SESSION["memberid"] = $finduserrow["memberid"];
                $_SESSION["membername"] = $finduserrow["membername"];
                $_SESSION["memberinfo"] = $finduserrow["memberinfo"];
                $_SESSION["membercreation"] = $finduserrow["membercreation"];
                $_SESSION["memberemail"] = $finduserrow["memberemail"];
                $_SESSION["membercode"] = $finduserrow["membercode"];
                $_SESSION["memberprivate"] = $finduserrow["memberprivate"];
                $_SESSION["allownotifications"] = $finduserrow["allownotifications"];
                $_SESSION["allowprivatenotifications"] = $finduserrow["allowprivatenotifications"];
                $_SESSION["allowgroupnotifications"] = $finduserrow["allowgroupnotifications"];
                $_SESSION["memberpassword"] = $_POST["userpassword"];
                $_SESSION["sessionexpiry"] = date(
                    "Y-m-d H:i:s",
                    strtotime("+10 hours")
                );
                $_SESSION["isloggedin"] = true;

                
                // Update activity
                $updateactivitysql =
                "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
                $updateactivitystatement = $database_connection->prepare($updateactivitysql);
                $updateactivitystatement->bind_param("s", $_SESSION["membercode"]);
                $updateactivitystatement->execute();

                header("Location: " . $home_page);
                exit();
                
            } else {
                $errormessage = $wrong_credentials;
            }
        }
    } else {
        $errormessage = $wrong_credentials;
    }
}

$webpage_name = $welcome;

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
      <meta name="description" content="<?php echo $logos_info; ?>">
      <meta name="og:description" content="<?php echo $logos_info; ?>">
      <meta property="og:image" content="<?php echo $application_logo_path; ?>">

   </head>
   <body>
      <br><img src="<?php echo $application_logo_path; ?>" width="200px" onclick="window.location.href = '<?php echo $index_page; ?>';" height="80px" style="display: block; margin-left: auto; margin-right: auto;">
      <div style="background-color:#ffffff;" class="jumbotron">
         <h1 style="font-size:35px; text-align:center;" class="display-4"><?php echo $welcome; ?></h1>
         <hr class="my-4">
         
         <u><a style="color:blue;" href="<?php echo $registration_landing_page; ?>"><?php echo $no_account; ?></a></u> <br> <u><a style="color:blue;" href="<?php echo $recovery_page; ?>"><?php echo $forgot_password; ?></a></u><br><br>

         <form method="post" class="card p-3 bg-light">
            <?php if(!empty($errormessage)) { ?>
            <mark><?php echo $errormessage; ?></mark><br><br>
            <?php } ?>
            <p style="font-size:20px;"><?php echo $enter_your_email; ?></p>
            <input type="email" name="email" required="true"><br><br>
            <p style="font-size:20px;"><?php echo $enter_your_password; ?></p>
            <input type="password" name="userpassword" required="true">
            <br><br><br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="access" value="<?php echo $access; ?>">
            </form>

            <br><br>
            <a href="<?php echo $translate_page; ?>?language=en&url=<?php echo $index_page; ?>"><img src="media/flags/en.png" width="25px;" height="15px;"> English</a> |
            <a href="<?php echo $translate_page; ?>?language=it&url=<?php echo $index_page; ?>"><img src="media/flags/it.png" width="25px;" height="15px;"> Italiano</a> |
            <a href="<?php echo $translate_page; ?>?language=zh&url=<?php echo $index_page; ?>"><img src="media/flags/zh.png" width="25px;" height="15px;"> 中国人</a>
            <br><br>
            <p style="text-align:center; display:inline;"><?php echo $copyright; ?></p>
            <a href="<?php echo $terms_page; ?>"><?php echo $terms_title;?></a> | <a href="<?php echo $privacy_page; ?>"><?php echo $privacy_title;?></a>
      </div>
      </div>
   </body>
</html>