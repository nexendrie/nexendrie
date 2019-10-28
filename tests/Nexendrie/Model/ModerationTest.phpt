<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\ContentReport;
use Nextras\Orm\Collection\ICollection;
use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class ModerationTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Moderation */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Moderation::class);
  }
  
  public function testModeration() {
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

    $this->logout();
    Assert::exception(function() {
      $this->model->ignoreReport(50);
    }, AuthenticationNeededException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      $this->model->ignoreReport(50);
    }, MissingPermissionsException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->ignoreReport(50);
    }, ContentReportNotFoundException::class);
    $this->model->ignoreReport($report->id);
    Assert::true($report->handled);
    Assert::false($report->comment->deleted);
    Assert::exception(function() use($report) {
      $this->model->ignoreReport($report->id);
    }, ContentReportNotFoundException::class);
    $report->handled = false;
    $orm->contentReports->persistAndFlush($report);

    $this->logout();
    Assert::exception(function() {
      $this->model->deleteContent(50);
    }, AuthenticationNeededException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      $this->model->deleteContent(50);
    }, MissingPermissionsException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->deleteContent(50);
    }, ContentReportNotFoundException::class);
    $this->model->deleteContent($report->id);
    Assert::true($report->handled);
    Assert::true($report->comment->deleted);
    $report->comment->deleted = false;
    $orm->comments->persistAndFlush($report->comment);

    $orm->contentReports->removeAndFlush($report);
  }

  public function testGetReportedContent() {
    $result = $this->model->getReportedContent();
    Assert::type(ICollection::class, $result);
  }
}

$test = new ModerationTest();
$test->run();
?>