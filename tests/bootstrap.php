<?php
declare(strict_types=1);

const WWW_DIR = __DIR__ ."/../";
const APP_DIR = WWW_DIR . "/app";

require __DIR__ . "/../vendor/autoload.php";

/**
 * TUserControl
 * Simplifies working with users in a test suit
 *
 * @author Jakub Konečný
 */
trait TUserControl {
  use \Testbench\TCompiledContainer;
  
  /**
   * Login a user
   *
   * @param string $username
   * @param string $password
   * @return void
   */
  function login($username = "", $password = "") {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    if($username === "") $username = getenv("APP_USER");
    if($password === "") $password = getenv("APP_PASSWORD");
    $user->login($username, $password);
  }
  
  /**
   * Logout the user
   *
   * @return void
   */
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
    "wwwDir" => __DIR__ . "/..",
    "appDir" => __DIR__ . "/../app",
  ]);
  $configurator->addConfig(__DIR__ . "/../app/config/main.neon");
  $configurator->addConfig(__DIR__ . "/../app/config/ci.neon");
});
?>