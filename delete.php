<?php

session_start();

require "configuration.php";

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];

if (!isset($_SESSION["isloggedin"])) {
    header("Location: " . $home_page);
}

if (
    isset($_POST["deleteaccount"])
) {
    
    if($_POST["confirm"] === "on" &&
    $_POST["mycode"] === $membercode &&
    $_SESSION["memberpassword"] === $_POST["mypassword"]) {
    
    $deleteaccountsql = "DELETE FROM Member WHERE memberid = ?";
    $deleteaccountstatement = $database_connection->prepare($deleteaccountsql);
    $deleteaccountstatement->bind_param("s", $member);
    $deleteaccountstatement->execute();

    $findmessagessql =
        "SELECT * FROM Message WHERE messagesender = ? OR messagerecipient = ?";
    $findmessagesstatement = $database_connection->prepare($findmessagessql);
    $findmessagesstatement->bind_param("ss", $membercode, $membercode);
    $findmessagesstatement->execute();

    $findmessagesresult = $findmessagesstatement->get_result();

    while ($findmessagesrow = $findmessagesresult->fetch_assoc()) {
        $deletemessagessql = "DELETE FROM Message WHERE messagesender = ?";
        $deletemessagesstatement = $database_connection->prepare(
            $deletemessagessql
        );
        $deletemessagesstatement->bind_param("s", $membercode);
        $deletemessagesstatement->execute();
        unlink(
            "media/upload_image/" .
                encrypt_decrypt($findmessagesrow["messagemedia"], "decrypt") .
                ".png"
        );
    }

    $chaturl = $chat_page ."?interlocutor=" . $membercode;

    $deletechatssql = "DELETE FROM Chat WHERE chatowner = ? OR chaturl = ?";
    $deletechatsstatement = $database_connection->prepare($deletechatssql);
    $deletechatsstatement->bind_param("ss", $membercode, $chaturl);
    $deletechatsstatement->execute();

    $deletecontactssql =
        "DELETE FROM Contact WHERE contactcode = ? OR contactowner = ?";
    $deletecontactsstatement = $database_connection->prepare(
        $deletecontactssql
    );
    $deletecontactsstatement->bind_param("ss", $membercode, $membercode);
    $deletecontactsstatement->execute();

    $deletegroupmembersql = "DELETE FROM Groupmember WHERE groupmembercode = ?";
    $deletegroupmemberstatement = $database_connection->prepare(
        $deletegroupmembersql
    );
    $deletegroupmemberstatement->bind_param("s", $membercode);
    $deletegroupmemberstatement->execute();

    $deleteblockssql =
        "DELETE FROM Block WHERE blockowner = ? OR blockcode = ?";
    $deleteblocksstatement = $database_connection->prepare($deleteblockssql);
    $deleteblocksstatement->bind_param("ss", $membercode, $membercode);
    $deleteblocksstatement->execute();

    unlink("media/PFP/" . $_SESSION["membercode"] . ".png");

    header("Location: " . $logout_page);
    
    exit;
    
    } else {
        $wrong_deletion = 1;
    }
}

$webpage_name = $application_name . " - " . $account_deletion;

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
  
         <h1 style="text-align:center; font-size:32px;"><?php echo $account_deletion_page; ?></h1><br>
         <form method="post">
             
            <p style="font-size:20px;" class="lead"><?php echo $account_deletion_message; ?></p>
            
            <?php if($wrong_deletion) { ?>
            <mark><?php echo "<br>" .$code_or_password_wrong ."<br>"; ?></mark><br>
            <?php } ?>
        
            <input type="checkbox" required="true" id="confirm" name="confirm">
            <label for="confirm"><?php echo $account_deletion_confirm;?></label>
            <br><br>
            <p style="font-size:20px;" class="lead"><label for="mycode"><?php echo $enter_messaging_code; ?> (<?php echo htmlspecialchars($_SESSION["membercode"]); ?>)</label></p>
            <input type="text" required="true" maxlength="20" name="mycode" id="mycode"><br><br>

            <p style="font-size:20px;" class="lead"><label for="mypassword"><?php echo $enter_your_password; ?></label></p>
            <input type="text" required="true" name="mypassword" id="mypassword"><br><br>
            <br><br>            
            
            
            <input type="submit" class="btn btn" style="background-color:#e0e0e0; font-size:13px;" name="deleteaccount" value="<?php echo $delete_account; ?>">
         </form>
      </div>
      <br>
   </body>
</html>