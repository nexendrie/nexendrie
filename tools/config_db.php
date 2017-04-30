<?php
use Nette\Neon\Neon,
    Nette\Utils\Arrays,
    Nextras\Dbal\Utils\FileImporter;

require __DIR__ . "/../vendor/autoload.php";

$filename = __DIR__ . "../app/config/local.neon";
$db = [
  "driver" => Arrays::get($argv, 1, "mysqli"),
  "host" => Arrays::get($argv, 2, "localhost"),
  "dbname" => Arrays::get($argv, 3, "nexendrie"),
  "user" => Arrays::get($argv, 4, "nexendrie"),
  "password" => Arrays::get($argv, 5, "nexendrie"),
];

$config = Neon::decode(file_get_contents($filename));
$config["dbal"] = $db;
file_put_contents($filename, Neon::encode($config, Neon::BLOCK));
echo "Settings written to " . realpath($filename) . ".\n";

if($db["driver"] !== "mysqli") {
  echo "PostgreSql database cannot be set up automatically at the moment.\n";
  exit(0);
} else {
  $sqlsFolder = __DIR__ . "/../app/sqls";
}

echo "Setting up database ...\n";
$connection = new Nextras\Dbal\Connection($config["dbal"]);
$files = ["structure", "data_basic"];
foreach($files as $file) {
  FileImporter::executeFile($connection, "$sqlsFolder/$file.sql");
}
echo "Tables were created and filled with basic data.\n";
?>