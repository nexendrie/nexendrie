<?php
namespace Nexendrie;

use Nette\Utils\Arrays;

/**
 * Polls Model
 *
 * @author Jakub Konečný
 */
class Polls extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nexendrie\Profile */
  protected $profileModel;
  /** @var \Nexendrie\Locale */
  protected $localeModel;
  /** @var \Nette\Security\User */
  protected $user;
  
  /**
   * @param \Nette\Database\Context $db
   * @param \Nexendrie\Profile $profileModel
   * @param \Nexendrie\Locale $localeModel
   */
  function __construct(\Nette\Database\Context $db, \Nexendrie\Profile $profileModel, \Nexendrie\Locale $localeModel) {
    $this->db = $db;
    $this->profileModel = $profileModel;
    $this->localeModel = $localeModel;
  }
  
  /**
   * @param \Nette\Security\User $user
   * @return void
   */
  function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * Get list of all polls
   * 
   * @return array
   */
  function all() {
    $return = array();
    $polls = $this->db->table("polls")->order("added DESC");
    foreach($polls as $poll) {
      $p = new \stdClass;
      foreach($poll as $key => $value) {
        if($key === "text") {
          $p->$key = substr($value, 0 , 150);
          continue;
        } elseif($key === "author") {
          $user = $this->profileModel->getNames($value);
          $p->$key = $user->publicname;
          $key .= "_username";
          $p->$key = $user->username;
        } elseif($key === "added") {
          $p->$key = $this->localeModel->formatDateTime($value);
        } else {
          $p->$key = $value;
        }
      }
      $return[] = $p;
    }
    return $return;
  }
  
  /**
   * Show specified poll
   * 
   * @param int $id Poll's id
   * @param bool $parseAnswers
   * @return \stdClass
   * @throws \Nette\Application\ForbiddenRequestException
   */
  function view($id, $parseAnswers = false) {
    $poll = $this->db->table("polls")->get($id);
    if(!$poll) throw new \Nette\Application\ForbiddenRequestException("Specified poll does not exist.");
    $return = new \stdClass;
    foreach($poll as $key => $value) {
      if($key === "author") {
        $user = $this->profileModel->getNames($value);
        $return->$key = $user->publicname;
        $key .= "_username";
        $return->$key = $user->username;
      } elseif($key === "added") {
        $return->$key = $this->localeModel->formatDateTime($value);
      } elseif($key === "answers" AND $parseAnswers) {
        $return->$key = explode("\n", $value);
      } else {
        $return->$key = $value;
      }
    }
    return $return;
  }
  
  /**
   * Add poll
   * 
   * @param \Nette\Utils\ArrayHash $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @return void
   */
  function add(\Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("poll", "add")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for adding news.", 403);
    $data["author"] = $this->user->id;
    $data["added"] = time();
    $this->db->query("INSERT INTO polls", $data);
  }
  
  /**
   * Check whetever specified poll exists
   * 
   * @param int $id News' id
   * @return bool
   */
  function exists($id) {
    $row = $this->db->table("polls")
      ->where("id", $id);
    return (bool) $row->count("*");
  }
  
  /**
   * Edit specified poll
   * 
   * @param int $id Poll's id
   * @param \Nette\Utils\ArrayHash $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @throws \Nette\ArgumentOutOfRangeException
   */
  function edit($id, \Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("poll", "add")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for editing polls.", 403);
    if(!$this->exists($id)) throw new \Nette\ArgumentOutOfRangeException("Specified news does not exist");
    $this->db->query("UPDATE polls SET ? WHERE id=?", $data, $id);
  }
  
  /**
   * Check whetever the user can vote in specified poll
   * 
   * @param int $id Poll's id
   * @return bool
   */
  function canVote($id) {
    if(!$this->user->isLoggedIn()) return false;
    elseif(!$this->user->isAllowed("poll", "vote")) return false;
    $row = $this->db->table("poll_votes")
      ->where("poll", $id)
      ->where("user", $this->user->id);
    return !($row->count("*") > 0 );
  }
  
  /**
   * Vote in a poll
   * 
   * @param int $pollId
   * @param int $answer
   * @throws \Nette\InvalidArgumentException
   * @throws \Nette\Application\ForbiddenRequestException
   * @throws PollVotingException
   * @return void
   */
  function vote($pollId, $answer) {
    if(!$this->exists($pollId)) throw new \Nette\InvalidArgumentException("Specified poll does not exist.");
    if(!$this->canVote($pollId)) throw new \Nette\Application\ForbiddenRequestException("You can't vote in this poll.", 403);
    $poll = $this->view($pollId, true);
    if($answer > count($poll->answers)) throw new PollVotingException("The poll has less then $answer answers.");
    $data = array(
      "poll" => $pollId, "user" => $this->user->id, "answer" => $answer, "voted" => time()
    );
    $this->db->query("INSERT INTO poll_votes", $data);
  }
  
  /**
   * Get votes for a poll
   * 
   * @param int $poll Poll's id
   * @return array
   */
  function getVotes($poll) {
    $return = array("total" => 0, "answers" => array());
    $votes = $this->db->table("poll_votes")
      ->where("poll", $poll);
    if($votes->count() > 0) {
      $return["total"] = $votes->count();
      foreach($votes as $vote) {
        $count = Arrays::get($return["answers"], $vote->answer, 0);
        $return["answers"][$vote->answer] = $count + 1;
      }
    }
    return $return;
  }
}

/**
 * Poll voting exception
 * 
 * @author Jakub Konečný
 */
class PollVotingException extends \Exception {
  
}
?>