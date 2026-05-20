<?php

session_start();

require "configuration.php";

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];

$mediatype = $_GET["type"];

$messageid = $_GET["id"];

$return_page = $_GET["return"];
$return_page = str_replace("@","#",$return_page);

if ($mediatype === "image") {
    

    $findmediasql = "SELECT * FROM Message WHERE messageid = ?";
    $findmediastatement = $database_connection->prepare($findmediasql);
    $findmediastatement->bind_param("s", $messageid);

    $findmediastatement->execute();
    $findmediaresult = $findmediastatement->get_result();

    if (mysqli_num_rows($findmediaresult) > 0) {
        while ($findmediarow = $findmediaresult->fetch_assoc()) {
            $messagegroup = $findmediarow["messagegroup"];
            $messagerecipient = $findmediarow["messagerecipient"];
            $messagemedia = encrypt_decrypt($findmediarow["messagemedia"], "decrypt");

            $mediaurl = str_replace("media/upload_image/", "", $messagemedia);
            $mediaurl = substr($mediaurl, 0, -4);

            $base64_encoding = base64_encode(file_get_contents("https://www.logos.chat/media/upload_image/" .$mediaurl));
            
            $mediacode = "<img max-width='50px;' width='80%;' src='data:image/png;base64, " .$base64_encoding ."'>";

            if (!empty($messagegroup) || !empty($messagerecipient)) {
                $findgroupsql =
                    "SELECT * FROM Groupmember WHERE groupid = ? AND groupmembercode = ?";
                $findgroupstatement = $database_connection->prepare(
                    $findgroupsql
                );
                $findgroupstatement->bind_param(
                    "ss",
                    $messagegroup,
                    $membercode
                );

                $findgroupstatement->execute();
                $findgroupresult = $findgroupstatement->get_result();

                if (mysqli_num_rows($findgroupresult) === 0) {
                    $findmessagesql =
                        "SELECT * FROM Message WHERE messageid = ? AND (messagerecipient = ? OR messagesender = ?)";
                    $findrecipientstatement = $database_connection->prepare(
                        $findmessagesql
                    );
                    $findrecipientstatement->bind_param(
                        "sss",
                        $messageid,
                        $membercode,
                        $membercode
                    );

                    $findrecipientstatement->execute();
                    $findrecipientresult = $findrecipientstatement->get_result();

                    if (mysqli_num_rows($findrecipientresult) === 0) {
                        $nullmedia = 1;
                    }
                }
            } else {
                $nullmedia = 1;
            }
        }
    } else {
        $nullmedia = 1;
    }
    
} else if ($mediatype === "video") { 
    
    $findmediasql = "SELECT * FROM Message WHERE messageid = ?";
    $findmediastatement = $database_connection->prepare($findmediasql);
    $findmediastatement->bind_param("s", $messageid);

    $findmediastatement->execute();
    $findmediaresult = $findmediastatement->get_result();

    if (mysqli_num_rows($findmediaresult) > 0) {
        while ($findmediarow = $findmediaresult->fetch_assoc()) {
            $messagegroup = $findmediarow["messagegroup"];
            $messagerecipient = $findmediarow["messagerecipient"];
            $messagemedia = encrypt_decrypt($findmediarow["messagemedia"], "decrypt");

            $mediaurl = str_replace("media/upload_video/", "", $messagemedia);

            $base64_encoding = base64_encode(file_get_contents("https://www.logos.chat/media/upload_video/" .$mediaurl));

            $mediacode = "<video max-width='50px;' width='80%;' controls autoplay>
                         <source src='data:video/mp4;base64, " .$base64_encoding ."'>
                         Your browser does not support the video tag.
                         </video>";
            
            if (!empty($messagegroup) || !empty($messagerecipient)) {
                $findgroupsql =
                    "SELECT * FROM Groupmember WHERE groupid = ? AND groupmembercode = ?";
                $findgroupstatement = $database_connection->prepare(
                    $findgroupsql
                );
                $findgroupstatement->bind_param(
                    "ss",
                    $messagegroup,
                    $membercode
                );

                $findgroupstatement->execute();
                $findgroupresult = $findgroupstatement->get_result();

                if (mysqli_num_rows($findgroupresult) === 0) {
                    $findmessagesql =
                        "SELECT * FROM Message WHERE messageid = ? AND (messagerecipient = ? OR messagesender = ?)";
                    $findrecipientstatement = $database_connection->prepare(
                        $findmessagesql
                    );
                    $findrecipientstatement->bind_param(
                        "sss",
                        $messageid,
                        $membercode,
                        $membercode
                    );

                    $findrecipientstatement->execute();
                    $findrecipientresult = $findrecipientstatement->get_result();

                    if (mysqli_num_rows($findrecipientresult) === 0) {
                        $nullmedia = 1;
                    }
                }
            } else {
                $nullmedia = 1;
            }
        }
    } else {
        $nullmedia = 1;
    }
    
} else {
    $nullmedia = 1;
}

if ($nullmedia === 1) {
    header("Location: " . $home_page);
}

$webpage_name = $application_name . " - " . $view_media_notice;

?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title><?php echo $webpage_name; ?></title>
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
      <div style="background-color:#ffffff;" class="jumbotron">
          
      <a href="<?php echo $return_page; ?>"><img src="<?php echo $goback_icon_path; ?>" width="100px;"></a><br><br>
      <div id="showmedia" style="text-align:center;">
          <?php echo $mediacode; ?>
      </div>
      </div>
   </body>
</html>