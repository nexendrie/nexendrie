<?php
use Nette\Neon\Neon;

const WWW_DIR = __DIR__ . "/..";
const APP_DIR = WWW_DIR . "/app";

require WWW_DIR . "/vendor/autoload.php";

$config = Neon::decode(file_get_contents(APP_DIR . "/config/ci.neon"));

$connection = new Nextras\Dbal\Connection($config["dbal"]);
$sqlsFolder = APP_DIR . "/sqls";
$files = ["structure", "data_basic", "data_test"];
foreach($files as $file) {
  Nextras\Dbal\Utils\FileImporter::executeFile($connection, "$sqlsFolder/$file.sql");
}
?>