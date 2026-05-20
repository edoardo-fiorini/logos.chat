<?php

session_start();

require "configuration.php";

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];
$interlocutor = $_GET["interlocutor"];

if (isset($_GET["block"])) {
    $blocksql = "INSERT INTO Block (blockcode, blockowner) VALUES(?, ?)";
    $blockstatement = $database_connection->prepare($blocksql);
    $blockstatement->bind_param("ss", $_GET["block"], $membercode);
    $blockstatement->execute();
    $updateactivity = 1;
}

if (isset($_GET["unblock"])) {
    $unblocksql = "DELETE FROM Block WHERE blockowner = ? AND blockcode = ?";
    $unblockstatement = $database_connection->prepare($unblocksql);
    $unblockstatement->bind_param("ss", $membercode, $_GET["unblock"]);
    $unblockstatement->execute();
    $updateactivity = 1;
}

if ($interlocutor === null) {
    if (!($_GET["block"] === null)) {
        $interlocutor = $_GET["block"];
    }

    if (!($_GET["unblock"] === null)) {
        $interlocutor = $_GET["unblock"];
    }
}

$chaturl = $chat_page ."?interlocutor=" .$interlocutor;

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

$findusersql = "SELECT * FROM Member WHERE membercode = ?";
$finduserstatement = $database_connection->prepare($findusersql);
$finduserstatement->bind_param("s", $interlocutor);
$finduserstatement->execute();
$finduserresult = $finduserstatement->get_result();

while ($finduserrow = $finduserresult->fetch_assoc()) {
    $defaultcontactname = $finduserrow["membername"];
    $contactinfo = $finduserrow["memberinfo"];
    $currentcontactid = $finduserrow["memberid"];
}

$findblocksql = "SELECT * FROM Block WHERE blockcode = ? AND blockowner = ?";
$findblockstatement = $database_connection->prepare($findblocksql);
$findblockstatement->bind_param("ss", $interlocutor, $membercode);
$findblockstatement->execute();
$findblockresult = $findblockstatement->get_result();

if (mysqli_num_rows($findblockresult) > 0) {
    $blocked = true;
}

$findyourblocksql =
    "SELECT * FROM Block WHERE blockcode = ? AND blockowner = ?";
$findyourblockstatement = $database_connection->prepare($findyourblocksql);
$findyourblockstatement->bind_param("ss", $membercode, $interlocutor);
$findyourblockstatement->execute();
$findyourblockresult = $findyourblockstatement->get_result();

if (mysqli_num_rows($findyourblockresult) > 0) {
    $youblocked = true;
}

$setreadsql = "UPDATE Message SET isread = 1 WHERE messagesender = ?";
$setreadstatement = $database_connection->prepare($setreadsql);
$setreadstatement->bind_param("s", $interlocutor);
$setreadstatement->execute();
$updateactivity = 1;

$notsaved = true;

$findcontactsql =
    "SELECT * FROM Contact WHERE contactcode = ? AND contactowner = ?";
$findcontactstatement = $database_connection->prepare($findcontactsql);
$findcontactstatement->bind_param("ss", $interlocutor, $membercode);
$findcontactstatement->execute();
$findcontactresult = $findcontactstatement->get_result();

if (mysqli_num_rows($findcontactresult) > 0) {
    while ($findcontactrow = $findcontactresult->fetch_assoc()) {
        $contactname = encrypt_decrypt(
            $findcontactrow["contactname"],
            "decrypt"
        );

        if (!empty($contactname)) {
            $notsaved = false;
        }

        $lastcleared = $findcontactrow["lastcleared"];

    }
} else {
    $contactname = $defaultcontactname;
}

if(empty($lastcleared)) {
    $chaturl = $chat_page ."?interlocutor=" .$interlocutor;
    $findchatsql =
    "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
$findchatstatement = $database_connection->prepare($findchatsql);
$findchatstatement->bind_param("ss", $membercode, $chaturl);
$findchatstatement->execute();
$findchatresult = $findchatstatement->get_result();

if (mysqli_num_rows($findchatresult) > 0) {
    while ($findchatrow = $findchatresult->fetch_assoc()) {
        $lastcleared = $findchatrow["chatlastcleared"];
}

}

}

if (empty($lastcleared)) {
    $lastcleared = "1970-01-01 00:00:00";
}

if (
    file_exists("media/PFP/" . $currentcontactid . "-" . $interlocutor . ".png")
) {
    $PFP_path = "media/PFP/" . $currentcontactid . "-" . $interlocutor . ".png";
} else {
    $PFP_path = "media/PFP/default.png";
}

if ($youblocked) {
    $blockalert = $alert =
        $no_sending_first . $interlocutor . $no_sending_second;
}

if (isset($_POST["submit"]) && !empty($_POST["contactname"])) {
    $changecontactnamesql =
        "UPDATE Contact SET contactname = ? WHERE contactowner = ? AND contactcode = ?";
    $changecontactnamestatement = $database_connection->prepare(
        $changecontactnamesql
    );
    $changecontactnamestatement->bind_param(
        "sss",
        encrypt_decrypt($_POST["contactname"], "encrypt"),
        $membercode,
        $interlocutor
    );
    $changecontactnamestatement->execute();
    $updateactivity = 1;

    header("Location: " . $chat_page . "?interlocutor=" . $interlocutor);
}

if (isset($_GET["accept"])) {
    $acceptsql = "INSERT INTO Chataccepted (chatowner, chatcode) VALUES(?, ?)";
    $acceptstatement = $database_connection->prepare($acceptsql);
    $acceptstatement->bind_param("ss", $membercode, $interlocutor);
    $acceptstatement->execute();
    $updateactivity = 1;
    header("Location: " .$chat_page ."?interlocutor=" . $interlocutor);
}

if (isset($_GET["disaccept"])) {
    $acceptsql =
        "DELETE FROM Chataccepted WHERE chatowner = ? AND chatcode = ?";
    $acceptstatement = $database_connection->prepare($acceptsql);
    $acceptstatement->bind_param("ss", $membercode, $interlocutor);
    $acceptstatement->execute();
    header("Location: " .$chat_page ."?interlocutor=" . $interlocutor);
}

if (isset($_POST["clear"])) {
    $chaturl = $chat_page . "?interlocutor=" . $interlocutor;

    $clearchatsql =
        "UPDATE Chat SET chatlastcleared = now() WHERE chatowner = ? AND chaturl = ?";
    $clearchatstatement = $database_connection->prepare($clearchatsql);
    $clearchatstatement->bind_param("ss", $membercode, $chaturl);
    $clearchatstatement->execute();

    $clearchatsql =
        "UPDATE Contact SET lastcleared = now() WHERE contactowner = ? AND contactcode = ?";
    $clearchatstatement = $database_connection->prepare($clearchatsql);
    $clearchatstatement->bind_param("ss", $membercode, $interlocutor);
    $clearchatstatement->execute();
    
    $updateactivity = 1;
}

if (isset($_POST["deletechat"])) {
    $chaturl = $chat_page . "?interlocutor=" . $interlocutor;

    $deletechatsql =
        "DELETE FROM Chat WHERE chatowner = ? AND chaturl = ?";
    $deletechatstatement = $database_connection->prepare($deletechatsql);
    $deletechatstatement->bind_param("ss", $membercode, $chaturl);
    $deletechatstatement->execute();

    $deletechatsql =
        "DELETE FROM Contact WHERE contactowner = ? AND contactcode = ?";
    $deletechatstatement = $database_connection->prepare($deletechatsql);
    $deletechatstatement->bind_param("ss", $membercode, $interlocutor);
    $deletechatstatement->execute();
}

if (isset($_POST["delete"])) {
    $deletecontactnamesql =
        "DELETE FROM Contact WHERE contactowner = ? AND contactcode = ?";
    $deletecontactnamestatement = $database_connection->prepare(
        $deletecontactnamesql
    );
    $deletecontactnamestatement->bind_param("ss", $membercode, $interlocutor);
    $deletecontactnamestatement->execute();
}

$findchatsql =
    "SELECT * FROM Chataccepted WHERE chatowner = ? AND chatcode = ?";
$findchatstatement = $database_connection->prepare($findchatsql);
$findchatstatement->bind_param("ss", $membercode, $interlocutor);
$findchatstatement->execute();
$findchatresult = $findchatstatement->get_result();

if (mysqli_num_rows($findchatresult) > 0) {
    $allowchat = 1;
} else {
    $allowchat = 0;
}

if($_SESSION["memberprivate"] === 0) {
    $allowchat = 1;
}

if (
    mysqli_num_rows($finduserresult) === 0) {
    header("Location: " .$wrongsearch_page);
    exit();
} else if($interlocutor === $membercode) {
    header("Location: " .$home_page);
    exit();    
}

if (
    !isset($_SESSION["isloggedin"])) {
    header("Location: " .$index_page);
    exit();      
}

if($updateactivity = 1) {
      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
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

$webpage_name =
    $application_name .
    " - " .
    $chat_with .
    " " .
    $contactname .
    " (" .
    $interlocutor .
    ")";

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
            height: 100%; width: 100%;">
            <p style="margin-left: 10px"><?php echo $js_warning; ?></p>
        </div>
      </noscript>
      <div style="background-color:#ffffff;" class="jumbotron">
      <img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $home_page; ?>';" height="80px"><br>
   
         <div id="menubar" class="menubar" style="text-align:center; width: 100%; color:#ff5632;">
            <a href="<?php echo $home_page; ?>" style="color:#0210a8;"><?php echo $home ." " .$notificate ." "; ?> <img src="<?php echo $home_icon_path; ?>" width="20px;" height="20px;"> </a><span>|</span><a href="<?php echo $settings_page; ?>" style="color:#0210a8;"> <?php echo $settings; ?> <img src="<?php echo $settings_icon_path; ?>" width="20px;" height="20px;"> </a><span>|</span><a href="<?php echo $logout_page; ?>" style="color:#0210a8;"> <?php echo $logout; ?> <img src="<?php echo $exit_icon_path; ?>" width="20px;" height="20px;"></a><br>
          </div><br>
           
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
         <?php echo htmlspecialchars($contactname ." (" .$interlocutor .") "); ?><img src="<?php echo $PFP_path; ?>" width="35px;" height="35px;" style="border-radius:50%;">
         <p style="font-size:15px;"><?php echo htmlspecialchars($contactinfo); ?></p>
      </h1>
           <p style="font-size:15px; text-align:center;" id="openmodal" onclick="document.getElementById('myModal').style.display = 'block';"><span class="btn btn" role="button" aria-pressed="true" style="font-size:13px; float:right; background-color:#000000; color:#ffffff;"><?php echo $settings_and_other; ?></span></p><br><br>

        <div id="myModal" class="modal">
         <div class="modal-content">
               <span class="close" onclick="document.getElementById('myModal').style.display = 'none';">&times;</span><br>
            
                     
                     <form method="post" style="display:inline;">
                     
                     <p><span><?php echo $allow_notifications; ?> </span> 

                        <input type="checkbox" value="1" name="blocknotifications" id="blocknotifications" <?php if($notificationsblocked !== 1) { ?> checked="true" <?php } ?>><br><br>

                        <input type="submit" class="btn btn-primary btn-lg active" role="button" style="font-size:10px; margin-bottom:20px;" name="submitnots" value="<?php echo $confirm; ?>">
                        <hr>
                     </form>
                     
            
                  <h1 style="text-align:center; font-size:26px;"><?php echo $groups_in_common ?></h1>
      <?php                
         $nogroups = true;
         
         $findgroupssql = "SELECT * FROM Groupmember WHERE groupmembercode = ?";
         $findgroupsstatement = $database_connection->prepare($findgroupssql); 
         
         $findgroupsstatement->bind_param("s", $membercode);
         $findgroupsstatement->execute();
         $findgroupsresult = $findgroupsstatement->get_result();
            
         while ($findgroupsrow = $findgroupsresult->fetch_assoc()) {
            
            $groupid = $findgroupsrow["groupid"];
            
            $isinterlocutormembersql = "SELECT * FROM Groupmember WHERE groupmembercode = ? AND groupid = ?";
            $isinterlocutormemberstatement = $database_connection->prepare($isinterlocutormembersql); 
         
            $isinterlocutormemberstatement->bind_param("ss", $interlocutor, $groupid);
            $isinterlocutormemberstatement->execute();
            $isinterlocutormemberresult = $isinterlocutormemberstatement->get_result();
            
            if(mysqli_num_rows($isinterlocutormemberresult) > 0) {
            $nogroups = false;
         
            $groupid = $findgroupsrow["groupid"];
            
            $findgroupsql = "SELECT * FROM Grouptable WHERE groupid = ?";
            $findgroupstatement = $database_connection->prepare($findgroupsql); 
            $findgroupstatement->bind_param("s", $groupid);
            $findgroupstatement->execute();
            $findgroupresult = $findgroupstatement->get_result();
            
            while ($findgrouprow = $findgroupresult->fetch_assoc()) {
                
                $groupname = encrypt_decrypt($findgrouprow["groupname"], "decrypt");
                $groupcreation = $findgrouprow["groupcreation"];

                if(file_exists("media/group_PFP/" .$groupid ."-" .$groupcreation .".png")) {
                    $group_PFP = "media/group_PFP/" .$groupid ."-" .$groupcreation .".png";
                } else {
                    $group_PFP = "media/group_PFP/default.png";
                }
         
               echo "<p style='font-size:15px; text-a lign:center;'><a style='color:#000000;' href='" .$group_page ."?groupid=" .$groupid ."'><img style='border-radius:50%;' src='" .$group_PFP ."' width='35px;' height='35px;'> " .htmlspecialchars($groupname) ."</p></a>";
                
                }
             }
         }
         
         if($nogroups) {
             echo "<p style='text-align:center;'><mark>" .$no_groups_in_common ."</mark></p>";
         }
            
            ?>
      <h1 style="text-align:center; font-size:26px;"><?php echo $contact_settings; ?></h1>
      <div id="settingsframe" style="text-align:center;">
         
               <form method="post">
           <div style="float:left;">
                
            <?php if(!$notsaved) { ?>
            <?php echo $modify_contact_name; ?> <input type="text" required="true" value="<?php echo htmlspecialchars($contactname); ?>" name="contactname"><br><br>
            
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="submit" value="<?php echo $confirm_edits; ?>"> <br><br> <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="delete" value="<?php echo $delete_contact; ?>"> <br><br> <?php } ?> <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="clear" value="<?php echo $clear_chat; ?>"><br><br><input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="deletechat" value="<?php echo $delete_chat; ?>">
          </div>
         </form>
        </div>
         
       <br><?php if(!$blocked) {
            echo "<br><br><p style='text-align:right; font-size:25px;'><a href='" .$chat_page ."?block=" .$interlocutor ."'>" .$block ." </a></p>";
            }
            
            if($allowchat === 1 && $_SESSION["memberprivate"] === 1) { ?>
         <p style="text-align:center; font-size:25px;"><a style="color:#ff1100;" href="<?php echo $chat_page; ?>?disaccept=<?php echo $interlocutor; ?>&interlocutor=<?php echo $interlocutor; ?>"><?php echo $disaccept_chat; ?></a></p>
         <?php } ?>
         
        </div>
        </div>

      <?php
         if($notsaved && !$blocked) {
             echo "<p style='text-align:center;'><mark>" .htmlspecialchars($interlocutor) ." " .$not_saved ." <a href='" .$chat_page ."?block=" .$interlocutor ."'>" .$block ." </a> " .$or ." <a href='" .$home_page ."?contactcode=" .htmlspecialchars($interlocutor) ."'>" .$save ." </a> </mark></p>";
         } 
         
         if($blocked) {
             echo "<p style='text-align:center;'><mark>" .$blocked_notice ." <a href='" .$chat_page ."?unblock=" .htmlspecialchars($interlocutor) ."'>" .$unblock ."</a></mark></p>";
         }
         
         if(!empty($blockalert)) {
             echo "<p style='text-align:center;'><mark>" .$blockalert ."</mark></p>";
         }
         ?>
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
        
         <textarea rows="1" autofocus="true" class="form-control" form="newmessage" name="messagetext" id="messagetext"></textarea>
         <br>
         <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="submit" id="submit" value="<?php echo $send; ?>">
         <input type="file" style="display:none;" accept="image/png, image/gif, image/jpeg, image/jpg" id="image" name="image">
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
         <span style="color:#3761d4;"><i style="font-size:25px;" class="fas fa-image"></i></span>
         </label>
         
         <label for="video">
         <span style="color:#3761d4;"><i style="font-size:25px;" class="fas fa-video"></i></span>
         </label>
         
      <div id="checkicon" style="display:none;" name="checkicon"><?php echo $media_selected; ?> <i style='color:#15b004; display:none; font-size:30px;' id="checkicon" name="checkicon" class='fa fa-check'></i></div>
      </form>
      <script>
         $("#newmessage").on("submit", function(ev) {
           ev.preventDefault(); 
         
           var formData = new FormData(this);
           formData.append("il", "<?php echo encrypt_decrypt($interlocutor, "encrypt"); ?>");
         
           $.ajax({
             url: "<?php echo $newprivatemessage_page; ?>",
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
      <p onclick="showhide()" style="color:#0d05ab; font-size:15px; text-align:center;"><?php echo $show_hide; ?></p>
      <?php if($allowchat === 0) { ?>
      <p style="text-align:center;"><a href="<?php echo $chat_page; ?>?accept=<?php echo $interlocutor; ?>&interlocutor=<?php echo $interlocutor; ?>"><?php echo $accept_chat; ?></a></p>
      <?php } ?>

      <?php if($allowchat === 1) { ?>
      <div id="messagesframe" class="noselect" style="text-align:center;">
        
         <?php
            $findmessagessql = "SELECT * FROM Message WHERE ((messagerecipient = ? AND messagesender = ?) OR (messagerecipient = ? AND messagesender = ?)) AND messagecreation > ? ORDER BY messagecreation DESC";
            $findmessagesstatement = $database_connection->prepare($findmessagessql);
            $findmessagesstatement->bind_param("sssss", $membercode, $interlocutor, $interlocutor, $membercode, $lastcleared);
            
            $findmessagesstatement->execute();
            $findmessagessresult = $findmessagesstatement->get_result();
            
            if(mysqli_num_rows($findmessagessresult) > 0) {
            
            while ($findmessagesrow = $findmessagessresult->fetch_assoc()) {
                  
                  if($findmessagesrow["isread"] == 1) {
                     $read = $read_message;
                  } else {
                     $read = $not_read;
                  }
               
                 $messageid = $findmessagesrow["messageid"];
                 $messagetext = $findmessagesrow["messagetext"];
                 $messagecreation = date_create($findmessagesrow["messagecreation"]);
                 $messagecreationshort = date_format($messagecreation, "H:i");
                 $messagecreationlong = date_format($messagecreation, "Y/m/d");
            
                 if($findmessagesrow["messagesender"] !== $_SESSION["membercode"]) {
               
                 if(!empty($findmessagesrow["messagetext"]) && empty($findmessagesrow["messagemedia"])) {
            
                 echo "<p id='" .$messageid ."' style='border: 1px solid black; margin-right:20%; text-align:left; font-size:20px; background-color:#e3e1b3; border: 1px solid black;'> " .add_links(htmlspecialchars(encrypt_decrypt($messagetext, "decrypt")), "_blank") ."<br> <span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span> </p><br>";
                 } else {
                     
                 $media_extension = "." .pathinfo(encrypt_decrypt($findmessagesrow["messagemedia"], "decrypt"), PATHINFO_EXTENSION);
                 
                 if($media_extension === ".png") {
                
                 echo "<p id='" .$messageid ."' style='margin-right:20%; text-align:justify; background-color:#e3e1b3; font-size:20px; border: 1px solid black;'><a style='color:#5c5c5c;' href='" .$view_media ."?id=" .$messageid ."&type=image&return=" .$chat_page ."?interlocutor=" .$interlocutor ."@" .$messageid ."'>" .$view_media_notice ." »</a><br><span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span> </p><br>";
                 } else if($media_extension === ".mp4") {
                
                 echo "<p id='" .$messageid ."' style='margin-right:20%; text-align:justify; border: 1px solid black; background-color:#e3e1b3; font-size:20px;'><a style='color:#5c5c5c;' href='" .$view_media ."?id=" .$messageid ."&type=video&return=" .$chat_page ."?interlocutor=" .$interlocutor ."@" .$messageid ."'>" .$view_media_notice ." »</a> <span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span> </p><br>";
                 }
                 
                 }
               } else {
            
            if(!empty($findmessagesrow["messagetext"]) && empty($findmessagesrow["messagemedia"])) {
            
            echo "<p id='" .$messageid ."' style='margin-left:20%; text-align:justify; border: 1px solid black; font-size:20px;'>" .add_links(htmlspecialchars(encrypt_decrypt($findmessagesrow["messagetext"], "decrypt")), "_blank") ." <a href='javascript:;' onClick='like(this);' rel='" .$deletemessage_page ."?id=" .$messageid ."&interlocutor=" .$interlocutor. "'><i class='fa fa-times' style='color:#cf1720; float:right;'></i></a><br><span style='color:#6e6e6e;'>" .$read ." <small>" .$messagecreationshort ." " .$messagecreationlong ."</small></span></p><br>";
            
                        ?>
         <script>
            function like(placeholder) {
                    $.ajax({
                        url: $(placeholder).attr("rel"),
                        type: "GET"
                    });
                    return false;
            }
                        
         </script>
         <?php
            } else {
            $media_extension = "." .pathinfo(encrypt_decrypt($findmessagesrow["messagemedia"], "decrypt"), PATHINFO_EXTENSION);
            
            if($media_extension === ".png") {
            
            echo "<p id='" .$messageid ."' style='margin-left:20%; text-align:justify; font-size:20px; border: 1px solid black;'> <a style='color:#5c5c5c;' href='" .$view_media ."?id=" .$messageid ."&type=image&return=" .$chat_page ."?interlocutor=" .$interlocutor ."@" .$messageid ."'>" .$view_media_notice ." »</a> <span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ." " .$read ."</small></span> <a href='javascript:;' onClick='like(this);' rel='" .$deletemessage_page ."?id=" .$messageid ."&interlocutor=" .$interlocutor. "'><i class='fa fa-times' style='color:#cf1720; float:right;'></i></a></p><br>";
            
                        
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
            
            echo "<p id='" .$messageid ."' style='margin-left:20%; text-align:justify; font-size:20px; border: 1px solid black;'> <a style='color:#5c5c5c;' href='" .$view_media ."?id=" .$messageid ."&type=video&return=" .$chat_page ."?interlocutor=" .$interlocutor ."@" .$messageid ."'>" .$view_media_notice ." »</a><br><span style='color:#6e6e6e;'><small>" .$messagecreationshort ." " .$messagecreationlong ."</small> " .$read ."</span>  <a href='javascript:;' onClick='like(this);' rel='" .$deletemessage_page ."?id=" .$messageid ."&interlocutor=" .$interlocutor. "'><i class='fa fa-times' style='color:#cf1720;'></i></a></p><br>";
            
                        
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
      <?php } ?>
     </div>
   </body>
</html>