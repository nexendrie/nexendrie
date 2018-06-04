<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class UserPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault() {
    $this->defaultChecks(":Admin:User:default");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:User:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:User:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
  
  public function testBan() {
    $this->defaultChecks(":Admin:User:ban", ["id" => 2]);
    $this->checkRedirect(":Admin:User:ban", "/admin/users", ["id" => 0]);
    $this->checkRedirect(":Admin:User:ban", "/admin/users", ["id" => 1]);
    $this->checkRedirect(":Admin:User:ban", "/admin/users", ["id" => 5000]);
  }
}

$test = new UserPresenterTest();
$test->run();
?>