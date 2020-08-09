<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nextras\Orm\Collection\ICollection;

/**
 * Presenter Polls
 *
 * @author Jakub Konečný
 */
final class PollsPresenter extends BasePresenter {
  protected \Nexendrie\Model\Polls $model;

  public function __construct(\Nexendrie\Model\Polls $model) {
    parent::__construct();
    $this->model = $model;
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