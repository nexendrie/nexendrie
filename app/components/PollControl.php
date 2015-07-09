<?php
namespace Nexendrie\Components;

use Nette\Utils\Arrays;

/**
 * Poll Control
 *
 * @author Jakub Konečný
 */
class PollControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  /** @var \Nexendrie\ILocale */
  protected $localeModel;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \stdClass */
  protected $poll;
  /** @var int */
  protected $id;
  
  /**
   * @param \Nexendrie\Model\Profile $profileModel
   * @param \Nexendrie\ILocale $localeModel
   * @param \Nette\Security\User $user
   * @param \Nette\Database\Context $db
   */
  function __construct(\Nexendrie\Model\Profile $profileModel, \Nexendrie\ILocale $localeModel, \Nette\Security\User $user, \Nette\Database\Context $db) {
    $this->profileModel = $profileModel;
    $this->localeModel = $localeModel;
    $this->user = $user;
    $this->db = $db;
  }
  
  /**
   * @return \stdClass
   * @throws \Nette\Application\BadRequestException
   */
  function getPoll() {
    if(isset($this->poll)) return $this->poll;
    $poll = $this->db->table("polls")->get($this->id);
    if(!$poll) throw new \Nette\Application\BadRequestException("Specified poll does not exist.");
    $return = new \stdClass;
    foreach($poll as $key => $value) {
      if($key === "author") {
        $user = $this->profileModel->getNames($value);
        $return->$key = $user->publicname;
        $key .= "_username";
        $return->$key = $user->username;
      } elseif($key === "added") {
        $return->$key = $this->localeModel->formatDateTime($value);
      } elseif($key === "answers") {
        $return->$key = explode("\n", $value);
      } else {
        $return->$key = $value;
      }
    }
    $this->poll = $return;
    return $return;
  }
  
  /**
   * @param int $id
   */
  function setId($id) {
    try {
      $this->id = $id;
      $this->getPoll();
    } catch(\Nette\Application\BadRequestException $e) {
      throw $e;
    }
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
    $poll = $this->getPoll();
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
    if(!$this->canVote()) throw new \Nette\Application\ForbiddenRequestException("You can't vote in this poll.", 403);
    $poll = $this->getPoll();
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