<?php
   session_start();
   
   require "configuration.php";
   
   if (!isset($_SESSION["isloggedin"])) {
      header("Location: " . $access_page);
      exit();
   }
   
   $member = $_SESSION["memberid"];
   $membercode = $_SESSION["membercode"];
   
   if (isset($_POST["create"])) {
       if (!empty($_POST["contactcode"]) && !empty($_POST["contactname"])) {
           $contactcode = $_POST["contactcode"];
           $contactname = encrypt_decrypt($_POST["contactname"], "encrypt");
   
           $findusersql = "SELECT * FROM Member WHERE membercode = ?";
           $finduserstatement = $database_connection->prepare($findusersql);
           $finduserstatement->bind_param("s", $contactcode);
           $finduserstatement->execute();
           $finduserresult = $finduserstatement->get_result();
   
           if (mysqli_num_rows($finduserresult) > 0) {
               $findusersql =
                   "SELECT * FROM Contact WHERE (contactowner = ? AND contactcode = ?) OR (contactname = ? AND contactowner = ?)";
               $finduserstatement = $database_connection->prepare($findusersql);
               $finduserstatement->bind_param(
                   "ssss",
                   $membercode,
                   $contactcode,
                   $contactname,
                   $member
               );
               $finduserstatement->execute();
               $finduserresult = $finduserstatement->get_result();
   
               if (mysqli_num_rows($finduserresult) === 0) {
                   if ($contactcode !== $membercode) {
                       $newmessagesql =
                           "INSERT INTO Contact(contactcode, contactname, contactowner) VALUES(?, ?, ?)";
                       $newmessagestatement = $database_connection->prepare(
                           $newmessagesql
                       );
                       $newmessagestatement->bind_param(
                           "sss",
                           $contactcode,
                           $contactname,
                           $membercode
                       );
   
                       if ($newmessagestatement->execute()) {
                           $chaturl = $chat_page . "?interlocutor=" . $contactcode;
   
                           $findchatsql =
                               "SELECT * FROM Chat WHERE chatowner = ? AND chaturl = ?";
                           $findchatstatement = $database_connection->prepare(
                               $findchatsql
                           );
                           $findchatstatement->bind_param(
                               "ss",
                               $membercode,
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
                                   $membercode,
                                   $chaturl
                               );
   
                               $newchatstatement->execute();
                           }
                       }
                   }
               }
           }
           header("Refresh:0;");
       }
   }
   
   if (isset($_POST["creategroup"])) {
       if (!empty($_POST["groupname"])) {
           $groupname = encrypt_decrypt($_POST["groupname"], "encrypt");
   
           $newgroupsql = "INSERT INTO Grouptable (groupname) VALUES(?)";
           $newgroupstatement = $database_connection->prepare($newgroupsql);
           $newgroupstatement->bind_param("s", $groupname);
   
           if ($newgroupstatement->execute()) {
               $findgroupsql =
                   "SELECT * FROM Grouptable ORDER BY groupcreation DESC LIMIT 1";
               $findgroupstatement = $database_connection->prepare($findgroupsql);
               $findgroupstatement->execute();
               $findgroupresult = $findgroupstatement->get_result();
   
               while ($findgrouprow = $findgroupresult->fetch_assoc()) {
                   $groupid = $findgrouprow["groupid"];
   
                   $groupadmin = "1";
                   $groupowner = "1";
   
                   $newmembersql =
                       "INSERT INTO Groupmember (groupmembercode, groupid, groupmemberadmin, groupmemberowner) VALUES(?, ?, ?, ?)";
                   $newmemberstatement = $database_connection->prepare(
                       $newmembersql
                   );
   
                   $newmemberstatement->bind_param(
                       "ssss",
                       $membercode,
                       $groupid,
                       $groupadmin,
                       $groupowner
                   );
   
                   if ($newmemberstatement->execute()) {
                       $findidsql =
                           "SELECT * FROM Grouptable ORDER BY groupcreation DESC LIMIT 1";
                       $findidstatement = $database_connection->prepare(
                           $findidsql
                       );
                       $findidstatement->execute();
                       $findidresult = $findidstatement->get_result();
   
                       while ($findidrow = $findidresult->fetch_assoc()) {
                           $groupid = $findidrow["groupid"];
                       }
   
                       $chaturl = $group_page . "?groupid=" . $groupid;
   
                       $newchatsql =
                           "INSERT INTO Chat (chatowner, chaturl) VALUES(?, ?)";
                       $newchatstatement = $database_connection->prepare(
                           $newchatsql
                       );
                       $newchatstatement->bind_param("ss", $membercode, $chaturl);
                       $newchatstatement->execute();
   
                   }
               }
           }
           header("Refresh:0;");
       }
   }
   
   if(isset($_POST["submitnots"])) {
   
         $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();
      
   if($_POST["allownotifications"] == 1) {
       
   $allowsql = "UPDATE Member SET allownotifications = 1 WHERE membercode = ?";
   $allowstatement = $database_connection->prepare($allowsql);
   $allowstatement->bind_param("s", $membercode);
   $allowstatement->execute();
   
   $_SESSION["allownotifications"] = 1;
   
   } else {
   
   $disallowsql = "UPDATE Member SET allownotifications = 0 WHERE membercode = ?";
   $disallowstatement = $database_connection->prepare($disallowsql);
   $disallowstatement->bind_param("s", $membercode);
   $disallowstatement->execute();
   
   $_SESSION["allownotifications"] = 0;    
   
   }
   
   if($_POST["allowprivatenotifications"] == 1) {
       
   $allowsql = "UPDATE Member SET allowprivatenotifications = 1 WHERE membercode = ?";
   $allowstatement = $database_connection->prepare($allowsql);
   $allowstatement->bind_param("s", $membercode);
   $allowstatement->execute();
   
   $_SESSION["allowprivatenotifications"] = 1;
   
   } else {
   
   $disallowsql = "UPDATE Member SET allowprivatenotifications = 0 WHERE membercode = ?";
   $disallowstatement = $database_connection->prepare($disallowsql);
   $disallowstatement->bind_param("s", $membercode);
   $disallowstatement->execute();
   
   $_SESSION["allowprivatenotifications"] = 0;    
   
   }
   
   if($_POST["allowgroupnotifications"] == 1) {
       
   $allowsql = "UPDATE Member SET allowgroupnotifications = 1 WHERE membercode = ?";
   $allowstatement = $database_connection->prepare($allowsql);
   $allowstatement->bind_param("s", $membercode);
   $allowstatement->execute();
   
   $_SESSION["allowgroupnotifications"] = 1;
   
   } else {
   
   $disallowsql = "UPDATE Member SET allowgroupnotifications = 0 WHERE membercode = ?";
   $disallowstatement = $database_connection->prepare($disallowsql);
   $disallowstatement->bind_param("s", $membercode);
   $disallowstatement->execute();
   
   $_SESSION["allowgroupnotifications"] = 0;    
   
   }
   
   }
   
   $webpage_name = $application_name;
   
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
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <link rel="icon" type="image/ico" href="<?php echo $favicon_path; ?>">
      <style>
         input[type='checkbox'] {
         -webkit-appearance: none;
         -moz-appearance: none;
         appearance: none;
         outline: none;
         position: relative;
         width: 2rem;
         height: 2rem;
         border: 2px solid #455A64;
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

            <b><a href="<?php echo $home_page; ?>" style="color:#0210a8;">
                <?php echo $home ." "; ?> <img src="<?php echo $home_icon_path; ?>" width="20px;" height="20px;"> </a></b><span>|</span><a href="<?php echo $settings_page; ?>" style="color:#0210a8;"> <?php echo $settings; ?> <img src="<?php echo $settings_icon_path; ?>" width="20px;" height="20px;"> </a><span>|</span><a href="<?php echo $logout_page; ?>" style="color:#0210a8;"> <?php echo $logout; ?> <img src="<?php echo $exit_icon_path; ?>" width="20px;" height="20px;"></a><?php echo $notificate; ?><br>
         </div>
   
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
   
         <br>
         <p style="text-align:left; font-size:20px;" class="lead"><?php echo htmlspecialchars($membercode); ?> <img style="border-radius:50%;" width="35px;" height="35px;" src="<?php if(file_exists("media/PFP/" .$member ."-" .$membercode .".png")) {
            echo "media/PFP/" .$member ."-" .$membercode .".png"; 
            } else {
            echo "media/PFP/default.png"; 
            }
            ?>"></p>
         <script>
            $(document).ready(function() {
            var pageRefresh = 3000;
                setInterval(function() {
                    refresh();
                }, pageRefresh);
            });
            
            function refresh() {
                $("#chatsframe").load(" #chatsframe > *");
                $("#menubar").load(" #menubar > *");
            }
         </script>
         <script>
            function showChatHint(query) {
              if (query.length == 0) {
                document.getElementById("chatsuggestions").innerHTML = "";
                return;
              } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                  if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("chatsuggestions").innerHTML = this.responseText;
                  }
                }
                xmlhttp.open("GET", "<?php echo $chathint_page; ?>?search=" + query, true);
                xmlhttp.send();
              }
            }
         </script>
         <form action="<?php echo $chat_page; ?>" style="text-align:center;">
            <input type="text" required="true" name="interlocutor" id="interlocutor" style="width:100%;" placeholder="<?php echo $search_chat; ?>" onkeyup="showChatHint(this.value)">
            <br>
            <p style="text-align:center;"><span id="chatsuggestions"></span></p>
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; float:center; font-size:13px;" name="search" id="search" value="<?php echo $chat; ?>">
         </form>
         <br>
         <script>
            function showhidecontacts() {
            
              document.getElementById("groups").style.display = "none";
              document.getElementById("notifications").style.display = "none";
              
              var x = document.getElementById("contacts");
              
              x.style.display = "block";
            }
            
            function showhidegroups() {
            
              document.getElementById("contacts").style.display = "none";
              document.getElementById("notifications").style.display = "none";
              
              var x = document.getElementById("groups");
              
              x.style.display = "block";
            }
            
            function showhidenotifications() {
            
              document.getElementById("groups").style.display = "none";
              document.getElementById("contacts").style.display = "none";
              
              var x = document.getElementById("notifications");
              
              x.style.display = "block";
            }
         </script>
         <p style="color:#0d05ab; font-size:15px; text-align:center;" id="openmodal"><span onclick="showhidecontacts()" class="btn btn-primary btn-lg active" role="button" aria-pressed="true" style="font-size:11px; float:left;"><?php echo $contacts; ?></span>  
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px; margin-bottom:20px;" role="button" style="font-size:13px; margin-bottom:20px;" name="submitnots" onclick="showhidenotifications()" value="<?php echo $manage_notifications; ?>">
            <span class="btn btn-primary btn-lg active" role="button" aria-pressed="true" style="font-size:11px; float:right;" onclick="showhidegroups()"><?php echo $groups; ?></span>
         </p>
         <div id="myModal" class="modal">
            <div class="modal-content">
               <span class="close">&times;</span><br>
               <div id="notifications" style="display:none;">
                  <form method="post" style="display:inline;">
                      <span style="font-size:15px;"><i><?php echo $notifications_paragraph; ?></i></span><br><br>

                     <p><span><?php echo $send_notifications_to; ?> <?php echo $_SESSION["memberemail"]; ?></span> <br>
                        <input type="checkbox" value="1" name="allownotifications" id="allownotifications" <?php if($_SESSION["allownotifications"] === 1) { ?> checked="true" <?php } ?>>
                     <p><span><?php echo $send_notifications_private; ?></span> <br>
                        <input type="checkbox" value="1" name="allowprivatenotifications" id="allowprivatenotifications" <?php if($_SESSION["allowprivatenotifications"] === 1) { ?> checked="true" <?php } ?>>
                     <p><span><?php echo $send_notifications_group; ?></span> <br>
                        <input type="checkbox" value="1" name="allowgroupnotifications" id="allowgroupnotifications" <?php if($_SESSION["allowgroupnotifications"] === 1) { ?> checked="true" <?php } ?>><br><br>
                     <input type="submit" class="btn btn-primary btn-lg active" role="button" style="font-size:10px; margin-bottom:20px;" name="submitnots" value="<?php echo $confirm; ?>">

                  </form>
                  </p>
               </div>
               <div id="contacts" style="display:none;">
                  <div>
                     <h1 style="text-align:center; font-size:25px;"><?php echo $add_contact; ?></h1>
                     <br>
                     <?php
                        if(isset($_GET["contactcode"])) {
                            ?>
                     <script>
                        var modal = document.getElementById("myModal");
                        modal.style.display = "block";
                        var contacts = document.getElementById('contacts');
                        
                        contacts.style.display = "block";
                        document.getElementsByName("contactcode")[0].placeholder="<?php echo htmlspecialchars($_GET["contactcode"]); ?>";
                        
                        
                     </script>
                     <?php
                        }
                        
                        ?>
                     <form method="post" style="text-align:center;">
                        <input type="text" placeholder="<?php echo $enter_contact_code; ?>" value="<?php echo htmlspecialchars($_GET["contactcode"]); ?>" required="true" maxlength="20" name="contactcode"><br><br>
                        <input type="text" placeholder="<?php echo $enter_contact_name; ?>" required="true" name="contactname"><br><br>
                        <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="create" value="<?php echo $add_contact; ?>">
                     </form>
                     <br>
                     <?php
                        $findchatssql = "SELECT * FROM Contact WHERE contactowner = ? ORDER BY contactcreation DESC";
                        $findchatsstatement = $database_connection->prepare($findchatssql); 
                        $findchatsstatement->bind_param("s", $membercode);
                        $findchatsstatement->execute();
                        $findchatsresult = $findchatsstatement->get_result();
                        
                        while ($findchatsrow = $findchatsresult->fetch_assoc()) {
                        
                           $contactcode = $findchatsrow["contactcode"];
                           $contactname = encrypt_decrypt($findchatsrow["contactname"], "decrypt");
                        
                        $findidsql = "SELECT * FROM Member WHERE membercode = ?";
                        $findidstatement = $database_connection->prepare($findidsql); 
                        $findidstatement->bind_param("s", $contactcode);
                        
                        $findidstatement->execute();
                        $findidresult = $findidstatement->get_result();
                              
                        while ($findidrow = $findidresult->fetch_assoc()) {
                        $id = $findidrow["memberid"];
                        }
                        
                           if(file_exists("media/PFP/" .$id ."-" .$contactcode .".png")) {
                               $PFP_path = "media/PFP/" .$id ."-" .$contactcode .".png";
                           } else {
                               $PFP_path = "media/PFP/default.png";
                           }
                           
                           echo "<p style='font-size:20px; text-align:left;'><img src='" .$PFP_path ."' width='35px;' height='35px;' style='border-radius:50%;'> <a style='color:#000000;' href='" .$chat_page ."?interlocutor=" .htmlspecialchars($contactcode) ."'>" .htmlspecialchars($contactname ." (" .$contactcode .")") ."</p></a>";
                        
                        }
                        ?>
                     <h1 style="text-align:center; font-size:25px;"><?php echo $search_contact; ?></h1>
                     <script>
                        function showHint(query) {
                          if (query.length == 0) {
                            document.getElementById("suggestions").innerHTML = "";
                            return;
                          } else {
                            var xmlhttp = new XMLHttpRequest();
                            xmlhttp.onreadystatechange = function() {
                              if (this.readyState == 4 && this.status == 200) {
                                document.getElementById("suggestions").innerHTML = this.responseText;
                              }
                            }
                            xmlhttp.open("GET", "<?php echo $contacthint_page; ?>?search=" + query, true);
                            xmlhttp.send();
                          }
                        }
                     </script>
                     <form style="text-align:center;">
                        <br><input type="text" id="contactsearch" placeholder="<?php echo $enter_contact_name; ?>" name="contactsearch" onkeyup="showHint(this.value)">
                     </form>
                     <p style="text-align:center;"><span id="suggestions"></span></p>
                  </div>
                  <br>
               </div>
               <div id="groups" style="display:none;">
                  <div style="display:block; text-align:center;">
                     <h1 style="text-align:center; font-size:25px;"><?php echo $add_group; ?></h1>
                     <br>
                     <form method="post">
                        <input type="text" placeholder="<?php echo $enter_new_group_name; ?>" name="groupname" required="true"><br><br>
                        <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="creategroup" value="<?php echo $new_group; ?>">
                     </form>
                     
                     <br><br>
                     <?php
                        $findgroupssql = "SELECT * FROM Groupmember WHERE groupmembercode = ?";
                        $findgroupsstatement = $database_connection->prepare($findgroupssql); 
                        
                        $findgroupsstatement->bind_param("s", $membercode);
                        $findgroupsstatement->execute();
                        $findgroupsresult = $findgroupsstatement->get_result();
                        
                        while ($findgroupsrow = $findgroupsresult->fetch_assoc()) {
                        
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
                               $PFP_path = "media/group_PFP/" .$groupid ."-" .$groupcreation .".png";
                           } else {
                               $PFP_path = "media/group_PFP/default.png";
                           }
                        
                           echo "<p style='font-size:20px; text-align:left;'><img src='" .$PFP_path ."' width='35px;' height='35px;' style='border-radius:50%;'> <a style='color:#000000;' href='" .$group_page ."?groupid=" .$groupid ."'>" .htmlspecialchars($groupname) ."</p></a>";
                            
                            }
                        }
                        ?>
                     <h1 style="text-align:center; font-size:25px;"><?php echo $search_group; ?></h1>
                     <br>
                     <script>
                        function showGroupHint(query) {
                          if (query.length == 0) {
                            document.getElementById("groupsuggestions").innerHTML = "";
                            return;
                          } else {
                            var xmlhttp = new XMLHttpRequest();
                            xmlhttp.onreadystatechange = function() {
                              if (this.readyState == 4 && this.status == 200) {
                                document.getElementById("groupsuggestions").innerHTML = this.responseText;
                              }
                            }
                            xmlhttp.open("GET", "<?php echo $grouphint_page; ?>?search=" + query, true);
                            xmlhttp.send();
                          }
                        }
                     </script>
                     <form>
                        <input type="text" id="groupsearch" placeholder="<?php echo $enter_new_group_name; ?>" name="groupsearch" onkeyup="showGroupHint(this.value)">
                     </form>
                     <p><span id="groupsuggestions"></span></p>
                  </div>
                  <br>
               </div>
            </div>
         </div>
         <script>
            var modal = document.getElementById("myModal");
            
            var btn = document.getElementById("openmodal");
            
            var span = document.getElementsByClassName("close")[0];
            
            btn.onclick = function() {
              modal.style.display = "block";
            }
            
            span.onclick = function() {
              modal.style.display = "none";
            }
            
            window.onclick = function(event) {
              if (event.target == modal) {
                modal.style.display = "none";
              }
            }
         </script>
         <div id="chatsframe" style="display:block;" class="card p-3 bg-light">
            <?php
               $findchatssql = "SELECT * FROM Chat WHERE chatowner = ? ORDER BY chatlastmessage DESC";
               
               $findchatsstatement = $database_connection->prepare($findchatssql);
               $findchatsstatement->bind_param("s", $membercode);
               $findchatsstatement->execute();
               $findchatssresult = $findchatsstatement->get_result();
               
               if(mysqli_num_rows($findchatssresult) > 0) {
               
               while ($findchatssrow = $findchatssresult->fetch_assoc()) {
                   
                   $chaturl = $findchatssrow["chaturl"];
                   $chatlastmessage = $findchatssrow["chatlastmessage"];
                   $chatlastcleared = $findchatssrow["chatlastcleared"];
                   $privatechatstarting = $chat_page ."?interlocutor=";
                   $groupchatstarting = $group_page ."?groupid=";
                   
                   if(is_null($chatlastcleared)) {
                       $chatlastcleared = "1970-01-01 00:00:00";
                   }
                   
                   if(strpos($chaturl, $privatechatstarting) === 0) {
                       $chattype = "privatechat";
                   } else if(strpos($chaturl, $groupchatstarting) === 0) {
                       $chattype = "groupchat";
                   }
                   
                   if($chatlastcleared === null) {
                       $chatlastcleared = "1970-01-01 00:00:00";
                   }
               
                   $findmessagesql = "SELECT * FROM Message WHERE messageid = ? AND messagecreation > ? LIMIT 1";
                   $findmessagestatement = $database_connection->prepare($findmessagesql);
                   $findmessagestatement->bind_param("ss", $chatlastmessage, $chatlastcleared);
               
                   $findmessagestatement->execute();
                   $findmessageresult = $findmessagestatement->get_result();
               
                   if(mysqli_num_rows($findmessageresult) > 0) {
               
                   while ($findmessagerow = $findmessageresult->fetch_assoc()) {
                       $no_message = false;
                       $messagetext = encrypt_decrypt($findmessagerow["messagetext"], "decrypt");
                       $messagecreation = $findmessagerow["messagecreation"];
                       $messagemedia = encrypt_decrypt($findmessagerow["messagemedia"], "decrypt");
                       $isread = $findmessagerow["isread"];
                       $messagesender = $findmessagerow["messagesender"];
                   }
                   
                   } else {
                       $no_message = true;
                   }
               
                   if($chattype === "privatechat") {
                       $interlocutor = str_replace($chat_page ."?interlocutor=", "", $chaturl);
               
                       $findcontactsql = "SELECT * FROM Contact WHERE contactowner = ? AND contactcode = ?";
                       $findcontactstatement = $database_connection->prepare($findcontactsql);
                       $findcontactstatement->bind_param("ss", $membercode, $interlocutor);
                       $findcontactstatement->execute();
                       $findcontactresult = $findcontactstatement->get_result();
               
                       if(mysqli_num_rows($findcontactresult) > 0) {
                       while ($findcontactrow = $findcontactresult->fetch_assoc()) {
                           $contactname = encrypt_decrypt($findcontactrow["contactname"], "decrypt");
                        
                           $findmembersql = "SELECT * FROM Member WHERE membercode = ?";
                           $findmemberstatement = $database_connection->prepare($findmembersql);
                           $findmemberstatement->bind_param("s", $interlocutor);
                           $findmemberstatement->execute();
                           $findmemberresult = $findmemberstatement->get_result();
                           
                           while ($findmemberrow = $findmemberresult->fetch_assoc()) {
                               
                               $contactid = $findmemberrow["memberid"];
                           }                           
                           
                       }
                       
                       } else {
                           $findmembersql = "SELECT * FROM Member WHERE membercode = ?";
                           $findmemberstatement = $database_connection->prepare($findmembersql);
                           $findmemberstatement->bind_param("s", $interlocutor);
                           $findmemberstatement->execute();
                           $findmemberresult = $findmemberstatement->get_result();
                           
                           while ($findmemberrow = $findmemberresult->fetch_assoc()) {
                               
                               $contactname = $findmemberrow["membername"];
                               $contactid = $findmemberrow["memberid"];
                           }
                       
                       }
                       
                       if(file_exists("media/PFP/" .$contactid ."-" .$interlocutor .".png")) {
                              $PFP_path = "media/PFP/" .$contactid ."-" .$interlocutor .".png";
                       } else {
                              $PFP_path = "media/PFP/default.png";
                       }
                       
                       $mediatype = substr($messagemedia, strpos($messagemedia, ".") + 1);
                       
                       if(empty($messagetext) && !empty($messagemedia)) {
               
                           if($mediatype === "png") {
                           
                           if($messagesender === $membercode) {
                           $preview = " <b>" .$you ."</b>: <i style='font-size:25px;' class='fas fa-camera'></i>";
                           } else {
                           $preview = ": <i style='font-size:25px;' class='fas fa-camera'></i>";    
                           }
                           
                           } else if($mediatype === "mp4") {
                               
                           if($messagesender === $membercode) {
                           $preview = " <b>" .$you ."</b>: <i style='font-size:25px;' class='fas fa-video'></i>";    
                           } else {
                           $preview = ": <i style='font-size:25px;' class='fas fa-video'></i>";        
                           }
                           }
                       } else if(!empty($messagetext) && empty($messagemedia)) {
                           
                           if($messagesender === $membercode) {
                               
                           if(strlen($messagetext) > 15) {
                           $previewdots = "...";
                           } else {
                           $previewdots = "";
                           }
                           
                           $preview = " <b>" .$you ."</b>: " .substr($messagetext, 0, 15) .$previewdots;
                           } else {
                           
                           if(strlen($messagetext) > 15) {
                           $previewdots = "...";
                           } else {
                           $previewdots = "";
                           }
                        
                           $preview = ": " .substr($messagetext, 0, 15) .$previewdots;    
                           }
                       } 
                       
                       if($isread === 0 && $messagesender !== $membercode) {
                           $isread = "<i style='float:right; color:#ff5632;' class='fa fa-circle'></i>";
                       } else {
                           $isread = "";
                       }
                       
                       if($no_message) {
                           $preview = "";
                       }
                       
                       echo "<a style='text-decoration:none; color:#000000;' href='" .$chaturl ."'><p><img src='" .$PFP_path ."' width='35px;' height='35px' style='border-radius:50%;'> <span style='font-size:20px;'>" .htmlspecialchars($contactname) ."</span>" .$preview ." " .$isread ." </p></a>";
                       
                   } else if($chattype === "groupchat") {
                       $groupid = substr($chaturl, -1);
               
                       $findgroupsql = "SELECT * FROM Grouptable WHERE groupid = ?";
                       $findgroupstatement = $database_connection->prepare($findgroupsql);
                       $findgroupstatement->bind_param("s", $groupid);
                       $findgroupstatement->execute();
                       $findgroupresult = $findgroupstatement->get_result();
               
                       while ($findgrouprow = $findgroupresult->fetch_assoc()) {
                           $groupname = encrypt_decrypt($findgrouprow["groupname"], "decrypt");
                           $groupcreation = $findgrouprow["groupcreation"];
                           
                       }
                       
                       if(file_exists("media/group_PFP/" .$groupid ."-" .$groupcreation .".png")) {
                              $PFP_path = "media/group_PFP/" .$groupid ."-" .$groupcreation .".png";
                       } else {
                              $PFP_path = "media/group_PFP/default.png";
                       }
                           
                       $mediatype = substr($messagemedia, strpos($messagemedia, ".") + 1);
               
                       if(empty($messagetext) && !empty($messagemedia)) {
                         
                           if($mediatype === "png") {
                               
                           $preview = ": <i style='font-size:25px;' class='fas fa-camera'></i>";
                           
                           } else if($mediatype === "mp4") {
                           
                           $preview = ": <i style='font-size:25px;' class='fas fa-video'></i>";    
                           
                           }
                           
                       } else if(!empty($messagetext) && empty($messagemedia)) {
               
                           if(strlen($messagetext) > 15) {
                           $previewdots = "...";
                           } else {
                           $previewdots = "";
                           }
                           
                           $preview = ": " .htmlspecialchars(substr($messagetext, 0, 15)) .$previewdots;
                           
                       }
                       
                       $findparticipantsql = "SELECT * FROM Groupmember WHERE groupid = ? AND groupmembercode = ?";
                       $findparticipantstatement = $database_connection->prepare($findparticipantsql);
                       $findparticipantstatement->bind_param("ss", $groupid, $membercode);
                       $findparticipantstatement->execute();
                       $findparticipantresult = $findparticipantstatement->get_result();
               
                       while ($findparticipantrow = $findparticipantresult->fetch_assoc()) {
                           $participantlastread = $findparticipantrow["groupmemberread"];
                           
                       }                       
                       
                       $messagecreation = strtotime($messagecreation);
                       $participantlastread = strtotime($participantlastread);
               
                       if($messagecreation > $participantlastread) {
                           $isread = "<i style='float:right; color:#ff5632;' class='fa fa-circle'></i>";
                       } else {
                           $isread = "";
                       }
                       
                       $findcontactsql = "SELECT * FROM Contact WHERE contactowner = ? AND contactcode = ?";
                       $findcontactstatement = $database_connection->prepare($findcontactsql);
                       $findcontactstatement->bind_param("ss", $membercode, $messagesender);
                       $findcontactstatement->execute();
                       $findcontactresult = $findcontactstatement->get_result();
               
                       if(mysqli_num_rows($findcontactresult) > 0) {
                       while ($findcontactrow = $findcontactresult->fetch_assoc()) {
                           $contactname = encrypt_decrypt($findcontactrow["contactname"], "decrypt");
                           
                       }
                       
                       } else {
               
                           $findusersql = "SELECT * FROM Member WHERE membercode = ?";
                           $finduserstatement = $database_connection->prepare($findusersql); 
                           $finduserstatement->bind_param("s", $messagesender);
                           $finduserstatement->execute();
                           $finduserresult = $finduserstatement->get_result();
               
                           while($finduserrow = $finduserresult->fetch_assoc()) {
                           $contactname = htmlspecialchars($finduserrow["membername"]);
                           }
               
               
                       }
                       
                       if($messagesender === $membercode) {
                           $contactname = "<b>" .$you ."</b>";
                       }
                       
                       if($no_message) {
                           $preview = "";
                           $contactname = "";
                       }
                       
                       echo "<a style='text-decoration:none; color:#000000;' href='" .$chaturl ."'><p><img src='" .$PFP_path ."' width='35px;' height='35px;' style='border-radius:50%;'> <span style='font-size:20px;'>" .htmlspecialchars($groupname) ."</span> " .$contactname .$preview ." " .$isread ."</p></a>";
                    
                   }
                   echo "<hr>";
               }
               
               } else {
                echo "<br><p style='text-align:center;'><mark>" .$no_chats_yet ."</mark></p>";
               }
               ?>
         </div>
         <br>
         <p><a style="color:#f50000;" href="<?php echo $delete_page; ?>"><?php echo $delete_account; ?></a> <span style="float:right;"><?php echo $copyright; ?></span></p>
      </div>
   </body>
</html>