<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class ArticlePresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault(): void {
    $this->defaultChecks(":Admin:Article:default");
  }
  
  public function testAdd(): void {
    $this->defaultChecks(":Admin:Article:add");
  }
  
  public function testEdit(): void {
    $this->defaultChecks(":Admin:Article:edit", ["id" => 1]);
    Assert::exception(function() {
      $this->check(":Admin:Article:edit", ["id" => 5000]);
    }, BadRequestException::class);
  }
}

$test = new ArticlePresenterTest();
$test->run();
?>