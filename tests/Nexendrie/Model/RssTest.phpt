<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nexendrie\Rss\Bridges\NetteApplication\RssResponse;

require __DIR__ . "/../../bootstrap.php";

final class RssTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Rss */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Rss::class);
  }
  
  public function testNewsFeed() {
    $feed = $this->model->newsFeed();
    Assert::type(RssResponse::class, $feed);
  }
  
  public function testCommentsFeed() {
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