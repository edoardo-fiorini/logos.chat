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
$image = $_FILES["image"]["tmp_name"];
$video = $_FILES["video"]["tmp_name"];
$groupid = $_POST["groupid"];

    if (!empty($messagetext)) { 
  
        $messagerecipient = $interlocutor;
        $messagesender = $membercode;

        $newmessagesql =
            "INSERT INTO Message (messagetext, messagesender, messagegroup) VALUES(?, ?, ?)";
        $newmessagestatement = $database_connection->prepare($newmessagesql);
        $newmessagestatement->bind_param(
            "sss",
            encrypt_decrypt($messagetext, "encrypt"),
            $messagesender,
            $groupid
        );
        if ($newmessagestatement->execute()) {
            
            $sent = 1;
            
            $chaturl = $group_page . "?groupid=" . $groupid;

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

            $findmemberssql = "SELECT * FROM Groupmember WHERE groupid = ?";
            $findmembersstatement = $database_connection->prepare(
                $findmemberssql
            );
            $findmembersstatement->bind_param("s", $groupid);
            $findmembersstatement->execute();
            $findmembersresult = $findmembersstatement->get_result();

            while ($findmembersrow = $findmembersresult->fetch_assoc()) {
                $updatechatsql =
                    "UPDATE Chat SET chatlastmessage = ? WHERE chaturl = ?";
                $updatechatstatement = $database_connection->prepare(
                    $updatechatsql
                );
                $updatechatstatement->bind_param("ss", $messageid, $chaturl);
                $updatechatstatement->execute();
            }

            $findchatsql =
                "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
            $findchatstatement = $database_connection->prepare($findchatsql);
            $findchatstatement->bind_param("ss", $membercode, $chaturl);
            $findchatstatement->execute();
            $findchatresult = $findchatstatement->get_result();

            if (mysqli_num_rows($findchatresult) === 0) {
                $newchatsql =
                    "INSERT INTO Chat (chatlastmessage, chatowner, chaturl) VALUES(?, ?, ?)";
                $newchatstatement = $database_connection->prepare($newchatsql);
                $newchatstatement->bind_param(
                    "sss",
                    $messageid,
                    $membercode,
                    $chaturl
                );
                $newchatstatement->execute();
            }
        }
    }
    
    if (isset($image)) {
    
        $code =
            $membercode .
            "_to_" .
            $groupid .
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
                    "INSERT INTO Message (messagemedia, messagesender, messagegroup) VALUES(?, ?, ?)";
                $newmessagestatement = $database_connection->prepare(
                    $newmessagesql
                );
                $newmessagestatement->bind_param(
                    "sss",
                    encrypt_decrypt($code, "encrypt"),
                    $membercode,
                    $groupid
                );
                if ($newmessagestatement->execute()) {
                    
                    $sent = 1;
                    
                    $chaturl = $group_page . "?groupid=" . $groupid;

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

                    $findmemberssql =
                        "SELECT * FROM Groupmember WHERE groupid = ?";
                    $findmembersstatement = $database_connection->prepare(
                        $findmemberssql
                    );
                    $findmembersstatement->bind_param("s", $groupid);
                    $findmembersstatement->execute();
                    $findmembersresult = $findmembersstatement->get_result();

                    while (
                        $findmembersrow = $findmembersresult->fetch_assoc()
                    ) {
                        $updatechatsql =
                            "UPDATE Chat SET chatlastmessage = ? WHERE chaturl = ?";
                        $updatechatstatement = $database_connection->prepare(
                            $updatechatsql
                        );
                        $updatechatstatement->bind_param(
                            "ss",
                            $messageid,
                            $chaturl
                        );
                        $updatechatstatement->execute();
                    }

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
                            "INSERT INTO Chat (chatlastmessage, chatowner, chaturl) VALUES(?, ?, ?)";
                        $newchatstatement = $database_connection->prepare(
                            $newchatsql
                        );
                        $newchatstatement->bind_param(
                            "sss",
                            $messageid,
                            $membercode,
                            $chaturl
                        );
                        $newchatstatement->execute();
                    }
                }
            }
       
        } if (is_uploaded_file($video)) {
    
        $code =
            $membercode .
            "_to_" .
            $groupid .
            "-" .
            time() .
            "_" .
            rand() .
            ".mp4";

        if (
            move_uploaded_file($video, "media/upload_video/" . $code . ".mp4")
        ) {
            if (
                exec("ffmpeg -i media/upload_video/" . $code . ".mp4  -vcodec libx264 -acodec aac media/upload_video/" . $code . ".mp4")
            ) {
                if (file_exists("media/upload_video/" . $code . ".mp4")) {
                   unlink("media/upload_video/" . $code . ".mp4");
                }
            } else {
                $newmessagesql =
                    "INSERT INTO Message (messagemedia, messagesender, messagegroup) VALUES(?, ?, ?)";
                $newmessagestatement = $database_connection->prepare(
                    $newmessagesql
                );
                $newmessagestatement->bind_param(
                    "sss",
                    encrypt_decrypt($code, "encrypt"),
                    $membercode,
                    $groupid
                );
                if ($newmessagestatement->execute()) {
                    
                    $sent = 1;
                    
                    $chaturl = $group_page . "?groupid=" . $groupid;

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

                    $findmemberssql =
                        "SELECT * FROM Groupmember WHERE groupid = ?";
                    $findmembersstatement = $database_connection->prepare(
                        $findmemberssql
                    );
                    $findmembersstatement->bind_param("s", $groupid);
                    $findmembersstatement->execute();
                    $findmembersresult = $findmembersstatement->get_result();

                    while (
                        $findmembersrow = $findmembersresult->fetch_assoc()
                    ) {
                        $updatechatsql =
                            "UPDATE Chat SET chatlastmessage = ? WHERE chaturl = ?";
                        $updatechatstatement = $database_connection->prepare(
                            $updatechatsql
                        );
                        $updatechatstatement->bind_param(
                            "ss",
                            $messageid,
                            $chaturl
                        );
                        $updatechatstatement->execute();
                    }

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
                            "INSERT INTO Chat (chatlastmessage, chatowner, chaturl) VALUES(?, ?, ?)";
                        $newchatstatement = $database_connection->prepare(
                            $newchatsql
                        );
                        $newchatstatement->bind_param(
                            "sss",
                            $messageid,
                            $membercode,
                            $chaturl
                        );
                        $newchatstatement->execute();
                    }
                }
            }
        }
    }

$findparticipantssql =
                "SELECT * FROM Groupmember WHERE groupid = ?";
$findparticipantsstatement = $database_connection->prepare($findparticipantssql);
$findparticipantsstatement->bind_param("s", $groupid);
$findparticipantsstatement->execute();
$findparticipantsresult = $findparticipantsstatement->get_result();

while($findparticipantsrow = $findparticipantsresult->fetch_assoc()) {

$groupparticipant = $findparticipantsrow["groupmembercode"];
    
if(!empty($messagetext)) {
    $prev = ": " .substr($messagetext, 0, 100);
} else if(is_uploaded_file($video)) {
    $prev = ": " .$video_prev;
} else if(is_uploaded_file($image)) {
    $prev = ": " .$image_prev;
}

$chaturl = $group_page ."?groupid=" .$groupid;

$findchatsql = "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
$findchatstatement = $database_connection->prepare($findchatsql);
$findchatstatement->bind_param("ss", $membercode, $chaturl);
$findchatstatement->execute();
$findchatsqlresult = $findchatstatement->get_result();

while($findchatrow = $findchatsqlresult->fetch_assoc()) {
    
$chatid = $findchatrow["chatid"];
    
$notificationsblocksql = "SELECT * FROM BlockedNotifications WHERE notificationblockusercode = ? AND notificationblockchatid = ?";
$notificationsblockstatement = $database_connection->prepare($notificationsblocksql);
$notificationsblockstatement->bind_param("ss", $membercode, $chatid);
$notificationsblockstatement->execute();
$notificationsblockresult = $notificationsblockstatement->get_result();
while($notificationsblockrow = $notificationsblockresult->fetch_assoc()) {
    $notificationsblocked = 1;
}

}


if($sent == 1 && $notificationsblocked != 1) {
    
         $getmembersql = "SELECT * FROM Member WHERE membercode = ? AND lastactivity < (NOW() - INTERVAL 3 MINUTE)";
         $getmemberstatement = $database_connection->prepare($getmembersql);
         $getmemberstatement->bind_param("s", $groupparticipant);
         $getmemberstatement->execute();
         $getmemberresult = $getmemberstatement->get_result();
         while($getmemberrow = $getmemberresult->fetch_assoc()) {
             $membermail = $getmemberrow["memberemail"];
             $allownotifications = $getmemberrow["allownotifications"];
             $allowgroupnotifications = $getmemberrow["allowgroupnotifications"];
         }
         
         if($allownotifications == 1 && $allowgroupnotifications == 1) {
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
         $mail->MsgHTML("<a href='" .$absolute_path .$group_page ."?groupid=" .$groupid ."'>" .$new_message_from .$prev ."</a>");
         $mail->Send();
        }
         
}

      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();

}

?>