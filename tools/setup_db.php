<?php
use Nette\Neon\Neon,
    Nextras\Dbal\Utils\FileImporter;

require __DIR__ . "/../vendor/autoload.php";

$config = Neon::decode(file_get_contents(__DIR__ . "/../tests/local.neon"));

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
  echo "Executing file: $file.$extension";
  FileImporter::executeFile($connection, "$sqlsFolder/$file.$extension");
  echo " ... Done\n";
}
?>