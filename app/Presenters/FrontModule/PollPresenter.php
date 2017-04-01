<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\PollControlFactory;

/**
 * Presenter Poll
 *
 * @author Jakub Konečný
 */
class PollPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Polls @autowire */
  protected $model;
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function renderView(int $id): void {
    if(!$this->model->exists($id)) {
      throw new \Nette\Application\BadRequestException;
    }
    $this->template->pollId = $id;
  }
  
  /**
   * @param PollControlFactory $factory
   * @return \Nette\Application\UI\Multiplier
   */
  protected function createComponentPoll(PollControlFactory $factory): \Nette\Application\UI\Multiplier {
    return new \Nette\Application\UI\Multiplier(function ($id) use ($factory) {
      $poll = $factory->create();
      $poll->id = $id;
      return $poll;
    });
  }
}
?>