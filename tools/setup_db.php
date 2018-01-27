<?php
declare(strict_types=1);

use Nette\Neon\Neon;

require __DIR__ . "/../vendor/autoload.php";

Tracy\Debugger::timer("setup_db");

$config = Neon::decode(file_get_contents(__DIR__ . "/../tests/local.neon"));

$connection = new Nextras\Dbal\Connection($config["dbal"]);

$dbImporter = new Nexendrie\Database\DatabaseImporter($connection, $config["dbal"]["driver"]);
$dbImporter->folder = __DIR__ . "/../app/sqls";
$dbImporter->useBasicAndTestData();
$dbImporter->run();
?>