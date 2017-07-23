<?php
declare(strict_types=1);

use Nette\Neon\Neon;

require __DIR__ . "/../vendor/autoload.php";

$config = Neon::decode(file_get_contents(__DIR__ . "/../tests/local.neon"));

$connection = new Nextras\Dbal\Connection($config["dbal"]);

$connection->query("SET foreign_key_checks = 0");
$tables = $connection->query("SHOW TABLES");
foreach($tables as $table) {
  $connection->query("DROP TABLE $table->Tables_in_nexendrie_test");
}
?>