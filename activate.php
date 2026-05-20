<?php

session_start();

require "configuration.php";

if (isset($_GET["email"]) && isset($_GET["hash"])) {
    $email = $_GET["email"];
    $hash = $_GET["hash"];

    $activateusersql =
        "UPDATE Member SET memberverified = 1 WHERE memberemail = ? AND memberhash = ?";
    $activateuserstatement = $database_connection->prepare($activateusersql);
    $activateuserstatement->bind_param("ss", $email, $hash);
    if ($activateuserstatement->execute()) {
        header("Location: " .$index_page);
        exit();
    }
}

$webpage_name = $application_name . " - " . $activate_title;
?>


<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title><?php echo $webpage_name; ?></title>
      <link rel="stylesheet" href="<?php echo $css_stylesheet_location; ?>">
      <link rel="stylesheet" href="bootstrap.css">
      <link rel="icon" type="image/ico" href="<?php echo $favicon_path; ?>">
   </head>
   <body>
      <br><img src="<?php echo $application_logo_path; ?>" style="display: block; margin-left: auto; margin-right: auto;" width="200px" onclick="window.location.href = '<?php echo $index_page; ?>';" height="80px">
      
      <div style="background-color:#ffffff;" class="jumbotron">
         <h1 class="display-5"><?php echo $unsuccessful_activation; ?></h1>
         <p class="lead"><?php echo $unsuccessful_activation_message; ?></p>
      </div>
   </body>
</html>