<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

class PropertyPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action): void {
    $this->checkRedirect($action, "/user/login");
    $this->login();
    $this->checkAction($action);
  }
  
  public function testDefault() {
    $this->defaultChecks(":Front:Property:default");
  }
  
  public function testBudget() {
    $this->defaultChecks(":Front:Property:budget");
  }
  
  public function testEquipment() {
    $this->defaultChecks(":Front:Property:equipment");
  }
  
  public function testPotions() {
    $this->defaultChecks(":Front:Property:potions");
  }
  
  public function testTown() {
    $this->checkRedirect(":Front:Property:town", "/user/login", ["id" => 5000]);
    $this->login("jakub");
    $this->checkRedirect(":Front:Property:town", "/", ["id" => 5000]);
    $this->checkRedirect(":Front:Property:town", "/", ["id" => 1]);
    $this->login();
    $this->checkAction(":Front:Property:town", ["id" => 2]);
  }
}

$test = new PropertyPresenterTest;
$test->run();
?>