<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

final class UserPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action): void {
    $this->checkAction($action);
    $this->login();
    $this->checkRedirect($action, "/");
  }
  
  public function testLogin() {
    $this->defaultChecks(":Front:User:login");
  }
  
  public function testLogout() {
    $this->checkRedirect(":Front:User:logout", "/");
    $this->login();
    $this->checkRedirect(":Front:User:logout", "/");
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    Assert::false($user->loggedIn, "The user is logged in.");
  }
  
  public function testRegister() {
    $this->defaultChecks(":Front:User:register");
  }
  
  public function testSettings() {
    $this->checkRedirect(":Front:User:settings", "/user/login");
    $this->login();
    $this->checkAction(":Front:User:settings");
  }

  public function testApiTokens() {
    $this->checkRedirect(":Front:User:apiTokens", "/user/login");
    $this->login();
    $this->checkAction(":Front:User:apiTokens");
  }

  public function testList() {
    $this->checkAction(":Front:User:list");
  }
}

$test = new UserPresenterTest();
$test->run();
?>