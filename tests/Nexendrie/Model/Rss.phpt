<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nexendrie\Rss\RssResponse;

require __DIR__ . "/../../bootstrap.php";

class RssTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Rss */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Rss::class);
  }
  
  function testNewsFeed() {
    $feed = $this->model->newsFeed();
    Assert::type(RssResponse::class, $feed);
  }
  
  function testCommentsFeed() {
    $feed = $this->model->commentsFeed(1);
    Assert::type(RssResponse::class, $feed);
    Assert::exception(function() {
      $this->model->commentsFeed(50);
    }, ArticleNotFoundException::class);
  }
}

$test = new RssTest;
$test->run();
?>