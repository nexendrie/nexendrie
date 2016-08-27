<?php
use Nette\Neon\Neon;

const WWW_DIR = __DIR__;
const APP_DIR = WWW_DIR . "/app";

require WWW_DIR . "/vendor/autoload.php";
Tracy\Debugger::enable(null, APP_DIR . "/log");

$filename = APP_DIR . "/config/local.neon";
$cfg = Neon::decode(file_get_contents($filename));
$cfg["dbal"]["host"] = "mysql";
$cfg["dbal"]["user"] = "root";
unlink($filename);
file_put_contents($filename, Neon::encode($cfg, Neon::BLOCK));
?>