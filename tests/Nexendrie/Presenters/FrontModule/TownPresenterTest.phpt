<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class TownPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkRedirect(":Front:Town:default", "/user/login");
    $this->login();
    $this->checkAction(":Front:Town:default");
  }
  
  public function testList() {
    $this->checkAction(":Front:Town:list");
  }
  
  public function testDetail() {
    Assert::exception(function() {
      $this->check(":Front:Town:detail", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Town:detail", ["id" => 1]);
    $this->login();
    $this->checkAction(":Front:Town:detail", ["id" => 1]);
  }
  
  public function testMove() {
    $this->checkRedirect(":Front:Town:move", "/user/login", ["id" => 5000]);
    $this->login("kazimira");
    $this->checkRedirect(":Front:Town:move", "/", ["id" => 1]);
    $this->login();
    $this->checkRedirect(":Front:Town:move", "/", ["id" => 5000]);
    $this->checkRedirect(":Front:Town:move", "/", ["id" => 2]);
  }
  
  public function testFound() {
    $this->checkRedirect(":Front:Town:found", "/user/login");
    $this->login("jakub");
    $this->checkRedirect(":Front:Town:found", "/");
    $this->login();
    $this->checkAction(":Front:Town:found");
  }
  
  public function testElections() {
    $this->checkRedirect(":Front:Town:elections", "/user/login");
    $this->login("kazimira");
    $this->checkRedirect(":Front:Town:elections", "/");
    $this->login();
    $this->checkAction(":Front:Town:elections");
  }
  
  public function testChat() {
    $this->checkRedirect(":Front:Town:chat", "/user/login");
    $this->login();
    $this->checkAction(":Front:Town:chat");
  }
}

$test = new TownPresenterTest();
$test->run();
?>