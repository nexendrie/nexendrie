<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class EventPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault(): void {
    $this->defaultChecks(":Admin:Event:default");
  }
  
  public function testAdd(): void {
    $this->defaultChecks(":Admin:Event:add");
  }
  
  public function testEdit(): void {
    $this->defaultChecks(":Admin:Event:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Event:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
  
  public function testDelete(): void {
    $this->checkRedirect(":Admin:Event:delete", "/user/login", ["id" => 1]);
    $this->login("kazimira");
    $this->checkRedirect(":Admin:Event:delete", "/", ["id" => 1]);
    $this->login();
    Assert::exception(function() {
      $this->check(":Admin:Event:delete", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkRedirect(":Admin:Event:delete", "/admin/", ["id" => 1]);
  }
}

$test = new EventPresenterTest();
$test->run();
?>