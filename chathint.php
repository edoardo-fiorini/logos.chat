<?php

session_start();

require "configuration.php";

$findchatssql = "SELECT * FROM Chat WHERE chatowner = ?";
$findchatsstatement = $database_connection->prepare($findchatssql);
$findchatsstatement->bind_param("s", $_SESSION["membercode"]);

$findchatsstatement->execute();
$findchatsresult = $findchatsstatement->get_result();

$chats = [];

while ($findchatsrow = $findchatsresult->fetch_assoc()) {
    $chats[] = $findchatsrow["chatid"];
}

$searchquery = $_REQUEST["search"];

$hint = null;

if (!empty($searchquery)) {
    $searchquery = strtolower($searchquery);
    $querylength = strlen($searchquery);
    foreach ((array) $chats as $chatid) {
        $findchatsql = "SELECT * FROM Chat WHERE chatid = ?";
        $findchatstatement = $database_connection->prepare($findchatsql);
        $findchatstatement->bind_param("s", $chatid);

        $findchatstatement->execute();
        $findchatresult = $findchatstatement->get_result();

        while ($findchatrow = $findchatresult->fetch_assoc()) {
            $chatid = $findchatrow["chatid"];
            $chaturl = $findchatrow["chaturl"];
            if (strpos($chaturl, "groupid") !== false) {
                $groupid = substr($chaturl, -1);

                $findgroupsql = "SELECT * FROM Grouptable WHERE groupid = ?";
                $findgroupstatement = $database_connection->prepare(
                    $findgroupsql
                );
                $findgroupstatement->bind_param("s", $groupid);
                $findgroupstatement->execute();
                $findgroupresult = $findgroupstatement->get_result();

                while ($findgrouprow = $findgroupresult->fetch_assoc()) {
                    $chatname = encrypt_decrypt(
                        $findgrouprow["groupname"],
                        "decrypt");
                    $groupcreation = $findgrouprow["groupcreation"];

                    if (file_exists("media/group_PFP/" . $groupid ."-" .$groupcreation . ".png")) {
                        $chat_PFP = "media/group_PFP/" . $groupid ."-" .$groupcreation . ".png";
                    } else {
                        $chat_PFP = "media/group_PFP/default.png";
                    }
                }
            } elseif (strpos($chaturl, "interlocutor") !== false) {

                $interlocutor = str_replace($chat_page ."?interlocutor=", "", $chaturl);
                
                $findusersql = "SELECT * FROM Member WHERE membercode = ?";
                $finduserstatement = $database_connection->prepare(
                    $findusersql
                );
                $finduserstatement->bind_param("s", $interlocutor);
                $finduserstatement->execute();
                $finduserresult = $finduserstatement->get_result();

                while ($finduserrow = $finduserresult->fetch_assoc()) {
                    $defaultcontactname = $finduserrow["membername"];
                    $id = $finduserrow["memberid"];
                }

                $findcontactsql = "SELECT * FROM Contact WHERE contactcode = ?";
                $findcontactstatement = $database_connection->prepare(
                    $findcontactsql
                );
                $findcontactstatement->bind_param("s", $interlocutor);
                $findcontactstatement->execute();
                $findcontactresult = $findcontactstatement->get_result();

                if (mysqli_num_rows($findcontactresult) > 0) {
                    while (
                        $findcontactrow = $findcontactresult->fetch_assoc()
                    ) {
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

                $chatname = $contactname;

                if (
                    file_exists(
                        "media/PFP/" . $id . "-" . $interlocutor . ".png"
                    )
                ) {
                    $chat_PFP =
                        "media/PFP/" . $id . "-" . $interlocutor . ".png";
                } else {
                    $chat_PFP = "media/PFP/default.png";
                }
            }
        }

        if (stristr($searchquery, substr($chatname, 0, $querylength))) {
            if (empty($hint)) {
                $hint =
                    "<a style='color:blue;' href='" .
                    $chaturl .
                    "'><img style='border-radius:50%;' height='35px;' width='35px;' src='" .
                    $chat_PFP .
                    "'></img> " .
                    $chatname .
                    "</a>";
            } else {
                $hint .=
                    ", " .
                    "<a style='color:blue;' href='" .
                    $chaturl .
                    "'><img style='border-radius:50%;' height='35px;' width='35px;' src='" .
                    $chat_PFP .
                    "'></img> " .
                    $chatname .
                    "</a>";
            }
        }
    }
}

echo empty($hint) ? "" : "<br>" .$hint;

?>