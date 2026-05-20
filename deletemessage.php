<?php

session_start();

require "configuration.php";

if (!isset($_SESSION["isloggedin"])) {
    header("Location: " . $access_page);
    exit();
}

$member = $_SESSION["memberid"];
$membercode = $_SESSION["membercode"];

$id = $_GET["id"];
$interlocutor = $_GET["interlocutor"];
$groupid = $_GET["groupid"];

$findmessageownersql = "SELECT * FROM Message WHERE messageid = ?";
$findmessageownerstatement = $database_connection->prepare(
    $findmessageownersql
);
$findmessageownerstatement->bind_param("s", $id);
$findmessageownerstatement->execute();
$findmessageownerresult = $findmessageownerstatement->get_result();

while ($findmessageownerrow = $findmessageownerresult->fetch_assoc()) {
    $messagowner = $findmessageownerrow["messagesender"];
    $messagemedia = encrypt_decrypt(
        $findmessageownerrow["messagemedia"],
        "decrypt"
    );
    $messagetext = $findmessageownerrow["messagetext"];
}

if ($messagowner === $membercode) {
    $deletemessagesql =
        "UPDATE Message SET messagemedia = NULL, messagetext = ? WHERE messageid = ?";
    $deletemessagestatement = $database_connection->prepare($deletemessagesql);
    $deletemessagestatement->bind_param(
        "ss",
        encrypt_decrypt($message_deleted, "encrypt"),
        $id
    );
    $deletemessagestatement->execute();

    unlink("media/upload_image/" . $messagemedia . ".png");
    unlink("media/upload_video/" . $messagemedia . ".mp4");
}

      $updateactivitysql = "UPDATE Member SET lastactivity = now() WHERE membercode = ?";
      $updateactivitystatement = $database_connection->prepare($updateactivitysql);
      $updateactivitystatement->bind_param("s", $_SESSION["membercode"]); 
      $updateactivitystatement->execute();

?>