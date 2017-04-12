<?php
use Nette\Neon\Neon;

const WWW_DIR = __DIR__ . "/..";
const APP_DIR = WWW_DIR . "/app";

require WWW_DIR . "/vendor/autoload.php";

$config = Neon::decode(file_get_contents(APP_DIR . "/config/ci.neon"));

$connection = new Nextras\Dbal\Connection($config["dbal"]);

$connection->query("SET foreign_key_checks = 0");
$tables = $connection->query("SHOW TABLES");
foreach($tables as $table) {
  $connection->query("DROP TABLE $table->Tables_in_nexendrie_test");
}
?>