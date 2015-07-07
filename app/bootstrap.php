<?php
use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

require LIBS_DIR . "/nette.phar";
Tracy\Debugger::enable(null, APP_DIR . "/log");

$configurator = new Nette\Configurator;
$configurator->setTempDirectory(APP_DIR . "/temp");
$configurator->addConfig(APP_DIR . "/config/main.neon");
$configurator->createRobotLoader()
    ->addDirectory(LIBS_DIR)
    ->addDirectory(APP_DIR)
    ->register();
$container = $configurator->createContainer();

$router = new RouteList;
$router[] = new Route("profile/<username>", "Front:Profile:default");
$router[] = new Route("message/<id [0-9]+>", "Front:Messages:view");
$router[] = new Route("poll/<id [0-9]+>", "Front:Poll:view");
$router[] = new Route("news/page/<page [0-9]+>", "Front:News:page");
$router[] = new Route("rss[/<action>][/<news [0-9]+>]", "Front:Rss:news");
$router[] = new Route("admin/groups", "Admin:Group:default");
$router[] = new Route("admin/<presenter>[/<action>][/<id>]", array(
"module" => "Admin", "presenter" => "Homepage", "action" => "default"));
$router[] = new Route("<presenter>[/<action>][/<id>]", array(
"module" => "Front", "presenter" => "Homepage", "action" => "default"));
$container->addService("router", $router);

return $container;
?>