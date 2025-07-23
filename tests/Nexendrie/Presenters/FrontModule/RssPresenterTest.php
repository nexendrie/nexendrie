<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class RssPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testNews(): void {
    $this->checkRss(":Front:Rss:news");
  }
  
  public function testComments(): void {
    Assert::exception(function() {
      $this->check(":Front:Rss:comments");
    }, BadRequestException::class);
    Assert::exception(function() {
      $this->check(":Front:Rss:comments", ["id" => 5000]);
    }, BadRequestException::class);
    $this->checkRss(":Front:Rss:comments", ["id" => 1]);
  }
}

$test = new RssPresenterTest();
$test->run();
?>