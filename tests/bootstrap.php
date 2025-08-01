<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

Testbench\Bootstrap::setup(__DIR__ . "/_temp", function(\Nette\Configurator $configurator): void {
  $configurator->addStaticParameters([
    "wwwDir" => __DIR__ . "/../www",
    "appDir" => __DIR__ . "/../app",
  ]);
  $configurator->addConfig(__DIR__ . "/../app/config/main.neon");
  $configurator->addConfig(__DIR__ . "/tests.neon");
  $configurator->addConfig(__DIR__ . "/local.neon");
});
?>