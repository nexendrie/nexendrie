<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class ArticlePresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault() {
    $this->defaultChecks(":Admin:Article:default");
  }
  
  public function testAdd() {
    $this->defaultChecks(":Admin:Article:add");
  }
  
  public function testEdit() {
    $this->defaultChecks(":Admin:Article:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Article:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new ArticlePresenterTest();
$test->run();
?>