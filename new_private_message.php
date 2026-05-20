<?php

session_start();

require "configuration.php";

if (!isset($_SESSION["isloggedin"])) {
    header("Location: " . $access_page);
    exit();
}

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];

$messagetext = $_POST["messagetext"];

$interlocutor = encrypt_decrypt($_POST["il"], "decrypt");

$findblocksql = "SELECT * FROM Block WHERE blockcode = ? AND blockowner = ?";
$findblockstatement = $database_connection->prepare($findblocksql);
$findblockstatement->bind_param("ss", $membercode, $interlocutor);
$findblockstatement->execute();
$findblockresult = $findblockstatement->get_result();

if (mysqli_num_rows($findblockresult) > 0) {
    $youblocked = 1;
} else {
    $youblocked = 0;
}

if (!empty($_POST["messagetext"])) {
    if ($youblocked != 1) {
        $messagerecipient = $interlocutor;
        $messagesender = $membercode;

        $newmessagesql =
            "INSERT INTO Message (messagetext, messagerecipient, messagesender) VALUES(?, ?, ?)";
        $newmessagestatement = $database_connection->prepare($newmessagesql);
        $newmessagestatement->bind_param(
            "sss",
            encrypt_decrypt($messagetext, "encrypt"),
            $messagerecipient,
            $messagesender
        );
        if ($newmessagestatement->execute()) {
            $sent = 1;
            $findmessagesql =
                "SELECT * FROM Message ORDER BY messagecreation DESC LIMIT 1";
            $findmessagestatement = $database_connection->prepare(
                $findmessagesql
            );
            $findmessagestatement->execute();
            $findmessageresult = $findmessagestatement->get_result();

            if ($findmessagerow = $findmessageresult->fetch_assoc()) {
                $messageid = $findmessagerow["messageid"];
            }

            $chaturl = $chat_page . "?interlocutor=" . $interlocutor;

            $findchatsql =
                "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
            $findchatstatement = $database_connection->prepare($findchatsql);
            $findchatstatement->bind_param("ss", $membercode, $chaturl);
            $findchatstatement->execute();
            $findchatresult = $findchatstatement->get_result();

            if (mysqli_num_rows($findchatresult) === 0) {
                $newchatsql =
                    "INSERT INTO Chat (chatowner, chaturl) VALUES(?, ?)";
                $newchatstatement = $database_connection->prepare($newchatsql);
                $newchatstatement->bind_param("ss", $membercode, $chaturl);
                $newchatstatement->execute();
            }

            $updatechatsql =
                "UPDATE Chat SET chatlastmessage = ? WHERE chatowner = ? AND chaturl = ?";
            $updatechatstatement = $database_connection->prepare(
                $updatechatsql
            );
            $updatechatstatement->bind_param(
                "sss",
                $messageid,
                $membercode,
                $chaturl
            );

            $updatechatstatement->execute();

            $chaturlsecond = $chat_page . "?interlocutor=" . $membercode;

            $updatechatsecondsql =
                "UPDATE Chat SET chatlastmessage = ? WHERE chatowner = ? AND chaturl = ?";
            $updatechatsecondstatement = $database_connection->prepare(
                $updatechatsecondsql
            );
            $updatechatsecondstatement->bind_param(
                "sss",
                $messageid,
                $interlocutor,
                $chaturlsecond
            );

            $updatechatsecondstatement->execute();

            $chaturl = $chat_page . "?interlocutor=" . $membercode;

            $findchatsql =
                "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
            $findchatstatement = $database_connection->prepare($findchatsql);
            $findchatstatement->bind_param("ss", $interlocutor, $chaturl);
            $findchatstatement->execute();
            $findchatresult = $findchatstatement->get_result();

            if (mysqli_num_rows($findchatresult) === 0) {
                $newchatsql =
                    "INSERT INTO Chat (chatowner, chaturl) VALUES(?, ?)";
                $newchatstatement = $database_connection->prepare($newchatsql);
                $newchatstatement->bind_param("ss", $interlocutor, $chaturl);
                $newchatstatement->execute();
            }

            $updatechatsql =
                "UPDATE Chat SET chatlastmessage = ? WHERE chatowner = ? AND chaturl = ?";
            $updatechatstatement = $database_connection->prepare(
                $updatechatsql
            );
            $updatechatstatement->bind_param(
                "sss",
                $messageid,
                $interlocutor,
                $chaturl
            );
            $updatechatstatement->execute();
        }
    }
}

if (isset($_FILES["image"]["tmp_name"])) {
    if ($youblocked != 1) {
        $code =
            $membercode .
            "_to_" .
            $interlocutor .
            "-" .
            time() .
            "_" .
            rand() .
            ".png";

        
        
        if (
    file_exists($_FILES["image"]["tmp_name"]) &&
    is_uploaded_file($_FILES["image"]["tmp_name"]) &&
    $_FILES["image"]["tmp_name"] < 3000000
) {
        
    $filename = basename($_FILES["image"]["name"]);
        
    $upload_path = "media/upload_image/" . $code . ".png";
        
    
    $fileType = pathinfo($filename, PATHINFO_EXTENSION); 
         
    $allowTypes = array("jpg", "png", "jpeg", "gif"); 
        
    if(in_array($fileType, $allowTypes)) { 

        $imageTemp = $_FILES["image"]["tmp_name"]; 
        $imageSize = $_FILES["image"]["size"];

        $compressedImage = compressImage($imageTemp, $upload_path, 75); 
             
        if($compressedImage) { 
                
            $compressedImageSize = filesize($compressedImage);
        }
    }

        
                $newmessagesql =
                    "INSERT INTO Message (messagemedia, messagerecipient, messagesender) VALUES(?, ?, ?)";
                $newmessagestatement = $database_connection->prepare(
                    $newmessagesql
                );
                $newmessagestatement->bind_param(
                    "sss",
                    encrypt_decrypt($code, "encrypt"),
                    $interlocutor,
                    $membercode
                );
                if ($newmessagestatement->execute()) {
                    $sent = 1;
                    $findmessagesql =
                        "SELECT * FROM Message ORDER BY messagecreation DESC LIMIT 1";
                    $findmessagestatement = $database_connection->prepare(
                        $findmessagesql
                    );
                    $findmessagestatement->execute();
                    $findmessageresult = $findmessagestatement->get_result();

                    if ($findmessagerow = $findmessageresult->fetch_assoc()) {
                        $messageid = $findmessagerow["messageid"];
                    }

                    $chaturl = $chat_page . "?interlocutor=" . $interlocutor;

                    $findchatsql =
                        "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
                    $findchatstatement = $database_connection->prepare(
                        $findchatsql
                    );
                    $findchatstatement->bind_param("ss", $membercode, $chaturl);
                    $findchatstatement->execute();
                    $findchatresult = $findchatstatement->get_result();

                    if (mysqli_num_rows($findchatresult) === 0) {
                        $newchatsql =
                            "INSERT INTO Chat (chatowner, chaturl) VALUES(?, ?)";
                        $newchatstatement = $database_connection->prepare(
                            $newchatsql
                        );
                        $newchatstatement->bind_param(
                            "ss",
                            $membercode,
                            $chaturl
                        );
                        $newchatstatement->execute();
                    }

                    $updatechatsql =
                        "UPDATE Chat SET chatlastmessage = ? WHERE chatowner = ? AND chaturl = ?";
                    $updatechatstatement = $database_connection->prepare(
                        $updatechatsql
                    );
                    $updatechatstatement->bind_param(
                        "sss",
                        $messageid,
                        $membercode,
                        $chaturl
                    );

                    $updatechatstatement->execute();

                    $chaturl = $chat_page . "?interlocutor=" . $membercode;

                    $findchatsql =
                        "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
                    $findchatstatement = $database_connection->prepare(
                        $findchatsql
                    );
                    $findchatstatement->bind_param(
                        "ss",
                        $interlocutor,
                        $chaturl
                    );
                    $findchatstatement->execute();
                    $findchatresult = $findchatstatement->get_result();

                    if (mysqli_num_rows($findchatresult) === 0) {
                        $newchatsql =
                            "INSERT INTO Chat (chatowner, chaturl) VALUES(?, ?)";
                        $newchatstatement = $database_connection->prepare(
                            $newchatsql
                        );
                        $newchatstatement->bind_param(
                            "ss",
                            $interlocutor,
                            $chaturl
                        );
                        $newchatstatement->execute();
                    }

                    $updatechatsql =
                        "UPDATE Chat SET chatlastmessage = ? WHERE chatowner = ? AND chaturl = ?";
                    $updatechatstatement = $database_connection->prepare(
                        $updatechatsql
                    );
                    $updatechatstatement->bind_param(
                        "sss",
                        $messageid,
                        $interlocutor,
                        $chaturl
                    );

                    $updatechatstatement->execute();
                }
        }
    }
}

if (is_uploaded_file($_FILES["video"]["tmp_name"])) {
    if ($youblocked != 1) {
        $code =
            $membercode .
            "_to_" .
            $interlocutor .
            "-" .
            time() .
            "_" .
            rand() .
            ".mp4";

        if (
            move_uploaded_file(
                $_FILES["video"]["tmp_name"],
                "media/upload_video/" . $code . ".mp4"
            )
        ) {
            if (
                exec("ffmpeg -i media/upload_video/" . $code . ".mp4  -vcodec libx264 -acodec aac media/upload_video/" . $code . ".mp4")
            ) {
                unlink("media/upload_video/" . $code . ".mp4");
            } else {
                $newmessagesql =
                    "INSERT INTO Message (messagemedia, messagerecipient, messagesender) VALUES(?, ?, ?)";
                $newmessagestatement = $database_connection->prepare(
                    $newmessagesql
                );
                $newmessagestatement->bind_param(
                    "sss",
                    encrypt_decrypt($code, "encrypt"),
                    $interlocutor,
                    $membercode
                );
                if ($newmessagestatement->execute()) {
                    $sent = 1;
                    $findmessagesql =
                        "SELECT * FROM Message ORDER BY messagecreation DESC LIMIT 1";
                    $findmessagestatement = $database_connection->prepare(
                        $findmessagesql
                    );
                    $findmessagestatement->execute();
                    $findmessageresult = $findmessagestatement->get_result();

                    if ($findmessagerow = $findmessageresult->fetch_assoc()) {
                        $messageid = $findmessagerow["messageid"];
                    }

                    $chaturl = $chat_page . "?interlocutor=" . $interlocutor;

                    $findchatsql =
                        "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
                    $findchatstatement = $database_connection->prepare(
                        $findchatsql
                    );
                    $findchatstatement->bind_param("ss", $membercode, $chaturl);
                    $findchatstatement->execute();
                    $findchatresult = $findchatstatement->get_result();

                    if (mysqli_num_rows($findchatresult) === 0) {
                        $newchatsql =
                            "INSERT INTO Chat (chatowner, chaturl) VALUES(?, ?)";
                        $newchatstatement = $database_connection->prepare(
                            $newchatsql
                        );
                        $newchatstatement->bind_param(
                            "ss",
                            $membercode,
                            $chaturl
                        );
                        $newchatstatement->execute();
                    }

                    $updatechatsql =
                        "UPDATE Chat SET chatlastmessage = ? WHERE chatowner = ? AND chaturl = ?";
                    $updatechatstatement = $database_connection->prepare(
                        $updatechatsql
                    );
                    $updatechatstatement->bind_param(
                        "sss",
                        $messageid,
                        $membercode,
                        $chaturl
                    );

                    $updatechatstatement->execute();

                    $chaturl = $chat_page . "?interlocutor=" . $membercode;

                    $findchatsql =
                        "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
                    $findchatstatement = $database_connection->prepare(
                        $findchatsql
                    );
                    $findchatstatement->bind_param(
                        "ss",
                        $interlocutor,
                        $chaturl
                    );
                    $findchatstatement->execute();
                    $findchatresult = $findchatstatement->get_result();

                    if (mysqli_num_rows($findchatresult) === 0) {
                        $newchatsql =
                            "INSERT INTO Chat (chatowner, chaturl) VALUES(?, ?)";
                        $newchatstatement = $database_connection->prepare(
                            $newchatsql
                        );
                        $newchatstatement->bind_param(
                            "ss",
                            $interlocutor,
                            $chaturl
                        );
                        $newchatstatement->execute();
                    }

                    $updatechatsql =
                        "UPDATE Chat SET chatlastmessage = ? WHERE chatowner = ? AND chaturl = ?";
                    $updatechatstatement = $database_connection->prepare(
                        $updatechatsql
                    );
                    $updatechatstatement->bind_param(
                        "sss",
                        $messageid,
                        $interlocutor,
                        $chaturl
                    );

                    $updatechatstatement->execute();
                }
            }
        }
    }
}

if(!empty($messagetext)) {
    $prev = ": " .substr($messagetext, 0, 100);
} else if(is_uploaded_file($_FILES["video"]["tmp_name"])) {
    $prev = ": " .$video_prev;
} else if(is_uploaded_file($_FILES["image"]["tmp_name"])) {
    $prev = ": " .$image_prev;
}

if($sent == 1) {
    
         $getmembersql = "SELECT * FROM Member WHERE membercode = ? AND lastactivity < (NOW() - INTERVAL 3 MINUTE)";
         $getmemberstatement = $database_connection->prepare($getmembersql);
         $getmemberstatement->bind_param("s", $interlocutor);
         $getmemberstatement->execute();
         $getmemberresult = $getmemberstatement->get_result();
         while($getmemberrow = $getmemberresult->fetch_assoc()) {
             $membermail = $getmemberrow["memberemail"];
             $allownotifications = $getmemberrow["allownotifications"];
             $allowprivatenotifications = $getmemberrow["allowprivatenotifications"];
         }
         
         if($allownotifications == 1 && $allowprivatenotifications == 1) {
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
         $mail->Username = $email_username_two;
         $mail->Password = $email_password_two;
         $mail->IsHTML(true);
         $mail->AddAddress($membermail);
         $mail->setFrom($email_username_two);
         $mail->Subject = $_SESSION["membername"] ." (" .$application_name .")";
         $mail->MsgHTML("<a href='" .$absolute_path .$chat_page ."?interlocutor=" .$interlocutor ."'>" .$new_message_from .$prev ."</a>");
         $mail->Send();
        }
        
      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
         
}

?>