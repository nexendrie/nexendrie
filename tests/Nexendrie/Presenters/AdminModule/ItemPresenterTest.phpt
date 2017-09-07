<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class ItemPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew() {
    $this->defaultChecks(":Admin:Item:new");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Item:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Item:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new ItemPresenterTest;
$test->run();
?>