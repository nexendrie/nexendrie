<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class JobPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew() {
    $this->defaultChecks(":Admin:Job:new");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Job:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Job:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new JobPresenterTest();
$test->run();
?>