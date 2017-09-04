<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

class PollsPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault() {
    $this->defaultChecks(":Admin:Polls:default");
  }
  
  public function testAdd() {
    $this->defaultChecks(":Admin:Polls:add");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Polls:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Polls:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new PollsPresenterTest;
$test->run();
?>