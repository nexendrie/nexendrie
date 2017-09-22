<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert,
    Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class RssPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testNews() {
    $this->checkRss(":Front:Rss:news");
  }
  
  public function testComments() {
    Assert::exception(function() {
      $this->check(":Front:Rss:comments");
    }, BadRequestException::class);
    Assert::exception(function() {
      $this->check(":Front:Rss:comments", ["article" => 5000]);
    }, BadRequestException::class);
    $this->checkRss(":Front:Rss:comments", ["article" => 1]);
  }
}

$test = new RssPresenterTest();
$test->run();
?>