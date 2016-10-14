<?php
const WWW_DIR = __DIR__ . "/..";
const APP_DIR = WWW_DIR . "/app";

require WWW_DIR . "/vendor/autoload.php";

$filename = WWW_DIR . "/deployment.ini";
$config = file_get_contents($filename);
if(!isset($_ENV["DEPLOY_ENVIRONMENT"])) {
  echo "Error: deploy environment is not set.";
  exit(1);
}
switch(getenv("DEPLOY_ENVIRONMENT")) {
  case "alpha":
    $user = getenv("FTP_ALPHA_USER");
    $password = getenv("FTP_ALPHA_PASSWORD");
    break;
  case "beta":
    $user = getenv("FTP_BETA_USER");
    $password = getenv("FTP_BETA_PASSWORD");
    break;
}
$user = getenv("FTP_ALPHA_USER");
$password = getenv("FTP_ALPHA_PASSWORD");
$config .= "user=$user
password=$password
";
file_put_contents($filename, $config);
?>