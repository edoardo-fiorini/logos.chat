<?php

session_start();

require "configuration.php";

if (isset($_SESSION["isloggedin"])) {
    header("Location: " . $home_page);
    exit();
}

if (isset($_POST["recover"]) && !empty($_POST["useremail"])) {
    $useremail = $_POST["useremail"];

    $findemailsql = "SELECT * FROM Member WHERE memberemail = ?";
    $findemailstatement = $database_connection->prepare($findemailsql);
    $findemailstatement->bind_param("s", $useremail);
    $findemailstatement->execute();
    $findemailresult = $findemailstatement->get_result();

    if (mysqli_num_rows($findemailresult) > 0) {
        while ($findemailrow = $findemailresult->fetch_assoc()) {
            $username = $findemailrow["membername"];

            $recovery_hash = md5(rand(0, 1000));
            $reset_completed = 0;

            $expiry_date = date("Y-m-d H:i:s", strtotime("+2 hours"));

            $recoversql =
                "INSERT INTO Recover (resetemail, resethash, resetexpiry, resetcompleted) VALUES (?, ?, ?, ?)";
            $recoverstatement = $database_connection->prepare($recoversql);
            $recoverstatement->bind_param(
                "ssss",
                $useremail,
                $recovery_hash,
                $expiry_date,
                $reset_completed
            );
            if ($recoverstatement->execute()) {
                $account_recovery_message =
                    $account_recovery_successful .
                    " (" .
                    $useremail .
                    ").<br><br>";

                require "phpmailer/PHPMailerAutoload.php";

                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->Mailer = "smtps";
                $mail->CharSet = "UTF-8";
                $mail->SMTPDebug = 0;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = "tls";
                $mail->Port = 465;
                $mail->Host = $email_host;
                $mail->Username = $email_username;
                $mail->Password = $email_password;
                $mail->IsHTML(true);
                $mail->AddAddress($useremail);
                $mail->setFrom($email_username);
                $mail->Subject =
                    "Recover your " . $application_name . " Account";

                $mail->MsgHTML(
                    $dear .
                        " " .
                        $username .
                        ", <br>" .
                        $recovery_email_first .
                        " <a href='" .
                        $absolute_path .
                        $final_recovery_page .
                        "?email=" .
                        $useremail .
                        "&hash=" .
                        $recovery_hash .
                        "' style='color:blue;'>" .
                        $recovery_email_second .
                        "</a>"
                );
                if (!$mail->Send()) {
                    $account_recovery_message =
                        $account_recovery_error . " <br><br>";
                }
            }
        }
    } else {
        $account_recovery_message = $no_email_found . " <br><br>";
    }
}

$webpage_name = $recover_your;

?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title><?php echo $webpage_name; ?></title>
      <link rel="stylesheet" href="<?php echo $css_stylesheet_location; ?>">
      <link rel="stylesheet" href="bootstrap.css">
      <link rel="icon" type="image/ico" href="<?php echo $favicon_path; ?>">
   </head>
   <body>
      <br><img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $index_page; ?>';" height="80px">
      <div style="background-color:#ffffff;" class="jumbotron">
         <h1 style="font-size:35px; text-align:center;" class="display-4"><?php echo $recover_title; ?></h1>
         <hr class="my-4">
         <u><a style="color:blue;" href="<?php echo $registration_landing_page; ?>"><?php echo $no_account; ?></a></u> <br> <u><a style="color:blue;" href="<?php echo $index_page; ?>"><?php echo $already_have_account; ?></a></u><br><br>
         <?php if(!empty($account_recovery_message)) { ?>
         <mark><?php echo $account_recovery_message; ?></mark>
         <?php } ?>
         <form method="post" class="card p-3 bg-light">
            <p style="font-size:20px;"><?php echo $enter_your_email; ?></p>
            <input type="email" name="useremail" required="true">
            <br><br><br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="recover" value="<?php echo $recover ?>">
            </form>
            <br><br>
         <a href="<?php echo $translate_page; ?>?language=en&url=<?php echo $recovery_page; ?>"><img src="media/flags/en.png" width="25px;" height="15px;"> English</a> |
         <a href="<?php echo $translate_page; ?>?language=it&url=<?php echo $recovery_page; ?>"><img src="media/flags/it.png" width="25px;" height="15px;"> Italiano</a> |
         <a href="<?php echo $translate_page; ?>?language=zh&url=<?php echo $recovery_page; ?>"><img src="media/flags/zh.png" width="25px;" height="15px;"> 中国人</a>
         <br><br>
         <p style="text-align:center; display:inline;"><?php echo $copyright; ?></p>
         <a href="<?php echo $terms_page; ?>"><?php echo $terms_title;?></a> | <a href="<?php echo $privacy_page; ?>"><?php echo $privacy_title;?></a>
      </div>
   </body>
</html>