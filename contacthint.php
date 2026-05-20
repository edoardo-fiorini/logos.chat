<?php

session_start();

require "configuration.php";

$membercode = $_SESSION["membercode"];

$findcontactssql = "SELECT * FROM Contact WHERE contactowner = ?";
$findcontactsstatement = $database_connection->prepare($findcontactssql);
$findcontactsstatement->bind_param("s", $membercode);

$findcontactsstatement->execute();
$findcontactsresult = $findcontactsstatement->get_result();

$contacts = [];
while ($findcontactsrow = $findcontactsresult->fetch_assoc()) {
    $contacts[] = $findcontactsrow["contactid"];
}

$searchquery = $_REQUEST["search"];

$hint = null;

if (!empty($searchquery)) {
    $searchquery = strtolower($searchquery);
    $querylength = strlen($searchquery);
    foreach ((array) $contacts as $id) {
        $findnamesql =
            "SELECT * FROM Contact WHERE contactid = ? AND contactowner = ?";
        $findnamestatement = $database_connection->prepare($findnamesql);
        $findnamestatement->bind_param("ss", $id, $membercode);

        $findnamestatement->execute();
        $findnameresult = $findnamestatement->get_result();

        while ($findnamerow = $findnameresult->fetch_assoc()) {
            $code = $findnamerow["contactcode"];
            $name = $findnamerow["contactname"];

            $findidsql = "SELECT * FROM Member WHERE membercode = ?";
            $findidstatement = $database_connection->prepare($findidsql);
            $findidstatement->bind_param("s", $code);

            $findidstatement->execute();
            $findidresult = $findidstatement->get_result();

            while ($findidrow = $findidresult->fetch_assoc()) {
                $id = $findidrow["memberid"];
            }
        }

        if (
            strpos(
                strtolower(encrypt_decrypt($name, "decrypt")),
                $searchquery
            ) !== false
        ) {
            if (file_exists("media/PFP/" . $id . "-" . $code . ".png")) {
                $PFP_path = "media/PFP/" . $id . "-" . $code . ".png";
            } else {
                $PFP_path = "media/PFP/default.png";
            }

            if (empty($hint)) {
                $hint =
                    "<a style='color:blue;' href='" .
                    $chat_page .
                    "?interlocutor=" .
                    $code .
                    "'><img style='border-radius:50%;' height='35px;' width='35px;' src='" .
                    $PFP_path .
                    "'></img> " .
                    htmlspecialchars(encrypt_decrypt($name, "decrypt")) .
                    "</a>";
            } else {
                $hint .=
                    ", " .
                    "<a style='color:blue;' href='" .
                    $chat_page .
                    "?interlocutor=" .
                    $code .
                    "'><img style='border-radius:50%;' height='35px;' width='35px;' src='" .
                    $PFP_path .
                    "'></img> " .
                    htmlspecialchars(encrypt_decrypt($name, "decrypt")) .
                    "</a>";
            }
        }
    }
}

echo empty($hint) ? $no_contact_found : $hint;
?>