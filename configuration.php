<?php

require "translate.php";

$application_name = "Logos";

$database_username = "Sql1626988";
$database_password = "7EsiL@2Pg+o()s32FHjiIR";
$database_server = "31.11.39.91";
$database_name = "Sql1626988_1";

$database_connection = mysqli_connect(
    $database_server,
    $database_username,
    $database_password,
    $database_name
);

$membercode = $_SESSION["membercode"];

$absolute_path = "https://logos.chat/";
$registration_landing_page = "register.php";
$index_page = "index.php";
$activation_page = "activate.php";
$recovery_page = "recover.php";
$access_page = "index.php";
$create_account_page = "createaccount.php";
$chat_page = "conversation.php";
$group_page = "group.php";
$settings_page = "settings.php";
$delete_page = "delete.php";
$final_recovery_page = "recover-complete.php";
$logout_page = "logout.php";
$home_page = "home.php";
$ban_page = "ban.php";
$translate_page = "translate.php";
$wrongsearch_page = "wsearch.php";
$newprivatemessage_page = "new_private_message.php";
$deletemessage_page = "deletemessage.php";
$contacthint_page = "contacthint.php";
$grouphint_page = "grouphint.php";
$chathint_page = "chathint.php";
$terms_page = "terms.php";
$privacy_page = "privacy.php";
$view_media = "viewmedia.php";

$media_folder = "media/";
$media_folder_location = $absolute_path . $media_folder;

$application_logo_file = "logos.png";
$application_logo_path = $media_folder_location . $application_logo_file;

$settings_icon_file = "settingsicon.png";
$settings_icon_path = $media_folder_location . $settings_icon_file;

$exit_icon_file = "exiticon.png";
$exit_icon_path = $media_folder_location . $exit_icon_file;

$home_icon_file = "homeicon.png";
$home_icon_path = $media_folder_location . $home_icon_file;

$goback_icon_file = "goback.png";
$goback_icon_path = $media_folder_location . $goback_icon_file;

$favicon_file = "favicon.ico";
$favicon_path = $favicon_file;

$email_username = "system@logos.chat";
$email_password = "3BowE)9Pc-o(/q481!j&IX";
$email_host = "smtps.aruba.it";

$email_username_two = "notifications@logos.chat";
$email_password_two = "3BowE)9Pc-o(/q481!j&IX";

$current_year = date("Y");
$copyright =
    $application_name . " © " . $current_year . " - contacts@logos.chat";

function encrypt_decrypt($string, $action)
{
    $encrypt_method = "AES-256-CBC";
    $secret_key = "AA74CDCC2BBRT935136HH7B63C27";
    $secret_iv = "5fgf5HJ5g27";
    $key = hash("sha256", $secret_key);
    $iv = substr(hash("sha256", $secret_iv), 0, 16);

    if ($action == "encrypt") {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } elseif ($action == "decrypt") {
        $output = openssl_decrypt(
            base64_decode($string),
            $encrypt_method,
            $key,
            0,
            $iv
        );
    }
    return $output;
}

function add_links($text, $target)
{
    if ($target) {
        $target = ' target="' . $target . '"';
    } else {
        $target = "";
    }

    $text = preg_replace(
        "@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.~]*(\?\S+)?)?)*)@",
        '<a href="$1" ' . $target . '>$1</a>',
        $text
    );

    $text = preg_replace(
        '/<a\s[^>]*href\s*=\s*"((?!https?:\/\/)[^"]*)"[^>]*>/i',
        '<a href="http://$1" ' . $target . ">",
        $text
    );

    return $text;
}

$findbanssql =
    "SELECT * FROM Ban WHERE banowner = ? AND banexpiry > NOW() LIMIT 1";
$findbansstatement = $database_connection->prepare($findbanssql);
$findbansstatement->bind_param("s", $membercode);
$findbansstatement->execute();
$findbansresult = $findbansstatement->get_result();

if (mysqli_num_rows($findbansresult) > 0) {
    while ($findbansrow = $findbansresult->fetch_assoc()) {
        header(
            "Location: " . $logout_page . "?ban=" . $findbansrow["banreason"]
        );
    }
}

function compressImage($source, $destination, $quality)
{
    $imageInfo = getimagesize($source);
    $mime = $imgInfo["mime"];

    switch ($mime) {
        case "image/jpeg":
            $image = imagecreatefromjpeg($source);
            break;
        case "image/png":
            $image = imagecreatefrompng($source);
            break;
        case "image/gif":
            $image = imagecreatefromgif($source);
            break;
        default:
            $image = imagecreatefromjpeg($source);
    }

    imagejpeg($image, $destination, $quality);

    return $destination;
}

$privatechats = 0;
$groupchats = 0;
$totalchats = 0;

$findchatsnotificationssql =
    "SELECT DISTINCT(messagesender) FROM Message WHERE messagerecipient = ? AND isread = 0";
$findchatsnotificationsstatement = $database_connection->prepare(
    $findchatsnotificationssql
);
$findchatsnotificationsstatement->bind_param("s", $membercode);
$findchatsnotificationsstatement->execute();
$findchatsnotificationsresult = $findchatsnotificationsstatement->get_result();
if (mysqli_num_rows($findchatsnotificationsresult) > 0) {

    while (
        $findchatsnotificationsrow = $findchatsnotificationsresult->fetch_assoc()
    ) {
        $privatechats++;

        $messagesender = $findchatsnotificationsrow["messagesender"];

        $findcreationnotificationssql =
            "SELECT * FROM Message WHERE messagesender = ? AND messagecreation >= DATE_SUB(NOW(), INTERVAL 4 SECOND) LIMIT 1";
        $findcreationnstatement = $database_connection->prepare(
            $findcreationnotificationssql
        );
        $findcreationnstatement->bind_param("s", $messagesender);
        $findcreationnstatement->execute();
        $findcreationnotificationsresult = $findcreationnstatement->get_result();

        if (mysqli_num_rows($findcreationnotificationsresult) > 0) {
            while (
                $findcreationnotificationsrow = $findcreationnotificationsresult->fetch_assoc()
            ) {
                
                $messagetext = encrypt_decrypt($findcreationnotificationsrow["messagetext"], "decrypt");
                $messagemedia = encrypt_decrypt($findcreationnotificationsrow["messagemedia"], "decrypt");
                
                $findcontactsql =
                    "SELECT * FROM Contact WHERE contactowner = ? AND contactcode = ?";
                $findcontactstatement = $database_connection->prepare(
                    $findcontactsql
                );
                $findcontactstatement->bind_param(
                    "ss",
                    $membercode,
                    $messagesender
                );
                $findcontactstatement->execute();
                $findcontactresult = $findcontactstatement->get_result();

                if (mysqli_num_rows($findcontactresult) > 0) {
                    while (
                        $findcontactrow = $findcontactresult->fetch_assoc()
                    ) {
                        $usercontactname = encrypt_decrypt(
                            $findcontactrow["contactname"],
                            "decrypt"
                        );

                        $findmembersql =
                            "SELECT * FROM Member WHERE membercode = ?";
                        $findmemberstatement = $database_connection->prepare(
                            $findmembersql
                        );
                        $findmemberstatement->bind_param("s", $messagesender);
                        $findmemberstatement->execute();
                        $findmemberresult = $findmemberstatement->get_result();

                        while (
                            $findmemberrow = $findmemberresult->fetch_assoc()
                        ) {
                            $contactid = $findmemberrow["memberid"];
                        }
                    }
                } else {
                    $findmembersql =
                        "SELECT * FROM Member WHERE membercode = ?";
                    $findmemberstatement = $database_connection->prepare(
                        $findmembersql
                    );
                    $findmemberstatement->bind_param("s", $messagesender);
                    $findmemberstatement->execute();
                    $findmemberresult = $findmemberstatement->get_result();

                    while ($findmemberrow = $findmemberresult->fetch_assoc()) {
                        $usercontactname = $findmemberrow["membername"];
                        $contactid = $findmemberrow["memberid"];
                    }
                }

                if (
                    file_exists(
                        "media/PFP/" .
                            $contactid .
                            "-" .
                            $messagesender .
                            ".png"
                    )
                ) {
                    $PFP_path =
                        "media/PFP/" .
                        $contactid .
                        "-" .
                        $messagesender .
                        ".png";
                } else {
                    $PFP_path = "media/PFP/default.png";
                }

                $mediatype = substr(
                    $messagemedia,
                    strpos($messagemedia, ".") + 1
                );

                if (empty($messagetext) && !empty($messagemedia)) {
                    if ($mediatype === "png") {
                        $preview =
                            ": <i style='font-size:17px;' class='fas fa-camera'></i>";
                    } elseif ($mediatype === "mp4") {
                        $preview =
                            ": <i style='font-size:17px;' class='fas fa-video'></i>";
                    }
                } elseif (!empty($messagetext) && empty($messagemedia)) {
                    if (strlen($messagetext) > 15) {
                        $previewdots = "...";
                    } else {
                        $previewdots = "";
                    }

                    $preview =
                        ": " . htmlspecialchars(substr($messagetext, 0, 15)) . $previewdots;
                }

                $upcoming_message =
                    "<a style='color:#000000;' href='" .
                    $chat_page .
                    "?interlocutor=" .
                    $messagesender .
                    "'><img style='border-radius:50%;' height='17px;' width='17px;' src='" .
                    $PFP_path .
                    "'></img> <span style='text-align:left; font-size:15px;' class='lead'>" .
                    htmlspecialchars(
                        $usercontactname
                    ) .$preview 
                    ."</span></a>";
            }
        } else {
            $usercontactname = "";
            $upcoming_message = "";
        }
    }
}

$findgroupnotificationssql =
    "SELECT * FROM Groupmember WHERE groupmembercode = ?";
$findgroupnotificationsstatement = $database_connection->prepare(
    $findgroupnotificationssql
);
$findgroupnotificationsstatement->bind_param("s", $membercode);
$findgroupnotificationsstatement->execute();
$findgroupnotificationsresult = $findgroupnotificationsstatement->get_result();
while (
    $findgroupnotificationsrow = $findgroupnotificationsresult->fetch_assoc()
) {

    $groupid = $findgroupnotificationsrow["groupid"];
    $groupcreation = $findgroupnotificationsrow["groupcreation"];
    $lastread = $findgroupnotificationsrow["groupmemberread"];

    $findmessagenotificationssql =
        "SELECT * FROM Message WHERE messagegroup = ? AND messagecreation > ?";
    $findmessagenotificationsstatement = $database_connection->prepare(
        $findmessagenotificationssql
    );
    $findmessagenotificationsstatement->bind_param("ss", $groupid, $lastread);
    $findmessagenotificationsstatement->execute();
    $findmessagenotificationsresult = $findmessagenotificationsstatement->get_result();
    if (mysqli_num_rows($findmessagenotificationsresult) > 0) {
        while($findmessagenotificationsrow = $findmessagenotificationsresult->fetch_assoc()) {
        
        $messageid = $findmessagenotificationsrow["messageid"];
        $messagesender = $findmessagenotificationsrow["messagesender"];

        $findcreationnotificationssql =
            "SELECT * FROM Message WHERE messageid = ? AND messagecreation >= DATE_SUB(NOW(), INTERVAL 4 SECOND) LIMIT 1";
        $findcreationnstatement = $database_connection->prepare(
            $findcreationnotificationssql
        );
        $findcreationnstatement->bind_param("s", $messageid);
        $findcreationnstatement->execute();
        $findcreationnotificationsresult = $findcreationnstatement->get_result();

        if (mysqli_num_rows($findcreationnotificationsresult) > 0) {
            while (
                $findcreationnotificationsrow = $findcreationnotificationsresult->fetch_assoc()
            ) {
                
                $messagetext = encrypt_decrypt($findcreationnotificationsrow["messagetext"], "decrypt");
                $messagemedia = encrypt_decrypt($findcreationnotificationsrow["messagemedia"], "decrypt");
                
                $findcontactsql =
                    "SELECT * FROM Contact WHERE contactowner = ? AND contactcode = ?";
                $findcontactstatement = $database_connection->prepare(
                    $findcontactsql
                );
                $findcontactstatement->bind_param(
                    "ss",
                    $membercode,
                    $messagesender
                );
                $findcontactstatement->execute();
                $findcontactresult = $findcontactstatement->get_result();

                if (mysqli_num_rows($findcontactresult) > 0) {
                    while (
                        $findcontactrow = $findcontactresult->fetch_assoc()
                    ) {
                        $usercontactname = encrypt_decrypt(
                            $findcontactrow["contactname"],
                            "decrypt"
                        );

                        $findmembersql =
                            "SELECT * FROM Member WHERE membercode = ?";
                        $findmemberstatement = $database_connection->prepare(
                            $findmembersql
                        );
                        $findmemberstatement->bind_param("s", $messagesender);
                        $findmemberstatement->execute();
                        $findmemberresult = $findmemberstatement->get_result();

                        while (
                            $findmemberrow = $findmemberresult->fetch_assoc()
                        ) {
                            $contactid = $findmemberrow["memberid"];
                        }
                    }
                } else {
                    $findmembersql =
                        "SELECT * FROM Member WHERE membercode = ?";
                    $findmemberstatement = $database_connection->prepare(
                        $findmembersql
                    );
                    $findmemberstatement->bind_param("s", $messagesender);
                    $findmemberstatement->execute();
                    $findmemberresult = $findmemberstatement->get_result();

                    while ($findmemberrow = $findmemberresult->fetch_assoc()) {
                        $usercontactname = $findmemberrow["membername"];
                        $contactid = $findmemberrow["memberid"];
                    }
                }

                if (
                    file_exists(
                        "media/group_PFP/" .
                            $groupid .
                            "-" .
                            $groupcreation .
                            ".png"
                    )
                ) {
                    $PFP_path =
                        "media/group_PFP/" .
                        $groupid .
                        "-" .
                        $groupcreation .
                        ".png";
                } else {
                    $PFP_path = "media/group_PFP/default.png";
                }

                $mediatype = substr(
                    $messagemedia,
                    strpos($messagemedia, ".") + 1
                );

                if (empty($messagetext) && !empty($messagemedia)) {
                    if ($mediatype === "png") {
                        $preview =
                            ": <i style='font-size:17px;' class='fas fa-camera'></i>";
                    } elseif ($mediatype === "mp4") {
                        $preview =
                            ": <i style='font-size:17px;' class='fas fa-video'></i>";
                    }
                } elseif (!empty($messagetext) && empty($messagemedia)) {
                    if (strlen($messagetext) > 15) {
                        $previewdots = "...";
                    } else {
                        $previewdots = "";
                    }

                    $preview =
                        ": " . htmlspecialchars(substr($messagetext, 0, 15)) . $previewdots;
                }

                $upcoming_message =
                    "<a style='color:#000000;' href='" .
                    $group_page .
                    "?groupid=" .
                    $groupid .
                    "'><img style='border-radius:50%;' height='17px;' width='17px;' src='" .
                    $PFP_path .
                    "'></img> <span style='text-align:left; font-size:15px;' class='lead'>" .
                    htmlspecialchars(
                        $usercontactname
                    ) .$preview 
                    ."</span></a>";
            }
        } else {
            $usercontactname = "";
            $upcoming_message = "";
        }
        
        }
        
        $groupchats++;
        
    }
}

$totalchats = $groupchats + $privatechats;

if ($totalchats > 0) {
    
    if(!empty($upcoming_message)) {
    
    $notificate =
        "<div style='border-style: outset; display:inline; float:right;'>" .$upcoming_message ."</div></i>";
    } else {
        
    $notificate =
        "<i style='float:right; font-size:17px; color:#0463cb;' class='fa fa-bell'>$test <span style='color:#000000; font-family:verdana;'>" .
        $totalchats .   
        "</span></i>";
    }
} else {
    $notificate = "";
}

?>