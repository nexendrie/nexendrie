<?php
declare(strict_types=1);

use Nette\Neon\Neon,
    Nette\Utils\Arrays,
    Nextras\Dbal\Utils\FileImporter;

require __DIR__ . "/../vendor/autoload.php";

Tracy\Debugger::timer("config_db");

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

echo "Setting up database ...\n";

$connection = new Nextras\Dbal\Connection($config["dbal"]);

if($db["driver"] === "mysqli") {
  $files = ["structure", "data_basic"];
  $extension = "mysql";
} else {
  $files = ["structure", "data_basic", "final"];
  $extension = "pgsql";
}
$sqlsFolder = __DIR__ . "/../app/sqls";

foreach($files as $file) {
  echo "Executing file: $file.$extension ... ";
  Tracy\Debugger::timer($file);
  FileImporter::executeFile($connection, "$sqlsFolder/$file.$extension");
  $time = round(Tracy\Debugger::timer($file), 2);
  echo "Done in $time second(s)\n";
}
echo "Tables were created and filled with basic data.\n";

$time = round(Tracy\Debugger::timer("config_db"), 2);
echo "\nTotal time: $time second(s)\n";
?>