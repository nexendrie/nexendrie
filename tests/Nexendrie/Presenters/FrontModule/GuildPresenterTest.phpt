<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class GuildPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action): void {
    $this->checkRedirect($action, "/user/login");
    $this->login();
    $this->checkRedirect($action, "/");
    $this->login("jakub");
    $this->checkAction($action);
  }
  
  public function testDefault() {
    $this->defaultChecks(":Front:Guild:default");
  }
  
  public function testList() {
    $this->checkAction(":Front:Guild:list");
  }
  
  public function testDetail() {
    Assert::exception(function() {
      $this->check(":Front:Guild:detail", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Guild:detail", ["id" => 1]);
  }
  
  public function testFound() {
    $this->checkRedirect(":Front:Guild:found", "/user/login");
    $this->login();
    $this->checkRedirect(":Front:Guild:found", "/");
    $this->login("kazimira");
    $this->checkAction(":Front:Guild:found");
  }
  
  public function testJoin() {
    $this->checkRedirect(":Front:Guild:join", "/user/login", ["id" => 5000]);
    $this->login();
    $this->checkRedirect(":Front:Guild:join", "/", ["id" => 5000]);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->check(":Front:Guild:join", ["id" => 5000]);
    }, BadRequestException::class);
  }
  
  public function testLeave() {
    $this->checkRedirect(":Front:Guild:leave", "/user/login");
    $this->login();
    $this->checkRedirect(":Front:Guild:leave", "/");
  }
  
  public function testManage() {
    $this->defaultChecks(":Front:Guild:manage");
  }
  
  public function testMembers() {
    $this->checkRedirect(":Front:Guild:members", "/user/login");
    $this->login();
    $this->checkRedirect(":Front:Guild:members", "/");
    $this->login("jakub");
    $this->checkAction(":Front:Guild:members");
  }
  
  public function testSignalPromote() {
    $this->checkSignal(":Front:Guild:members", "promote", ["user" => 5000], [], "/user/login");
    $this->login("jakub");
    $this->checkSignal(":Front:Guild:members", "promote", ["user" => 5000], [], "/");
    $this->checkSignal(":Front:Guild:members", "promote", ["user" => 1], [], "/");
    $this->checkSignal(":Front:Guild:members", "promote", ["user" => 3], [], "/guild/members");
  }
  
  public function testSignalDemote() {
    $this->checkSignal(":Front:Guild:members", "demote", ["user" => 5000], [], "/user/login");
    $this->login("jakub");
    $this->checkSignal(":Front:Guild:members", "demote", ["user" => 5000], [], "/");
    $this->checkSignal(":Front:Guild:members", "demote", ["user" => 1], [], "/");
    $this->checkSignal(":Front:Guild:members", "demote", ["user" => 3], [], "/guild/members");
  }
  
  public function testSignalKick() {
    $this->checkSignal(":Front:Guild:members", "kick", ["user" => 5000], [], "/user/login");
    $this->login("jakub");
    $this->checkSignal(":Front:Guild:members", "kick", ["user" => 5000], [], "/");
    $this->checkSignal(":Front:Guild:members", "kick", ["user" => 1], [], "/");
    $this->checkSignal(":Front:Guild:members", "kick", ["user" => 3], [], "/guild/members");
  }
  
  public function testChat() {
    $this->defaultChecks(":Front:Guild:chat");
  }
}

$test = new GuildPresenterTest();
$test->run();
?>