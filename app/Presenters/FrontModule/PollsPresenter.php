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
  /** @var \Nexendrie\Model\Polls */
  protected $model;

  public function __construct(\Nexendrie\Model\Polls $model) {
    parent::__construct();
    $this->model = $model;
  }

  public function renderDefault(): void {
    $this->template->polls = $this->model->all()->orderBy("added", ICollection::DESC);
  }
}
?>