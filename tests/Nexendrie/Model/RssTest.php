<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nexendrie\Rss\Bridges\NetteApplication\RssResponse;

require __DIR__ . "/../../bootstrap.php";

final class RssTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  protected Rss $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Rss::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testNewsFeed(): void {
    $feed = $this->model->newsFeed();
    Assert::type(RssResponse::class, $feed);
  }
  
  public function testCommentsFeed(): void {
    $feed = $this->model->commentsFeed(1);
    Assert::type(RssResponse::class, $feed);
    Assert::exception(function() {
      $this->model->commentsFeed(50);
    }, ArticleNotFoundException::class);
  }
}

$test = new RssTest();
$test->run();
?>