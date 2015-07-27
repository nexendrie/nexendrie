<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Components\PollControlFactory;

/**
 * Description of PollPresenter
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
    if(!$this->model->exists($id)) $this->forward("notfound");
    $this->template->pollId = $id;
  }
  
  /**
   * @param PollControlFactory $factory
   * @return \Nette\Application\UI\Multiplier
   */
  function createComponentPoll(PollControlFactory $factory) {
    return new \Nette\Application\UI\Multiplier(function ($id) use ($factory) {
      $poll = $factory->create();
      $poll->id = $id;
      return $poll;
    });
  }
}
?>