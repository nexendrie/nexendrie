<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class TownPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew(): void {
    $this->defaultChecks(":Admin:Town:new");
  }
  
  public function testEdit(): void {
    $this->defaultChecks(":Admin:Town:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Town:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new TownPresenterTest();
$test->run();
?>