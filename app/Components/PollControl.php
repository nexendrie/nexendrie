<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nette\Utils\Arrays,
    Nexendrie\Model\PollVotingException,
    Nexendrie\Model\PollNotFoundException,
    Nexendrie\Model\AccessDeniedException,
    Nexendrie\Orm\Poll as PollEntity,
    Nexendrie\Orm\PollVote as PollVoteEntity;

/**
 * Poll Control
 *
 * @author Jakub Konečný
 */
class PollControl extends \Nette\Application\UI\Control {
  /** @var \Nette\Security\User */
  protected $user;
  /** @var \Nexendrie\Orm\Model $orm */
  protected $orm;
  /** @var PollEntity */
  protected $poll;
  /** @var int */
  protected $id;
  
  /**
   * @param \Nette\Security\User $user
   * @param \Nexendrie\Orm\Model $orm
   */
  function __construct(\Nette\Security\User $user, \Nexendrie\Orm\Model $orm) {
    $this->user = $user;
    $this->orm = $orm;
  }
  
  /**
   * @return PollEntity
   * @throws PollNotFoundException
   */
  function getPoll() {
    if(isset($this->poll)) return $this->poll;
    $poll = $this->orm->polls->getById($this->id);
    if(!$poll) throw new PollNotFoundException("Specified poll does not exist.");
    $this->poll = $poll;
    return $poll;
  }
  
  /**
   * @param int $id
   * @throws PollNotFoundException
   */
  function setId($id) {
    try {
      $this->id = $id;
      $this->getPoll();
    } catch(PollNotFoundException $e) {
      throw $e;
    }
  }
  
  /**
   * Get votes for the poll
   * 
   * @return array
   */
  function getVotes() {
    $return = ["total" => 0, "answers" => []];
    $votes = $this->orm->pollVotes->findByPoll($this->id);
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
    $poll = $this->getPoll();
    $this->template->poll = $poll;
    $votes = $this->getVotes();
    for($i = 1; $i <= count($poll->parsedAnswers); $i++) {
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
  protected function canVote() {
    if(!$this->user->isLoggedIn()) return false;
    elseif(!$this->user->isAllowed("poll", "vote")) return false;
    $row = $this->orm->pollVotes->getByPollAndUser($this->id, $this->user->id);
    return !(bool) $row;
  }
  
  /**
   * Vote in the poll
   * 
   * @param int $answer
   * @throws \Nette\InvalidArgumentException
   * @throws AccessDeniedException
   * @throws PollVotingException
   * @return void
   */
  protected function vote($answer) {
    if(!$this->canVote()) throw new AccessDeniedException("You can't vote in this poll.");
    $poll = $this->getPoll();
    if($answer > count($poll->parsedAnswers)) throw new PollVotingException("The poll has less then $answer answers.");
    $vote = new PollVoteEntity;
    $this->orm->pollVotes->attach($vote);
    $vote->poll = $this->poll;
    $vote->user = $this->user->id;
    $vote->answer = $answer;
    $this->orm->pollVotes->persistAndFlush($vote);
  }
  
  /**
   * @param int $answer
   * @return void
   */
  function handleVote($answer) {
    try {
      $this->vote($answer);
      $this->presenter->flashMessage("Hlas uložen.");
    } catch(\Nette\InvalidArgumentException $e) {
      $this->presenter->flashMessage("Zadaná anketa neexistuje.");
    } catch(AccessDeniedException $e) {
      $this->presenter->flashMessage("Nemůžeš hlasovat v této anketě.");
    } catch(PollVotingException $e) {
      $this->presenter->flashMessage("Neplatná volba.");
    }
  }
}

interface PollControlFactory {
  /** @return PollControl */
  function create();
}
?>