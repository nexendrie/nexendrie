<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class TownPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew() {
    $this->defaultChecks(":Admin:Town:new");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Town:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Town:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new TownPresenterTest();
$test->run();
?>