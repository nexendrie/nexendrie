<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

class ItemSetPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew() {
    $this->defaultChecks(":Admin:ItemSet:new");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:ItemSet:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:ItemSet:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new ItemSetPresenterTest;
$test->run();
?>