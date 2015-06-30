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
    $poll = $this->model->view($this->id, true);
    $this->template->poll = $poll;
    $template->canVote = $this->model->canVote($this->id);
    $template->canEdit = $this->user->isAllowed("poll", "add");
    $template->render();
  }
  
  /**
   * @param int $answer
   * @return void
   */
  function handleVote($answer) {
    try {
      $this->model->vote($this->id, $answer);
      $this->presenter->flashMessage("Hlas uložen.");
    } catch (\Nette\InvalidArgumentException $e) {
      $this->presenter->flashMessage("Zadaná anketa neexistuje.");
    } catch (\Nette\Application\ForbiddenRequestException $e) {
      $this->presenter->flashMessage("Nemůžeš hlasovat v této anketě.");
    } catch (\Nexendrie\PollVotingException $e) {
      $this->presenter->flashMessage("Neplatná volba.");
    }
  }
}
?>