<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\ContentReport;
use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class ModerationTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Moderation */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Moderation::class);
  }
  
  public function testReportComment() {
    Assert::exception(function() {
      $this->model->reportComment(50);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->reportComment(50);
    }, CommentNotFoundException::class);
    $this->model->reportComment(1);
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    $report = $orm->contentReports->getBy(["comment" => 1, "handled" => false,]);
    Assert::type(ContentReport::class, $report);
    Assert::exception(function() {
      $this->model->reportComment(1);
    }, ContentAlreadyReportedException::class);
    $orm->contentReports->removeAndFlush($report);
  }
}

$test = new ModerationTest();
$test->run();
?>