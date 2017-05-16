<?php
use Nette\Neon\Neon;

require __DIR__ . "/../vendor/autoload.php";

$filename = __DIR__ . "/../tests/local.neon";
$config = Neon::decode(file_get_contents($filename));
$config["dbal"]["driver"] = "pgsql";
$config["dbal"]["host"] = "postgres";
file_put_contents($filename, Neon::encode($config, Neon::BLOCK));
?>