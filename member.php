<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
require("configuration.php");

$userssql = "SELECT * FROM Member";
$usersstatement = $database_connection->prepare($userssql);
$usersstatement->execute();
$usersresult = $usersstatement->get_result();

echo mysqli_num_rows($usersresult);
?>