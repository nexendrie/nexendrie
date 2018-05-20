<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class AdventurePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action, array $params = []): void {
    $this->checkRedirect($action, "/user/login", $params);
    $this->login("kazimira");
    $this->checkRedirect($action, "/", $params);
  }
  
  public function testDefault() {
    $this->defaultChecks(":Front:Adventure:default");
    $this->login("Rahym");
    $this->checkRedirect(":Front:Adventure:default", "/adventure/list");
  }
  
  public function testMounts() {
    $this->defaultChecks(":Front:Adventure:mounts", ["id" => 1]);
    $this->login("Rahym");
    $this->checkAction(":Front:Adventure:mounts", ["id" => 1]);
  }
  
  public function testList() {
    $this->defaultChecks(":Front:Adventure:list");
    $this->login("Rahym");
    $this->checkAction(":Front:Adventure:list");
  }
}

$test = new AdventurePresenterTest();
$test->run();
?>