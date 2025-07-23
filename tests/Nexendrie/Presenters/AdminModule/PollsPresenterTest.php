<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class PollsPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault(): void {
    $this->defaultChecks(":Admin:Polls:default");
  }
  
  public function testAdd(): void {
    $this->defaultChecks(":Admin:Polls:add");
  }
  
  public function testEdit(): void {
    $this->defaultChecks(":Admin:Polls:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Polls:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new PollsPresenterTest();
$test->run();
?>