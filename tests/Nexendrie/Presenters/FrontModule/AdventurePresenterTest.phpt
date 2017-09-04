<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

class AdventurePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action): void {
    $this->checkRedirect($action, "/user/login");
    $this->login("kazimira");
    $this->checkRedirect($action, "/");
  }
  
  public function testDefault() {
    $this->defaultChecks(":Front:Adventure:default");
  }
  
  public function testMounts() {
    $this->defaultChecks(":Front:Adventure:mounts");
  }
}

$test = new AdventurePresenterTest();
$test->run();
?>