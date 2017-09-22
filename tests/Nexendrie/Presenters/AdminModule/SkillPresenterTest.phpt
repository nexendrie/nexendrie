<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class SkillPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testNew() {
    $this->defaultChecks(":Admin:Skill:new");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Skill:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Skill:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new SkillPresenterTest();
$test->run();
?>