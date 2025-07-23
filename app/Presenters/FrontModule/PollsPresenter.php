<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\Polls;
use Nextras\Orm\Collection\ICollection;

/**
 * Presenter Polls
 *
 * @author Jakub Konečný
 */
final class PollsPresenter extends BasePresenter {
  public function __construct(private readonly Polls $model) {
    parent::__construct();
  }

  public function renderDefault(): void {
    $this->template->polls = $this->model->all()->orderBy("created", ICollection::DESC);
  }

  protected function getDataModifiedTime(): int {
    $time = 0;
    /** @var \Nexendrie\Orm\Poll $poll */
    foreach($this->template->polls as $poll) {
      $time = max($time, $poll->updated);
    }
    return $time;
  }
}
?>