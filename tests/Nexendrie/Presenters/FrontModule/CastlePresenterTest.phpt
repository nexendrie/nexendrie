<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class CastlePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action): void {
    $this->checkRedirect($action, "/user/login");
    $this->login("jakub");
    $this->checkRedirect($action, "");
  }
  
  public function testDefault() {
    $this->defaultChecks(":Front:Castle:default");
    $this->login();
    $this->checkAction(":Front:Castle:default");
  }
  
  public function testList() {
    $this->checkAction(":Front:Castle:list");
  }
  
  public function testDetail() {
    Assert::exception(function() {
      $this->check(":Front:Castle:detail", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Castle:detail", ["id" => 1]);
  }
  
  public function testBuild() {
    $this->defaultChecks(":Front:Castle:build");
    $this->login();
    $this->checkRedirect(":Front:Castle:build", "/castle");
  }
}

$test = new CastlePresenterTest();
$test->run();
?>