<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class AdventurePresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew() {
    $this->defaultChecks(":Admin:Adventure:new");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Adventure:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Adventure:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new AdventurePresenterTest();
$test->run();
?>