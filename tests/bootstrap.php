<?php
declare(strict_types=1);

const WWW_DIR = __DIR__ ."/../";
const APP_DIR = WWW_DIR . "/app";

require __DIR__ . "/../vendor/autoload.php";

Trait TUserControl {
  function login($username = "", $password = "") {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    if($username === "") $username = getenv("APP_USER");
    if($password === "") $password = getenv("APP_PASSWORD");
    $user->login($username, $password);
  }
  
  function logout() {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    $user->logout(true);
  }
  
  function tearDown() {
    $this->logout();
  }
}

Testbench\Bootstrap::setup(__DIR__ . "/_temp", function (\Nette\Configurator $configurator) {
  $configurator->addParameters([
    "appDir" => __DIR__ . "/../app",
  ]);
  $configurator->addConfig(__DIR__ . "/../app/config/ci.neon");
});
?>