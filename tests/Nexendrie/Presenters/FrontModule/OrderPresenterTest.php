<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class OrderPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action): void {
    $this->checkRedirect($action, "/user/login");
    $this->login("Rahym");
    $this->checkRedirect($action, "/");
    $this->login();
    $this->checkAction($action);
  }
  
  public function testDefault(): void {
    $this->defaultChecks(":Front:Order:default");
  }
  
  public function testList(): void {
    $this->checkAction(":Front:Order:list");
  }
  
  public function testDetail(): void {
    Assert::exception(function() {
      $this->check(":Front:Order:detail", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Order:detail", ["id" => 1]);
  }
  
  public function testFound(): void {
    $this->checkRedirect(":Front:Order:found", "/user/login");
    $this->login("Rahym");
    $this->checkRedirect(":Front:Order:found", "/");
  }
  
  public function testJoin(): void {
    $this->checkRedirect(":Front:Order:join", "/user/login", ["id" => 5000]);
    $this->login("Rahym");
    $this->checkRedirect(":Front:Order:join", "/", ["id" => 5000]);
  }
  
  public function testLeave(): void {
    $this->checkRedirect(":Front:Order:leave", "/user/login");
    $this->login("Rahym");
    $this->checkRedirect(":Front:Order:leave", "/");
  }
  
  public function testManage(): void {
    $this->defaultChecks(":Front:Order:manage");
  }
  
  public function testMembers(): void {
    $this->defaultChecks(":Front:Order:members");
  }
  
  public function testSignalPromote(): void {
    $this->checkSignal(":Front:Order:members", "promote", ["user" => 5000], [], "/user/login");
    $this->login();
    $this->checkSignal(":Front:Order:members", "promote", ["user" => 5000], [], "/order/members");
    $this->checkSignal(":Front:Order:members", "promote", ["user" => 3], [], "/order/members");
    $this->checkSignal(":Front:Order:members", "promote", ["user" => 1], [], "/order/members");
  }
  
  public function testSignalDemote(): void {
    $this->checkSignal(":Front:Order:members", "demote", ["user" => 5000], [], "/user/login");
    $this->login();
    $this->checkSignal(":Front:Order:members", "demote", ["user" => 5000], [], "/order/members");
    $this->checkSignal(":Front:Order:members", "demote", ["user" => 3], [], "/order/members");
    $this->checkSignal(":Front:Order:members", "demote", ["user" => 1], [], "/order/members");
  }
  
  public function testSignalKick(): void {
    $this->checkSignal(":Front:Order:members", "kick", ["user" => 5000], [], "/user/login");
    $this->login();
    $this->checkSignal(":Front:Order:members", "kick", ["user" => 5000], [], "/order/members");
    $this->checkSignal(":Front:Order:members", "kick", ["user" => 3], [], "/order/members");
    $this->checkSignal(":Front:Order:members", "kick", ["user" => 1], [], "/order/members");
  }
  
  public function testChat(): void {
    $this->defaultChecks(":Front:Order:chat");
  }
}

$test = new OrderPresenterTest();
$test->run();
?>