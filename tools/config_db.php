<?php
declare(strict_types=1);

use Nette\Neon\Neon;
use Nette\Utils\Arrays;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require __DIR__ . "/../vendor/autoload.php";

$filename = __DIR__ . "/../app/config/local.neon";
$db = [
    "driver" => Arrays::get($argv, 1, "mysqli"),
    "host" => Arrays::get($argv, 2, "localhost"),
    "database" => Arrays::get($argv, 3, "nexendrie"),
    "username" => Arrays::get($argv, 4, "nexendrie"),
    "password" => Arrays::get($argv, 5, "nexendrie"),
];

$content = file_get_contents($filename);
if ($content === false) {
    throw new RuntimeException("File $filename does not exist or cannot be read.");
}
$config = Neon::decode($content);
$config["dbal"] = $db;
file_put_contents($filename, Neon::encode($config, true));
echo "Settings written to " . realpath($filename) . ".\n";

echo "Running database migrations ...\n";

$environment = "production";
$config = new Config(require __DIR__ . "/../phinx.php");
$manager = new Manager($config, new StringInput(" "), new ConsoleOutput());
$manager->migrate($environment);
