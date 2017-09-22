<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class AdventureEnemiesPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testList() {
    $this->defaultChecks(":Admin:AdventureEnemies:list", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:AdventureEnemies:list", ["id" => 5000]);
    }, BadRequestException::class);
  }
  
  public function testAdd() {
    $this->defaultChecks(":Admin:AdventureEnemies:add", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:AdventureEnemies:add", ["id" => 5000]);
    }, BadRequestException::class);
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:AdventureEnemies:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:AdventureEnemies:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new AdventureEnemiesPresenterTest();
$test->run();
?>