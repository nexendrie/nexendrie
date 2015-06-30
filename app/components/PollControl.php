<?php
namespace Nexendrie;

/**
 * Poll Control
 *
 * @author Jakub Konečný
 */
class PollControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Polls */ 
  protected $model;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $id;
  
  function __construct(\Nexendrie\Polls $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->model->user = $this->user = $user;
  }
  
  /**
   * @param int $id
   */
  function setId($id) {
    $this->id = $id;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/poll.latte");
    $poll = $this->model->view($this->id);
    $poll->answers = explode("\n", $poll->answers);
    $this->template->poll = $poll;
    $template->canVote = $this->user->isAllowed("poll", "vote");
    $template->canEdit = $this->user->isAllowed("poll", "add");
    $template->render();
  }
}
?>