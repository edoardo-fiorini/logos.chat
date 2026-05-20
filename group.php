<?php

session_start();

require "configuration.php";

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];
$groupid = $_GET["groupid"];

      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
      
$findgroupsql = "SELECT * FROM Grouptable WHERE groupid = ?";
$findgroupstatement = $database_connection->prepare($findgroupsql);
$findgroupstatement->bind_param("s", $groupid);
$findgroupstatement->execute();
$findgroupresult = $findgroupstatement->get_result();

while ($findgrouprow = $findgroupresult->fetch_assoc()) {
    $groupname = encrypt_decrypt($findgrouprow["groupname"], "decrypt");
    $groupinfo = $findgrouprow["groupinfo"];
    $groupcreation = $findgrouprow["groupcreation"];

    $ispartecipantsql =
        "SELECT * FROM Groupmember WHERE groupid = ? AND groupmembercode = ?";
    $isparetcipantstatement = $database_connection->prepare($ispartecipantsql);
    $isparetcipantstatement->bind_param("ss", $groupid, $membercode);
    $isparetcipantstatement->execute();
    $ispartecipantresult = $isparetcipantstatement->get_result();

    while ($memberdatarow = $ispartecipantresult->fetch_assoc()) {
        $groupmemberentrance = $memberdatarow["groupmemberentrance"];
        $lastcleared = $memberdatarow["lastcleared"];
        $isadmin = $memberdatarow["groupmemberadmin"];
        $isowner = $memberdatarow["groupmemberowner"];
    }
}

if (
    mysqli_num_rows($findgroupresult) === 0 ||
    mysqli_num_rows($ispartecipantresult) === 0 ||
    !isset($_SESSION["isloggedin"])
) {
    header("Location: " . $home_page);
}

$updatelastreadsql =
    "UPDATE Groupmember SET groupmemberread = now() WHERE groupmembercode = ?";
$updatelastreadstatement = $database_connection->prepare($updatelastreadsql);
$updatelastreadstatement->bind_param("s", $membercode);
$updatelastreadstatement->execute();

if (file_exists("media/group_PFP/". $groupid ."-" .$groupcreation .".png")) {
    $PFP_path = "media/group_PFP/". $groupid ."-" .$groupcreation .".png";
} else {
    $PFP_path = "media/group_PFP/default.png";
}

if (isset($_POST["submit"]) && !empty($_POST["groupname"])) {
    $newgroupname = encrypt_decrypt($_POST["groupname"], "encrypt");
    if ($isadmin) {
        $changegroupnamesql =
            "UPDATE Grouptable SET groupname = ? WHERE groupid = ?";
        $newgroupstatement = $database_connection->prepare($changegroupnamesql);
        $newgroupstatement->bind_param("ss", $newgroupname, $groupid);
        $newgroupstatement->execute();
      
      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
      
        header(
            "Location: " . $group_page . "?groupid=" . $groupid . "#settings"
        );
    }
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

if(isset($_POST["submitnots"])) {

    if($_POST["blocknotifications"] != 1) {

    $blocknotssql = "INSERT INTO BlockedNotifications (notificationblockusercode, notificationblockchatid) VALUES(?, ?)";
    $blocknotsstatement = $database_connection->prepare($blocknotssql);
    $blocknotsstatement->bind_param("ss", $membercode, $chatid);
    $blocknotsstatement->execute();
    } else {
    $blocknotssql = "DELETE FROM BlockedNotifications WHERE notificationblockusercode = ? AND notificationblockchatid = ?";
    $blocknotsstatement = $database_connection->prepare($blocknotssql);
    $blocknotsstatement->bind_param("ss", $membercode, $chatid);
    $blocknotsstatement->execute();
    }
    header("Refresh:0;");
}

if (
    isset($_POST["submit"]) &&
    file_exists($_FILES["PFP"]["tmp_name"]) &&
    is_uploaded_file($_FILES["PFP"]["tmp_name"]) &&
    $_FILES["PFP"]["size"] < 3000000
) {
        
    $filename = basename($_FILES["PFP"]["name"]);
        
    $upload_path = "media/group_PFP/" . $groupid ."-" .$groupcreation .".png";
        
    
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

if (isset($_POST["submit"]) && !empty($_POST["groupinfo"])) {
    $groupinfoinput = $_POST["groupinfo"];
    $changeinfosql = "UPDATE Grouptable SET groupinfo = ? WHERE groupid = ?";
    $changeinfostatement = $database_connection->prepare($changeinfosql);
    $changeinfostatement->bind_param("ss", $groupinfoinput, $groupid);

    $changeinfostatement->execute();
    
          $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
}

if (isset($_POST["clear"])) {
    $clearchatsql =
        "UPDATE Groupmember SET lastcleared = now() WHERE groupmembercode = ? AND groupid = ?";
    $clearchatstatement = $database_connection->prepare($clearchatsql);
    $clearchatstatement->bind_param("ss", $membercode, $groupid);
    $clearchatstatement->execute();
    
          $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
}

if (isset($_POST["leave"])) {
    $leavegroupsql =
        "DELETE FROM Groupmember WHERE groupmembercode = ? AND groupid = ?";
    $leavegroupstatement = $database_connection->prepare($leavegroupsql);
    $leavegroupstatement->bind_param("ss", $membercode, $groupid);
    $leavegroupstatement->execute();

    $chaturl = $group_page . "?groupid=" . $groupid;

    $deletechatsql = "DELETE FROM Chat WHERE chatowner = ? AND chaturl = ?";
    $deletechatstatement = $database_connection->prepare($deletechatsql);
    $deletechatstatement->bind_param("ss", $membercode, $chaturl);
    $deletechatstatement->execute();

    header("Location: " . $home_page);
}

if (isset($_POST["delete"])) {
    if ($isowner) {
        $deletegroupsql = "DELETE FROM Grouptable WHERE groupid = ?";
        $deletegroupstatement = $database_connection->prepare($deletegroupsql);
        $deletegroupstatement->bind_param("s", $groupid);
        $deletegroupstatement->execute();

        $deletememberssql = "DELETE FROM Groupmember WHERE groupid = ?";
        $deletemembersstatement = $database_connection->prepare(
            $deletememberssql
        );
        $deletemembersstatement->bind_param("s", $groupid);
        $deletemembersstatement->execute();

        $chaturl = $group_page . "?groupid=" . $groupid;

        $deletechatsql = "DELETE FROM Chat WHERE chaturl = ?";
        $deletechatstatement = $database_connection->prepare($deletechatsql);
        $deletechatstatement->bind_param("s", $chaturl);
        $deletechatstatement->execute();

        if (file_exists("media/group_PFP/" . $groupid ."-" .$groupcreation . ".png")) {
            unlink("media/group_PFP/" . $groupid ."-" .$groupcreation . ".png");
        }

        header("Refresh:0;");
    }
}

if (isset($_POST["addmember"]) && !empty($_POST["membercode"])) {
    if ($isadmin) {
        $partecipantcode = $_POST["membercode"];

        $findblocksql =
            "SELECT * FROM Block WHERE blockowner = ? AND blockcode = ?";
        $findblockstatement = $database_connection->prepare($findblocksql);
        $findblockstatement->bind_param("ss", $partecipantcode, $membercode);
        $findblockstatement->execute();
        $findblockresult = $findblockstatement->get_result();

        if (mysqli_num_rows($findblockresult) === 0) {
            $findusersql = "SELECT * FROM Member WHERE membercode = ?";
            $finduserstatement = $database_connection->prepare($findusersql);
            $finduserstatement->bind_param("s", $partecipantcode);
            $finduserstatement->execute();
            $finduserresult = $finduserstatement->get_result();

            if (mysqli_num_rows($finduserresult) > 0) {
                $findpartecipantsql =
                    "SELECT * FROM Groupmember WHERE groupmembercode = ? AND groupid = ?";
                $findpartecipantstatement = $database_connection->prepare(
                    $findpartecipantsql
                );
                $findpartecipantstatement->bind_param(
                    "ss",
                    $partecipantcode,
                    $groupid
                );
                $findpartecipantstatement->execute();
                $findpartecipantresult = $findpartecipantstatement->get_result();

                if (mysqli_num_rows($findpartecipantresult) === 0) {
                    $addpartecipantsql =
                        "INSERT INTO Groupmember (groupmembercode, groupid) VALUES(?, ?)";
                    $addpartecipantstatement = $database_connection->prepare(
                        $addpartecipantsql
                    );
                    $addpartecipantstatement->bind_param(
                        "ss",
                        $partecipantcode,
                        $groupid
                    );
                    if ($addpartecipantstatement->execute()) {
                        $chaturl = $group_page . "?groupid=" . $groupid;

                        $newchatsql =
                            "INSERT INTO Chat (chatowner, chaturl) VALUES(?, ?)";
                        $newchatstatement = $database_connection->prepare(
                            $newchatsql
                        );
                        $newchatstatement->bind_param(
                            "ss",
                            $partecipantcode,
                            $chaturl
                        );
                        $newchatstatement->execute();
                        
      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
      
                        $partecipantschanges = true;
                    }
                }
            }
        } else {
            $memberadd = "<mark>" . $blocking_notice . " </mark><br><br>";
        }
    }
}

if (isset($_POST["removemember"]) && !empty($_POST["membercode"])) {
    if ($isadmin) {
        $partecipantcode = $_POST["membercode"];

        $removepartecipantsql =
            "DELETE FROM Groupmember WHERE groupid = ? AND groupmembercode = ? AND groupmemberowner = 0";
        $removepartecipantstatement = $database_connection->prepare(
            $removepartecipantsql
        );
        $removepartecipantstatement->bind_param(
            "ss",
            $groupid,
            $partecipantcode
        );
        $removepartecipantstatement->execute();

        $chaturl = $group_page . "?groupid=" . $groupid;

        $deletechatsql = "DELETE FROM Chat WHERE chatowner = ? AND chaturl = ?";
        $deletechatstatement = $database_connection->prepare($deletechatsql);
        $deletechatstatement->bind_param("ss", $partecipantcode, $chaturl);
        $deletechatstatement->execute();

        $partecipantschanges = true;
    }
}

if (isset($_POST["removeadmintitle"]) && !empty($_POST["membercode"])) {
    if ($isadmin) {
        $partecipantcode = $_POST["membercode"];

        $removeadminsql =
            "UPDATE Groupmember SET groupmemberadmin = 0 WHERE groupid = ? AND groupmembercode = ? AND groupmemberowner = 0";
        $removeadminstatement = $database_connection->prepare($removeadminsql);
        $removeadminstatement->bind_param("ss", $groupid, $partecipantcode);
        $removeadminstatement->execute();
      
      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();

        $partecipantschanges = true;
    }
}

if (isset($_POST["addadmintitle"]) && !empty($_POST["membercode"])) {
    if ($isadmin) {
        $partecipantcode = $_POST["membercode"];

        $removeadminsql =
            "UPDATE Groupmember SET groupmemberadmin = 1 WHERE groupid = ? AND groupmembercode = ?";
        $removeadminstatement = $database_connection->prepare($removeadminsql);
        $removeadminstatement->bind_param("ss", $groupid, $partecipantcode);
        $removeadminstatement->execute();
        
      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();

        $partecipantschanges = true;
    }
}

if ($partecipantschanges) {
    header(
        "Location: " . $group_page . "?groupid=" . $groupid . "#partecipants"
    );
}

$webpage_name = $application_name . " - " . $groupname;

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
      <meta name="format-detection" content="telephone=no">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <link rel="icon" type="image/ico" href="<?php echo $favicon_path; ?>">
      <style>
         input[type='checkbox'] {
         -webkit-appearance: none;
         -moz-appearance: none;
         appearance: none;
         outline: none;
         position: relative;
         width: 1.5rem;
         height: 1.5rem;
         border: 1.5px solid #455A64;
         overflow: hidden;
         border-radius: 3px;
         box-shadow: inset 0 0 5px 0 rgba(0, 0, 0, 0.6);
         cursor: pointer;
         }
         input[type='checkbox']::before {
         content: '';
         color: #fff;
         position: absolute;
         top: 4px;
         right: 4px;
         bottom: 4px;
         left: 4px;
         background-color: transparent;
         background-size: contain;
         background-position: center center;
         background-repeat: no-repeat;
         border-radius: 2px;
         -webkit-transform: scale(0);
         transform: scale(0);
         -webkit-transition: -webkit-transform 0.25s ease-in-out;
         transition: -webkit-transform 0.25s ease-in-out;
         transition: transform 0.25s ease-in-out;
         transition: transform 0.25s ease-in-out, -webkit-transform 0.25s ease-in-out;
         background-image: url("media/confirm.svg");
         }
         input[type='checkbox']:checked::before {
         -webkit-transform: scale(1);
         transform: scale(1);
         }
         
         .noselect {
         -webkit-touch-callout: none; 
         -webkit-user-select: none; 
         -khtml-user-select: none; 
         -moz-user-select: none;
         -ms-user-select: none;
         user-select: none; 
         }
         
         .modal {
         display: none; 
         position: fixed;
         z-index: 1;
         padding-top: 100px;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         overflow: auto;
         background-color: rgb(0,0,0);
         background-color: rgba(0,0,0,0.4);
         }
         .modal-content {
         background-color: #fefefe;
         margin: auto;
         padding: 20px;
         border: 1px solid #888;
         width: 80%;
         }
         .close {
         color: #aaaaaa;
         float: right;
         font-size: 28px;
         font-weight: bold;
         }
         .close:hover,
         .close:focus {
         color: #000;
         text-decoration: none;
         cursor: pointer;
         }
         .menubar { 
             text-align: center; background-color:white; width: 100%; 
         }
      </style>
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

         <div id="menubar" class="menubar" style="text-align:center; width: 100%; color:#ff5632;">
            <a href="<?php echo $home_page; ?>" style="color:#0210a8;"><?php echo $home ." " .$notificate ." "; ?> <img src="<?php echo $home_icon_path; ?>" width="20px;" height="20px;"> </a><span>|</span><a href="<?php echo $settings_page; ?>" style="color:#0210a8;"> <?php echo $settings; ?> <img src="<?php echo $settings_icon_path; ?>" width="20px;" height="20px;"> </a><span>|</span><a href="<?php echo $logout_page; ?>" style="color:#0210a8;"> <?php echo $logout; ?> <img src="<?php echo $exit_icon_path; ?>" width="20px;" height="20px;"></a><br>
            </div> <br>
         
             <script>
   var fixmeTop = $('.menubar').offset().top;
   $(window).scroll(function() {
   var currentScroll = $(window).scrollTop();
   if (currentScroll >= fixmeTop) {
       $('.menubar').css({
           position: 'fixed',
           top: '0',
           left: '0'
       });
   } else {
       $('.menubar').css({
           position: 'static'
       });
   }
   });
   </script>
   
   
      <h1 class="lead" style="text-align:left; font-size:20px;">
         <?php echo htmlspecialchars($groupname); ?> <img src="<?php echo $PFP_path; ?>" width="30px;" height="30px;" style="border-radius:50%;"><br>
         <p style="font-size:15px;"><?php echo htmlspecialchars($groupinfo); ?></p>
      </h1>

              <div id="myModal" class="modal">
           <div class="modal-content">
               <span class="close" onclick="document.getElementById('myModal').style.display = 'none';">&times;</span><br>
            
                  <form method="post" style="display:inline;">
                     
                     <p><span><?php echo $allow_notifications; ?> </span> 

                    <input type="checkbox" value="1" name="blocknotifications" id="blocknotifications" <?php if($notificationsblocked !== 1) { ?> checked="true" <?php } ?>><br><br>

                    <input type="submit" class="btn btn-primary btn-lg active" role="button" style="font-size:10px; margin-bottom:20px;" name="submitnots" value="<?php echo $confirm; ?>">
                    <hr>
                 </form>
                 
        <div>
          
                  <h1 style="text-align:center; font-size:26px;"><?php echo $group_participants; ?></h1>
      <p id="partecipants"></p>
         <div id="partecipantsframe" style="text-align:center;">
             
         <?php
         $contactlist = "<select name='membercode' id='membercode'>";
  
         $findchatssql = "SELECT * FROM Chat WHERE chatowner = ? AND chaturl LIKE 'conversation.php?interlocutor=%'";
         $findchatsstatement = $database_connection->prepare(
             $findchatssql
         );
         $findchatsstatement->bind_param("s", $membercode);

         $findchatsstatement->execute();
         $findchatsresult = $findchatsstatement->get_result();

         while ($findchatsrow = $findchatsresult->fetch_assoc()) {
       
         $chaturl = $findchatsrow["chaturl"];
       
         $chatcode = str_replace($chat_page ."?interlocutor=", "", $chaturl);

         $findusercontactssql = "SELECT * FROM Contact WHERE contactowner = ? AND contactcode = ?";
         $findusercontactsstatement = $database_connection->prepare(
             $findusercontactssql
         );
         $findusercontactsstatement->bind_param("ss", $membercode, $chatcode);

         $findusercontactsstatement->execute();
         $findusercontactsresult = $findusercontactsstatement->get_result();

         if(mysqli_num_rows($findusercontactsresult) > 0) {

         while ($findusercontactsrow = $findusercontactsresult->fetch_assoc()) {
             $contactlist .=
                 "<option value='" .
                 htmlspecialchars($findusercontactsrow["contactcode"]) .
                 "'>" .
                 htmlspecialchars(
                     encrypt_decrypt(
                         $findusercontactsrow["contactname"],
                         "decrypt"
                     )
                 ) .
                 "</option>";
         }
         
         } else {

         $findmemberinfosql = "SELECT * FROM Member WHERE membercode = ?";
         $findmemberinfostatement = $database_connection->prepare(
             $findmemberinfosql
         );
         $findmemberinfostatement->bind_param("s", $chatcode);

         $findmemberinfostatement->execute();
         $findmemberinforesult = $findmemberinfostatement->get_result();

         while ($findmemberinforow = $findmemberinforesult->fetch_assoc()) {
             $contactlist .=
                 "<option value='" .
                 htmlspecialchars($findmemberinforow["membercode"]) .
                 "'>" .
                 htmlspecialchars(
                         $findmemberinforow["membername"]
                 ) .
                 "</option>";
         }
         
         }
         
         }

         $contactlist .= "</select>";

         $list = "<select name='membercode' id='membercode'>";

         $findpartecipantssql = "SELECT * FROM Groupmember WHERE groupid = ?";
         $findpartecipantsstatement = $database_connection->prepare(
             $findpartecipantssql
         );
         $findpartecipantsstatement->bind_param("s", $groupid);

         $findpartecipantsstatement->execute();
         $findpartecipantssresult = $findpartecipantsstatement->get_result();

         while (
             $findpartecipantsrow = $findpartecipantssresult->fetch_assoc()
         ) {
             $partecipantid = $findpartecipantsrow["groupmembercode"];
             $partecipantadmin = $findpartecipantsrow["groupmemberadmin"];

             $findmembersql = "SELECT * FROM Member WHERE membercode = ?";
             $findmemberstatement = $database_connection->prepare(
                 $findmembersql
             );
             $findmemberstatement->bind_param("s", $partecipantid);

             $findmemberstatement->execute();
             $findmemberresult = $findmemberstatement->get_result();

             $findidsql = "SELECT * FROM Member WHERE membercode = ?";
             $findidstatement = $database_connection->prepare($findidsql);
             $findidstatement->bind_param("s", $partecipantid);

             $findidstatement->execute();
             $findidresult = $findidstatement->get_result();

             while ($findidrow = $findidresult->fetch_assoc()) {
                 $id = $findidrow["memberid"];
             }

             while ($findmemberrow = $findmemberresult->fetch_assoc()) {
                 $partecipantcode = $findmemberrow["membercode"];
                 $partecipantname = $findmemberrow["membername"];

                 $findcontactsql =
                     "SELECT * FROM Contact WHERE contactowner = ? AND contactcode = ?";
                 $findcontactstatement = $database_connection->prepare(
                     $findcontactsql
                 );
                 $findcontactstatement->bind_param(
                     "ss",
                     $membercode,
                     $partecipantcode
                 );

                 $findcontactstatement->execute();
                 $findcontactresult = $findcontactstatement->get_result();

                 if (mysqli_num_rows($findcontactresult) > 0) {
                     while (
                         $findcontactrow = $findcontactresult->fetch_assoc()
                     ) {
                         $partecipantname = encrypt_decrypt(
                             $findcontactrow["contactname"],
                             "decrypt"
                         );
                         $partecipantcode = $findcontactrow["contactcode"];
                     }
                 }

                 if (
                     file_exists(
                         "media/PFP/" . $id . "-" . $partecipantcode . ".png"
                     )
                 ) {
                     $PFP_path =
                         "media/PFP/" . $id . "-" . $partecipantcode . ".png";
                 } else {
                     $PFP_path = "media/PFP/default.png";
                 }

                 if ($partecipantadmin === 1) {
                     $admin = $adminnotice;
                 } elseif ($partecipantadmin === 0) {
                     $admin = "";
                 }

                 if ($partecipantid !== $membercode) {
                     $list .=
                         "<option value='" .
                         htmlspecialchars($partecipantcode) .
                         "'>" .
                         htmlspecialchars($partecipantname) .
                         "</option>";
                 }

                 echo "<p style='font-size:20px; text-align:left;'><img src='" .
                     $PFP_path .
                     "' width='35px;' height='35px;' style='border-radius:50%;'> <a style='color:#000000;' href='" .
                     $chat_page .
                     "?interlocutor=" .
                     htmlspecialchars($partecipantcode) .
                     "'>" .
                     htmlspecialchars(
                         $partecipantname .
                             " (" .
                             htmlspecialchars($partecipantcode) .
                             ")"
                     ) .
                     " " .
                     $admin .
                     "</p></a>";
             }
         }

         $list .= "</select>";
         
         if($isadmin) { ?>
         
        <div style="text-align:left;">
         <h1 style="font-size:20px;"><?php echo $add_member; ?></h1>
         <form method="post">
            <?php if(!empty($memberadd)) { 
               echo $memberadd;
               } ?>
            <?php echo $contactlist; ?><br><br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="addmember" value="<?php echo $add_member; ?>">
         </form>
         <br>
         <h1 style="font-size:20px;"><?php echo $remove_member; ?></h1>
         <form method="post">
            <?php echo $list; ?><br><br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="removemember" value="<?php echo $remove_member; ?>">
         </form>
         <br>
         <h1 style="font-size:20px;"><?php echo $add_admin_title; ?></h1>
         <form method="post">
            <?php echo $list; ?><br><br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="addadmintitle" value="<?php echo $add_admin_title; ?>">
         </form>
         <br>

         <h1 style="font-size:20px;"><?php echo $remove_admin_title; ?></h1>
         <form method="post">
            <?php echo $list; ?><br><br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="removeadmintitle" value="<?php echo $remove_admin_title; ?>">
         </form>
         <br>
        </div>
         <?php } ?>
      </div>
      <br>
      <h1 style="text-align:left; font-size:26px;"><?php echo $group_settings; ?></h1>
     <div id="settingsframe" style="text-align:left;">
         <p id="settings"></p>
         <form method="post" enctype="multipart/form-data" id="groupinfoform">
            <?php if($isadmin) { ?>
            <?php echo $modify_group_name; ?> <br><br><input type="text" value="<?php echo htmlspecialchars($groupname); ?>" name="groupname"><br><br>
            <label for="PFP"><span style="color:#3761d4;"><img style="border-radius:50%;" src="<?php if(file_exists("media/group_PFP/" .$groupid ."-" .$groupcreation .".png")) {
               echo "media/group_PFP/" .$groupid ."-" .$groupcreation .".png"; 
               } else {
               echo "media/group_PFP/default.png"; 
               }
               ?>" width="100px;" height="100px;"> <br> <?php echo $select_group_PFP; ?></span></label><input type="file" id="PFP" name="PFP" style="display:none;" accept="image/png, image/jpeg, image/jpg, image/gif"><br><br>
            <textarea style="width:70%; max-width:300px;" rows="4" cols="50" form="groupinfoform" placeholder="<?php echo $group_biography_thrust; ?>" name="groupinfo" id="groupinfo"><?php echo htmlspecialchars($groupinfo); ?></textarea><br><br>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="submit" value="<?php echo $confirm_edits; ?>"> <br><br>       
            <?php } ?>
            <?php if($isowner) { ?> 
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="delete" value="<?php echo $delete_group; ?>"> 
            <?php } ?> <br><br> <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="clear" value="<?php echo $clear_chat; ?>"> <br><br> <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="leave" class="btn btn" value="<?php echo $leave_group; ?>">
         </form>
     </div>
    
     </div>
     </div>
     </div>
      
         <p style="font-size:15px; text-align:center;" id="openmodal" onclick="document.getElementById('myModal').style.display = 'block';"><span class="btn btn" role="button" aria-pressed="true" style="font-size:13px; float:right; background-color:#000000; color:#ffffff;"><?php echo $settings_and_participants; ?></span></p><br><br>

      <script>
         $(document).ready(function() {
         var pageRefresh = 1500;
             setInterval(function() {
                 refresh();
             }, pageRefresh);
         });
         
         function refresh() {
             $("#messagesframe").load(" #messagesframe > *");
             $("#menubar").load(" #menubar > *");
         }
      </script>
      <form id="newmessage" enctype="multipart/form-data">
         <textarea rows="1" autofocus="true" class="form-control" form="newmessage" id="messagetext" name="messagetext"></textarea>
         <br>
         <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="submit" value="<?php echo $send; ?>" onclick="return newMessage();">
         <input type="file" style="display:none;" accept="image/png, image/jpeg, image/jpg, image/gif" id="image" name="image">
         <input type="file" style="display:none;" accept="video/*" id="video" name="video">
         <script>
            document.getElementById("image").onchange = function(e) {
            document.getElementById("checkicon").style.display = "block";
            }

            document.getElementById("video").onchange = function(e) {
            document.getElementById("checkicon").style.display = "block";
            }
         </script>  
         <label for="image">
            <p><span style="color:#3761d4;"><i style="font-size:25px;" class="fas fa-image"></i> </span></p>
         </label>

         <label for="video">
         <span style="color:#3761d4;"><i style="font-size:25px;" class="fas fa-video"></i></span>
         </label>
         
        <div id="checkicon" style="display:none;" name="checkicon"><?php echo $media_selected; ?></div> 
      </form>
      <script>
         $("#newmessage").on("submit", function(ev) {
           ev.preventDefault(); 
         
           var formData = new FormData(this);
           formData.append("groupid", "<?php echo $groupid; ?>");
         
           $.ajax({
             url: "new_group_message.php",
             type: "POST",
             data: formData,
             success: function (message) {
               document.getElementById("messagetext").value = "";
               document.getElementById("image").value = null;
               document.getElementById("video").value = null;
               document.getElementById("checkicon").style.display = "none";
             },
             cache: false,
             contentType: false,
             processData: false
           });
           return false;
         });
        
         function showhide() {
           var x = document.getElementById("messagesframe");
           if (x.style.display === "none") {
             x.style.display = "block";
           } else {
             x.style.display = "none";
           }
         }
      </script>
      <p onclick="showhide()" style="color:#0d05ab; text-align:center;"><?php echo $show_hide; ?></p>
       <div id="messagesframe" class="noselect" style="text-align:center;">
         <?php
            $findmessagessql = "SELECT * FROM Message WHERE messagegroup = ? AND messagecreation > ? ORDER BY messagecreation DESC";
            $findmessagesstatement = $database_connection->prepare($findmessagessql);
            $findmessagesstatement->bind_param("ss", $groupid, $lastcleared);
            
            $findmessagesstatement->execute();
            $findmessagessresult = $findmessagesstatement->get_result();
            
            if(mysqli_num_rows($findmessagessresult) > 0) {
            while ($findmessagesrow = $findmessagessresult->fetch_assoc()) {
            
                 $messageid = $findmessagesrow["messageid"];
                 $messagetext = $findmessagesrow["messagetext"];
                 $messagecreation = date_create($findmessagesrow["messagecreation"]);
                 $messagecreationshort = date_format($messagecreation, "H:i");
                 $messagecreationlong = date_format($messagecreation, "Y/m/d");
            
                 if($findmessagesrow["messagesender"] !== $membercode) {
            
            $findmembersql = "SELECT * FROM Member WHERE membercode = ?";
            $findmemberstatement = $database_connection->prepare($findmembersql);
            $findmemberstatement->bind_param("s", $findmessagesrow["messagesender"]);
            $findmemberstatement->execute();
            $findmembersresult = $findmemberstatement->get_result();
            
            while ($findmemberrow = $findmembersresult->fetch_assoc()) {
                $sender = $findmemberrow["membername"];
                $senderid = $findmemberrow["memberid"];
            }
            
            $findsendercontactsql = "SELECT * FROM Contact WHERE contactowner = ? AND contactcode = ?";
            $findsendercontactstatement = $database_connection->prepare($findsendercontactsql);
            $findsendercontactstatement->bind_param("ss", $membercode, $findmessagesrow["messagesender"]);
            
            $findsendercontactstatement->execute();
            $findsendercontactresult = $findsendercontactstatement->get_result();
            
            if(mysqli_num_rows($findsendercontactresult) > 0) {
            while ($findsendercontactrow = $findsendercontactresult->fetch_assoc()) {
                $sender = encrypt_decrypt($findsendercontactrow["contactname"], "decrypt");
                $sendercode = $findsendercontactrow["contactcode"];
            
            }
            
            }
            
                 if(file_exists("media/PFP/" .$senderid ."-" .$findmessagesrow["messagesender"] .".png")) {
                     $PFP_path = "media/PFP/" .$senderid ."-" .$findmessagesrow["messagesender"] .".png";
                 } else {
                     $PFP_path = "media/PFP/default.png";
                 }
               
                 if(!empty($findmessagesrow["messagetext"]) && empty($findmessagesrow["messagemedia"])) {
            
                 echo "<p id='" .$messageid ."' style='margin-right:20%; text-align:justify; border: 1px solid black; font-size:20px; background-color:#e3e1b3;'><a style='color:#000000;' href='" .$chat_page ."?interlocutor=" .htmlspecialchars($sendercode) ."'><img src='" .$PFP_path ."' width='30px;' height='30px;' style='border-radius:50%;'> <span style='font-size:15px;'>" .htmlspecialchars($sender) ."</span> </a> " .preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/','<a href="$0">$0</a>', htmlspecialchars(encrypt_decrypt($messagetext, "decrypt"))) ." <br><span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span> </p><br>";
                 } else {
                     
                 $media_extension = "." .pathinfo(encrypt_decrypt($findmessagesrow["messagemedia"], "decrypt"), PATHINFO_EXTENSION);
                 
                 if($media_extension === ".png") {
                     
                 echo "<p id='" .$messageid ."' style='border: 1px solid black; margin-right:20%; text-align:justify; font-size:20px; background-color:#e3e1b3;'> <a style='color:#000000;' href='" .$chat_page ."?interlocutor=" .htmlspecialchars($sendercode) ."'><img src='" .$PFP_path ."' width='30px;' height='30px;' style='border-radius:50%;'> <span style='font-size:15px;'>" .htmlspecialchars($sender) ."</span> </a> <a style='color:#5c5c5c;' href='" .$view_media ."?id=" .$messageid ."&type=image&return=" .$group_page ."?groupid=" .$groupid ."@" .$messageid ."'>" .$view_media_notice ." »</a> <br><span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span> </p><br>";
                 } else if($media_extension === ".mp4") {
              
                 echo "<p id='" .$messageid ."' style='margin-right:20%; border: 1px solid black; text-align:justify; font-size:20px; background-color:#e3e1b3;'> <a style='color:#000000;' href='" .$chat_page ."?interlocutor=" .htmlspecialchars($sendercode) ."'><img src='" .$PFP_path ."' width='30px;' height='30px;' style='border-radius:50%;'> <span style='font-size:15px;'>" .htmlspecialchars($sender) ."</span> </a> <a style='color:#5c5c5c;' href='" .$view_media ."?id=" .$messageid ."&type=video&return=" .$group_page ."?groupid=" .$groupid ."@" .$messageid ."'>" .$view_media_notice ." »</a> <br><span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span> </p><br>";                     
                 }
                 
                 }
               } else {
                
                
                    if(file_exists("media/PFP/" .$member ."-" .$membercode .".png")) {
                     $PFP_path = "media/PFP/" .$member ."-" .$membercode .".png";
                    } else {
                     $PFP_path = "media/PFP/default.png";
                    }
                 
                 if(!empty($findmessagesrow["messagetext"]) && empty($findmessagesrow["messagemedia"])) {
                 echo "<p id='" .$messageid ."' style='margin-left:20%; text-align:justify; border: 1px solid black; font-size:20px;'> " .preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/','<a href="$0">$0</a>', htmlspecialchars(encrypt_decrypt($messagetext, "decrypt"))) ." <a href='javascript:;' onClick='like(this);' rel='deletemessage.php?id=" .$messageid ."&interlocutor=" .$interlocutor. "'><i class='fa fa-times' style='color:#cf1720; float:right'></i></a> <br><span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span></p><br>";
            
                        ?>
         <script>
            function like(placeholder) {
                    $.ajax({
                        url: $(placeholder).attr('rel'),
                        type: "GET"
                    });
                    return false;
            }
                        
         </script>
         <?php
                  
            } else {
            $media_extension = "." .pathinfo(encrypt_decrypt($findmessagesrow["messagemedia"], "decrypt"), PATHINFO_EXTENSION);
            
            if($media_extension === ".png") {
                
            echo "<p id='" .$messageid ."' style='margin-left:20%; border: 1px solid black; text-align:justify; font-size:20px;'> <a style='color:#5c5c5c;' href='" .$view_media ."?id=" .$messageid ."&type=image&return=" .$group_page ."?groupid=" .$groupid ."@" .$messageid ."'>" .$view_media_notice ." »</a> <a href='javascript:;' onClick='like(this);' rel='deletemessage.php?id=" .$messageid ."&interlocutor=" .$interlocutor. "'><i class='fa fa-times' style='color:#cf1720; float:right;'></i></a> <br><span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span></p><br>";
            
              ?>
         <script>
            function like(placeholder) {
                    $.ajax({
                        url: $(placeholder).attr('rel'),
                        type: "GET"
                    });
                    return false;
            }
                        
         </script>
         <?php
            } else if($media_extension === ".mp4") {

            echo "<p id='" .$messageid ."' style='margin-left:20%; text-align:justify; border: 1px solid black; font-size:20px;'> <a style='color:#5c5c5c;' href='" .$view_media ."?id=" .$messageid ."&type=video&return=" .$group_page ."?groupid=" .$groupid ."@" .$messageid ."'>" .$view_media_notice ." »</a> <a href='javascript:;' onClick='like(this);' rel='deletemessage.php?id=" .$messageid ."&interlocutor=" .$interlocutor. "'><i class='fa fa-times' style='color:#cf1720; float:right;'></i></a> <br><span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span></p><br>";
            
              ?>
         <script>
            function like(placeholder) {
                    $.ajax({
                        url: $(placeholder).attr('rel'),
                        type: "GET"
                    });
                    return false;
            }
                        
         </script>
         <?php
            }
            
            }
            
            }
            
            } 
            
            } else {
                echo "<p style='text-align:center;'><mark>" .$no_messages_yet ."</mark></p>";
            }
            ?>
            </div>
      </div>
      <br>
   </body>
</html>