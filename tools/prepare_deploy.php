<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

$filename = __DIR__ . "/../deployment.ini";
$config = file_get_contents($filename);
switch(getenv("DEPLOY_ENVIRONMENT")) {
  case "alpha":
    $user = getenv("FTP_ALPHA_USER");
    $password = getenv("FTP_ALPHA_PASSWORD");
    break;
  case "beta":
    $user = getenv("FTP_BETA_USER");
    $password = getenv("FTP_BETA_PASSWORD");
    break;
  default:
    echo "Error: invalid environment";
    exit(1);
}
$config .= "user=$user
password=$password
";
file_put_contents($filename, $config);
?>