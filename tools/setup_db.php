<?php
use Nette\Neon\Neon,
    Nextras\Dbal\Utils\FileImporter;

require __DIR__ . "/../vendor/autoload.php";

Tracy\Debugger::timer("setup_db");

$config = Neon::decode(file_get_contents(__DIR__ . "/../tests/local.neon"));

echo "Setting up database ...\n";

$connection = new Nextras\Dbal\Connection($config["dbal"]);

if($config["dbal"]["driver"] === "mysqli") {
  $files = ["structure", "data_basic", "data_test"];
  $extension = "mysql";
} else {
  $files = ["structure", "data_basic", "data_test", "final"];
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

$time = round(Tracy\Debugger::timer("setup_db"), 2);
echo "\nTotal time: $time second(s)\n";
?>