<?php
declare(strict_types=1);

use Nette\Neon\Neon;
use Nette\Utils\Arrays;

require __DIR__ . "/../vendor/autoload.php";

$filename = __DIR__ . "/../app/config/local.neon";
$db = [
  "driver" => Arrays::get($argv, 1, "mysqli"),
  "host" => Arrays::get($argv, 2, "localhost"),
  "database" => Arrays::get($argv, 3, "nexendrie"),
  "username" => Arrays::get($argv, 4, "nexendrie"),
  "password" => Arrays::get($argv, 5, "nexendrie"),
];

$content = file_get_contents($filename);
if($content === false) {
  throw new RuntimeException("File $filename does not exist or cannot be read.");
}
$config = Neon::decode($content);
$config["dbal"] = $db;
file_put_contents($filename, Neon::encode($config, Neon::BLOCK));
echo "Settings written to " . realpath($filename) . ".\n";

$connection = new Nextras\Dbal\Connection($config["dbal"]);

$dbImporter = new Nexendrie\Database\DatabaseImporter($connection, $config["dbal"]["driver"]);
$dbImporter->folder = __DIR__ . "/../app/sqls";
$dbImporter->finalMessage = "Tables were created and filled with basic data.";
$dbImporter->useBasicData();
$dbImporter->run();
?>