<?php
namespace Nexendrie;

use Nette\Utils\Arrays;

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
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var int */
  protected $id;
  
  function __construct(\Nexendrie\Polls $model, \Nette\Security\User $user, \Nette\Database\Context $db) {
    $this->model = $model;
    $this->user = $user;
    $this->db = $db;
  }
  
  /**
   * @param int $id
   */
  function setId($id) {
    $this->id = $id;
  }
  
  /**
   * Get votes for the poll
   * 
   * @return array
   */
  function getVotes() {
    $return = array("total" => 0, "answers" => array());
    $votes = $this->db->table("poll_votes")
      ->where("poll", $this->id);
    if($votes->count() > 0) {
      $return["total"] = $votes->count();
      foreach($votes as $vote) {
        $count = Arrays::get($return["answers"], $vote->answer, 0);
        $return["answers"][$vote->answer] = $count + 1;
      }
    }
    return $return;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/poll.latte");
    $poll = $this->model->view($this->id, true);
    $this->template->poll = $poll;
    $votes = $this->getVotes();
    for($i = 1; $i <= count($poll->answers); $i++) {
      if(!isset($votes["answers"][$i])) $votes["answers"][$i] = 0;
    }
    $this->template->votes = $votes;
    $template->canVote = $this->canVote();
    $template->canEdit = $this->user->isAllowed("poll", "add");
    $template->render();
  }
  
  /**
   * Check whetever the user can vote in the poll
   * 
   * @return bool
   */
  function canVote() {
    if(!$this->user->isLoggedIn()) return false;
    elseif(!$this->user->isAllowed("poll", "vote")) return false;
    $row = $this->db->table("poll_votes")
      ->where("poll", $this->id)
      ->where("user", $this->user->id);
    return !($row->count("*") > 0 );
  }
  
  /**
   * Vote in the poll
   * 
   * @param int $answer
   * @throws \Nette\InvalidArgumentException
   * @throws \Nette\Application\ForbiddenRequestException
   * @throws PollVotingException
   * @return void
   */
  function vote($answer) {
    if(!$this->model->exists($this->id)) throw new \Nette\InvalidArgumentException("Specified poll does not exist.");
    if(!$this->canVote($this->id)) throw new \Nette\Application\ForbiddenRequestException("You can't vote in this poll.", 403);
    $poll = $this->model->view($this->id, true);
    if($answer > count($poll->answers)) throw new PollVotingException("The poll has less then $answer answers.");
    $data = array(
      "poll" => $this->id, "user" => $this->user->id, "answer" => $answer, "voted" => time()
    );
    $this->db->query("INSERT INTO poll_votes", $data);
  }
  
  /**
   * @param int $answer
   * @return void
   */
  function handleVote($answer) {
    try {
      $this->vote($answer);
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