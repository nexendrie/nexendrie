<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class MealPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew() {
    $this->defaultChecks(":Admin:Meal:new");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Meal:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Meal:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new MealPresenterTest();
$test->run();
?>