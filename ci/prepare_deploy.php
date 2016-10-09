<?php
const WWW_DIR = __DIR__ . "/..";
const APP_DIR = WWW_DIR . "/app";

require WWW_DIR . "/vendor/autoload.php";

$filename = WWW_DIR . "/deployment.ini";
$config = file_get_contents($filename);
$user = getenv("FTP_ALPHA_USER");
$password = getenv("FTP_ALPHA_PASSWORD");
$config .= "user=$user
password=$password
";
file_put_contents($filename, $config);
?>