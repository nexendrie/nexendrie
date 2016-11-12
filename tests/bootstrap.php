<?php
declare(strict_types=1);

const WWW_DIR = __DIR__ ."/../";
const APP_DIR = WWW_DIR . "/app";

require __DIR__ . "/../vendor/autoload.php";

Testbench\Bootstrap::setup(__DIR__ . "/_temp", function (\Nette\Configurator $configurator) {
  $configurator->addParameters([
    "wwwDir" => __DIR__ . "/..",
    "appDir" => __DIR__ . "/../app",
  ]);
  $configurator->addConfig(__DIR__ . "/../app/config/main.neon");
  $configurator->addConfig(__DIR__ . "/../app/config/ci.neon");
});
?>