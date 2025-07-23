<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IPollControlFactory;
use Nexendrie\Components\PollControl;
use Nexendrie\Model\PollNotFoundException;
use Nexendrie\Model\Polls;

/**
 * Presenter Poll
 *
 * @author Jakub Konečný
 */
final class PollPresenter extends BasePresenter {
  protected \Nexendrie\Orm\Poll $poll;
  
  public function __construct(private readonly Polls $model) {
    parent::__construct();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderView(int $id): void {
    try {
      $this->poll = $this->model->view($id);
    } catch(PollNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
    $this->template->pollId = $id;
  }
  
  protected function createComponentPoll(IPollControlFactory $factory): \Nette\Application\UI\Multiplier {
    return new \Nette\Application\UI\Multiplier(function($id) use ($factory): PollControl {
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