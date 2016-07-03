<?php
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
   */
  function renderView($id) {
    if(!$this->model->exists($id)) throw new \Nette\Application\BadRequestException;
    $this->template->pollId = $id;
  }
  
  /**
   * @param PollControlFactory $factory
   * @return \Nette\Application\UI\Multiplier
   */
  protected function createComponentPoll(PollControlFactory $factory) {
    return new \Nette\Application\UI\Multiplier(function ($id) use ($factory) {
      $poll = $factory->create();
      $poll->id = $id;
      return $poll;
    });
  }
}
?>