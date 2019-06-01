<?php
declare(strict_types=1);

use Nette\Neon\Neon;

require __DIR__ . "/../vendor/autoload.php";

$filename = __DIR__ . "/../tests/local.neon";
$content = file_get_contents($filename);
if($content === false) {
  throw new RuntimeException("File $filename does not exist or cannot be read.");
}
$config = Neon::decode($content);

$connection = new Nextras\Dbal\Connection($config["dbal"]);

$dbImporter = new Nexendrie\Database\DatabaseImporter($connection, $config["dbal"]["driver"]);
$dbImporter->folder = __DIR__ . "/../app/sqls";
$dbImporter->useBasicAndTestData();
$dbImporter->run();
?>