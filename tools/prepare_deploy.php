<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

$filename = __DIR__ . "/../deployment.ini";
$config = file_get_contents($filename);
switch(getenv("DEPLOY_ENVIRONMENT")) {
  case "alpha":
    $remote = "sftp://nexendrie.cz:2222/www/nexendrie.cz/wwwroot/alpha";
    break;
  case "beta":
    $remote = "sftp://nexendrie.cz:2222/www/nexendrie.cz/wwwroot/beta";
    break;
  default:
    echo "Error: invalid environment";
    exit(1);
}
$user = getenv("SSH_USER");
$password = getenv("SSH_PASSWORD");
$config .= "remote=$remote
user=$user
password=$password
";
file_put_contents($filename, $config);
?>