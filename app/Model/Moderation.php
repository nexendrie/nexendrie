<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\ContentReport;

final class Moderation {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;

  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }

  /**
   * @throws AuthenticationNeededException
   * @throws CommentNotFoundException
   * @throws ContentAlreadyReportedException
   */
  public function reportComment(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $comment = $this->orm->comments->getBy(["id" => $id, "deleted" => false]);
    if($comment === null) {
      throw new CommentNotFoundException();
    }
    $report = $this->orm->contentReports->getBy(["comment" => $id, "handled" => false]);
    if($report !== null) {
      throw new ContentAlreadyReportedException();
    }
    $report = new ContentReport();
    $this->orm->contentReports->attach($report);
    $report->comment = $comment;
    $report->user = $this->user->id;
    $this->orm->contentReports->persistAndFlush($report);
  }
}
?>