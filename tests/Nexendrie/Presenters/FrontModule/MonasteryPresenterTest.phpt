<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class MonasteryPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action, array $params = []): void {
    $this->checkRedirect($action, "/user/login", $params);
    $this->login();
    $this->checkRedirect($action, "/", $params);
  }
  
  public function testDefault() {
    $this->defaultChecks(":Front:Monastery:default");
    $this->login("Rahym");
    $this->checkAction(":Front:Monastery:default");
  }
  
  public function testList() {
    $this->checkAction(":Front:Monastery:list");
  }
  
  public function testDetail() {
    Assert::exception(function() {
      $this->check(":Front:Monastery:detail", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Monastery:detail", ["id" => 1]);
  }
  
  public function testBuild() {
    $this->defaultChecks(":Front:Monastery:build");
  }
  
  public function testJoin() {
    $this->defaultChecks(":Front:Monastery:join", ["id" => 5000]);
    $this->login("bozena");
    Assert::exception(function() {
      $this->check(":Front:Monastery:join", ["id" => 5000]);
    }, BadRequestException::class);
  }
  
  public function testLeave() {
    $this->checkRedirect(":Front:Monastery:leave", "/user/login");
    $this->login("Rahym");
    $this->checkRedirect(":Front:Monastery:leave", "/monastery");
  }
  
  public function testPray() {
    $this->defaultChecks(":Front:Monastery:pray");
  }
  
  public function testManage() {
    $this->defaultChecks(":Front:Monastery:manage");
    $this->login("Rahym");
    $this->checkAction(":Front:Monastery:manage");
  }
  
  public function testChat() {
    $this->defaultChecks(":Front:Monastery:chat");
    $this->login("Rahym");
    $this->checkAction(":Front:Monastery:chat");
  }
}

$test = new MonasteryPresenterTest();
$test->run();
?>