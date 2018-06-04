<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class ShopPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew() {
    $this->defaultChecks(":Admin:Shop:new");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Shop:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Shop:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new ShopPresenterTest();
$test->run();
?>