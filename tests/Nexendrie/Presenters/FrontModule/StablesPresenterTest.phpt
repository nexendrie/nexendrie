<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class StablesPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkRedirect(":Front:Stables:default", "/user/login");
    $this->login();
    $this->checkAction(":Front:Stables:default");
  }
  
  public function testManage() {
    $this->checkRedirect(":Front:Stables:manage", "/user/login", ["id" => 5000]);
    $this->login();
    Assert::exception(function() {
      $this->check(":Front:Stables:manage", ["id" => 5000]);
    }, BadRequestException::class);
    Assert::exception(function() {
      $this->check(":Front:Stables:manage", ["id" => 1]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Stables:manage", ["id" => 2]);
  }
  
  public function testTrain() {
    $this->checkRedirect(":Front:Stables:train", "/user/login", ["id" => 5000]);
    $this->login();
    Assert::exception(function() {
      $this->check(":Front:Stables:train", ["id" => 5000]);
    }, BadRequestException::class);
    Assert::exception(function() {
      $this->check(":Front:Stables:train", ["id" => 1]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Stables:train", ["id" => 2]);
  }
}

$test = new StablesPresenterTest();
$test->run();
?>