<?php
define("WWW_DIR", dirname(__FILE__));
define("APP_DIR", WWW_DIR . "/app");
define("LIBS_DIR", WWW_DIR . "/libs");

define("GUEST_ROLE", 9);
define("LOGGEDIN_ROLE", 8);
define("BANNED_ROLE", 10);

$container = require APP_DIR . "/bootstrap.php";
$container->getService("application")->run();
?>