<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Description of PollPresenter
 *
 * @author Jakub Konečný
 */
class PollPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Polls */
  protected $model;
  
  function __construct(\Nexendrie\Model\Polls $model) {
    $this->model = $model;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderView($id) {
    if(!$this->model->exists($id)) $this->forward("notfound");
    $this->template->pollId = $id;
  }
  
  /**
   * @return \Nette\Application\UI\Multiplier
   */
  function createComponentPoll() {
    $p = $this;
    return new \Nette\Application\UI\Multiplier(function ($id) use ($p) {
      $poll = $p->context->getService("poll");
      $poll->id = $id;
      return $poll;
    });
  }
}
?>