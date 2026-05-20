<?php

session_start();

require "configuration.php";

if (isset($_SESSION["isloggedin"])) {
    header("Location: " . $home_page);
    exit();
}

if (isset($_POST["createaccount"])) {
    if (
        !empty($_POST["membername"]) &&
        !empty($_POST["useremail"]) &&
        !empty($_POST["userpassword"]) &&
        !empty($_POST["confirmemail"]) &&
        (!empty($_POST["personalcode"]) || $_POST["generate"] == "on") &&
        isset($_POST["g-recaptcha-response"])
    ) {
        
   $captcha = $_POST["g-recaptcha-response"];
   $ip = $_SERVER["REMOTE_ADDR"];
   $key = "6LeZcCMgAAAAAMl-1nipsz8eUoO2yBBGyZjmDVYy";
   $url = "https://www.google.com/recaptcha/api/siteverify";

   $recaptcha_response = file_get_contents($url ."?secret=" .$key. "&response=" .$captcha."&remoteip=" .$ip);
   $data = json_decode($recaptcha_response);

   if(isset($data->success) &&  $data->success === true) {
   }
   else {
      $successful_account_creation = 3;
   }
 
        $membercode = $_POST["personalcode"];
        $email = $_POST["useremail"];
        $confirmemail = $_POST["confirmemail"];

        $findusersql =
            "SELECT * FROM Member WHERE membercode = ? OR memberemail = ?";
        $finduserstatement = $database_connection->prepare($findusersql);
        $finduserstatement->bind_param("ss", $membercode, $email);
        $finduserstatement->execute();
        $finduserresult = $finduserstatement->get_result();

        if (mysqli_num_rows($finduserresult) > 0) {
            $successful_account_creation = 0;
        } else {
            if ($confirmemail === $email) {
                if ($_POST["generate"] == "on") {
                    $characters =
                        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789?!-()";
                    srand((float) microtime() * 1000000);
                    $i = 0;
                    $code = "";

                    while ($i <= 6) {
                        $num = rand() % 33;
                        $tmp = substr($characters, $num, 1);
                        $code = $code . $tmp;
                        $i++;
                    }
                } else {
                    $code = $_POST["personalcode"];
                }

                $code = trim($code);

                $username = $_POST["membername"];
                $useremail = $_POST["useremail"];
                $userpassword = $_POST["userpassword"];
                $usercode = $code;

                $hashed_password = password_hash(
                    $userpassword,
                    PASSWORD_DEFAULT
                );

                $member_verified = 0;
                
                $allownotifications = 0;

                $verification_hash = md5(rand(0, 1000));

                $successful_account_creation = 0;

                $ip = $_SERVER['REMOTE_ADDR'];
                
                $newusersql =
                    "INSERT INTO Member (membername, memberpassword, memberverified, memberhash, memberemail, membercode, allownotifications, memberip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $newuserstatement = $database_connection->prepare($newusersql);
                $newuserstatement->bind_param(
                    "ssssssss",
                    $username,
                    $hashed_password,
                    $member_verified,
                    $verification_hash,
                    $useremail,
                    $usercode,
                    $allownotifications,
                    $ip
                );

                if ($newuserstatement->execute()) {

                    $successful_account_creation = 1;

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
                    $mail->Subject = "Welcome to " . $application_name;

                    $mail->MsgHTML(
                        $dear .
                            " " .
                            $username .
                            ", <br>" .
                            $welcome_to .
                            " " .
                            $application_name .
                            $registration_email_first .
                            "  <a href='" .
                            $absolute_path .
                            $activation_page .
                            "?email=" .
                            $useremail .
                            "&hash=" .
                            $verification_hash .
                            "' style='color:blue;'>" .
                            $registration_email_second .
                            "</a>"
                    );
                    if (!$mail->Send()) {
                        $successful_account_creation = 0;
                    }
                } 
            } else {
                $successful_account_creation = 2;
            }
        }

        $account_creation_message = "";

        if ($successful_account_creation === 1) {
            $account_creation_message =
                $successful_account_creation_message .
                " (" .
                $useremail .
                "). " .
                $done_warning;
        } elseif ($successful_account_creation === 0) {
            $account_creation_message =
                $unsuccessful_account_creation .
                "<a style='color:blue;' href='" .
                $registration_landing_page .
                "'>" .
                $try_again .
                "</a>.";
        } elseif ($successful_account_creation === 2) {
            $account_creation_message =
                $unsuccessful_account_creation_two .
                "<a style='color:blue;' href='" .
                $registration_landing_page .
                "'>" .
                $try_again .
                "</a>.";
        } elseif ($successful_account_creation === 3) {
            $account_creation_message =
                $unsuccessful_account_creation_two .
                "<a style='color:blue;' href='" .
                $registration_landing_page .
                "'>" .
                $try_again .
                "</a>.";
        }
    } else {
        header("Location: " .$index_page);
    }
} else {
    header("Location: " .$index_page);
}

$webpage_name =
    $new_account_title_first . $application_name . $new_account_title_second;

?>


<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title><?php echo $webpage_name; ?></title>
      <link rel="icon" type="image/ico" href="<?php echo $favicon_path; ?>">
   </head>
   <body>
      <br><img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $index_page; ?>';" height="80px">
      
      <p style="text-align:center; font-size:25px;"><?php echo $account_creation_message; ?></p>
   </body>
</html>