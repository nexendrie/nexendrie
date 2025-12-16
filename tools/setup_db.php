<?php
declare(strict_types=1);

use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require __DIR__ . "/../vendor/autoload.php";

$environment = "testing";
$config = new Config(require __DIR__ . "/../phinx.php");
$manager = new Manager($config, new StringInput(" "), new ConsoleOutput());
$manager->migrate($environment);
$manager->seed($environment);
