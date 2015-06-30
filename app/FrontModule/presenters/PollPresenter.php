<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Description of PollPresenter
 *
 * @author Jakub Konečný
 */
class PollPresenter extends BasePresenter {
  /** @var \Nexendrie\Polls */
  protected $model;
  
  function __construct(\Nexendrie\Polls $model) {
    $this->model = $model;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderView($id) {
    try {
      $poll = $this->model->view($id);
      $poll->answers = explode("\n", $poll->answers);
      $this->template->poll = $poll;
    } catch(\Nette\Application\ForbiddenRequestException $e) {
      $this->forward("notfound");
    }
  }
}
?>