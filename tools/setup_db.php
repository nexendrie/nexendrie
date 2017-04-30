<?php
use Nette\Neon\Neon,
    Nextras\Dbal\Utils\FileImporter;

require __DIR__ . "/../vendor/autoload.php";

$config = Neon::decode(file_get_contents(__DIR__ . "/../app/config/ci.neon"));

$connection = new Nextras\Dbal\Connection($config["dbal"]);
$sqlsFolder = __DIR__ . "/../app/sqls";
$files = ["structure", "data_basic", "data_test"];
foreach($files as $file) {
  FileImporter::executeFile($connection, "$sqlsFolder/$file.sql");
}
?>