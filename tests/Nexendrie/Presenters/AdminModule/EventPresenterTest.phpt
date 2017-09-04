<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

class EventPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault() {
    $this->defaultChecks(":Admin:Event:default");
  }
  
  public function testAdd() {
    $this->defaultChecks(":Admin:Event:add");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Event:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Event:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new EventPresenterTest;
$test->run();
?>