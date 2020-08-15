<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IPollControlFactory;
use Nexendrie\Model\PollNotFoundException;

/**
 * Presenter Poll
 *
 * @author Jakub Konečný
 */
final class PollPresenter extends BasePresenter {
  protected \Nexendrie\Model\Polls $model;
  protected \Nexendrie\Orm\Poll $poll;
  
  public function __construct(\Nexendrie\Model\Polls $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderView(int $id): void {
    try {
      $this->poll = $this->model->view($id);
    } catch(PollNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
    $this->template->pollId = $id;
  }
  
  protected function createComponentPoll(IPollControlFactory $factory): \Nette\Application\UI\Multiplier {
    return new \Nette\Application\UI\Multiplier(function($id) use ($factory) {
      $poll = $factory->create();
      $poll->id = (int) $id;
      return $poll;
    });
  }

  protected function getDataModifiedTime(): int {
    if(!isset($this->poll)) {
      return 0;
    }
    return $this->poll->updated;
  }
}
?>