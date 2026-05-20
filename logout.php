<?php

session_start();

require "configuration.php";
    
$dismissactivitysql = "UPDATE Member SET lastactivity = (lastactivity - INTERVAL 3 MINUTE) WHERE membercode = ?";
$dismissactivitystatement = $database_connection->prepare($dismissactivitysql);
$dismissactivitystatement->bind_param("s", $_SESSION["membercode"]); 
$dismissactivitystatement->execute();
      
$_SESSION = [];

session_destroy();

header("Location: " .$index_page);
?>