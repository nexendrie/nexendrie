<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class ArticlePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testView() {
    Assert::exception(function() {
      $this->checkAction(":Front:Article:view", ["id" => 50]);
    }, BadRequestException::class);
    $this->checkAction(":Front:Article:view", ["id" => 1]);
    $this->login();
    $this->checkAction(":Front:Article:view", ["id" => 1]);
  }
}

$test = new ArticlePresenterTest();
$test->run();
?>