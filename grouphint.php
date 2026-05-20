<?php

session_start();

require "configuration.php";

$findgroupssql = "SELECT * FROM Groupmember WHERE groupmembercode = ?";
$findgroupsstatement = $database_connection->prepare($findgroupssql);
$findgroupsstatement->bind_param("s", $_SESSION["membercode"]);

$findgroupsstatement->execute();
$findgroupsresult = $findgroupsstatement->get_result();

$groups = [];

while ($findgroupsrow = $findgroupsresult->fetch_assoc()) {
    $groups[] = $findgroupsrow["groupid"];
}

$searchquery = $_REQUEST["search"];

$hint = null;

if (!empty($searchquery)) {
    $searchquery = strtolower($searchquery);
    $querylength = strlen($searchquery);
    foreach ((array) $groups as $groupid) {
        $findgroupsql = "SELECT * FROM Grouptable WHERE groupid = ?";
        $findgroupstatement = $database_connection->prepare($findgroupsql);
        $findgroupstatement->bind_param("s", $groupid);

        $findgroupstatement->execute();
        $findgroupresult = $findgroupstatement->get_result();

        while ($findgrouprow = $findgroupresult->fetch_assoc()) {
            $groupname = encrypt_decrypt($findgrouprow["groupname"], "decrypt");
            $groupid = $findgrouprow["groupid"];
            $groupcreation = $findgrouprow["groupcreation"];
        }

        if (file_exists("media/group_PFP/" . $groupid ."-" .$groupcreation . ".png")) {
            $PFP_path = "media/group_PFP/" . $groupid ."-" .$groupcreation . ".png";
        } else {
            $PFP_path = "media/group_PFP/default.png";
        }

        if (stristr($searchquery, substr($groupname, 0, $querylength))) {
            if (empty($hint)) {
                $hint =
                    "<a style='color:blue;' href='" .
                    $group_page .
                    "?groupid=" .
                    $groupid .
                    "'><img style='border-radius:50%;' height='35px;' width='35px;' src='" .
                    $PFP_path .
                    "'></img> " .
                    htmlspecialchars($groupname) .
                    "</a>";
            } else {
                $hint .=
                    ", " .
                    "<a style='color:blue;' href='" .
                    $group_page .
                    "?groupid=" .
                    $groupid .
                    "'><img style='border-radius:50%;' height='30px;' width='30px;' src='" .
                    $PFP_path .
                    "'></img> " .
                    htmlspecialchars($groupname) .
                    "</a>";
            }
        }
    }
}

echo empty($hint) ? $no_group_found : $hint;
?>