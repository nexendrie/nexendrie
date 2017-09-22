<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class OrderPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action): void {
    $this->checkRedirect($action, "/user/login");
    $this->login("Rahym");
    $this->checkRedirect($action, "/");
    $this->login();
    $this->checkAction($action);
  }
  
  public function testDefault() {
    $this->defaultChecks(":Front:Order:default");
  }
  
  public function testList() {
    $this->checkAction(":Front:Order:list");
  }
  
  public function testDetail() {
    Assert::exception(function() {
      $this->check(":Front:Order:detail", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Order:detail", ["id" => 1]);
  }
  
  public function testFound() {
    $this->checkRedirect(":Front:Order:found", "/user/login");
    $this->login("Rahym");
    $this->checkRedirect(":Front:Order:found", "/");
  }
  
  public function testJoin() {
    $this->checkRedirect(":Front:Order:join", "/user/login", ["id" => 5000]);
    $this->login("Rahym");
    $this->checkRedirect(":Front:Order:join", "/", ["id" => 5000]);
  }
  
  public function testLeave() {
    $this->checkRedirect(":Front:Order:leave", "/user/login");
    $this->login("Rahym");
    $this->checkRedirect(":Front:Order:leave", "/");
  }
  
  public function testManage() {
    $this->defaultChecks(":Front:Order:manage");
  }
  
  public function testMembers() {
    $this->defaultChecks(":Front:Order:members");
  }
  
  public function testSignalPromote() {
    $this->checkSignal(":Front:Order:members", "promote", ["user" => 5000], [], "/user/login");
    $this->login();
    $this->checkSignal(":Front:Order:members", "promote", ["user" => 5000], [], "/");
    $this->checkSignal(":Front:Order:members", "promote", ["user" => 3], [], "/");
    $this->checkSignal(":Front:Order:members", "promote", ["user" => 1], [], "/order/members");
  }
  
  public function testSignalDemote() {
    $this->checkSignal(":Front:Order:members", "demote", ["user" => 5000], [], "/user/login");
    $this->login();
    $this->checkSignal(":Front:Order:members", "demote", ["user" => 5000], [], "/");
    $this->checkSignal(":Front:Order:members", "demote", ["user" => 3], [], "/");
    $this->checkSignal(":Front:Order:members", "demote", ["user" => 1], [], "/order/members");
  }
  
  public function testSignalKick() {
    $this->checkSignal(":Front:Order:members", "kick", ["user" => 5000], [], "/user/login");
    $this->login();
    $this->checkSignal(":Front:Order:members", "kick", ["user" => 5000], [], "/");
    $this->checkSignal(":Front:Order:members", "kick", ["user" => 3], [], "/");
    $this->checkSignal(":Front:Order:members", "kick", ["user" => 1], [], "/order/members");
  }
}

$test = new OrderPresenterTest();
$test->run();
?>