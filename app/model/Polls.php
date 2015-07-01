<?php
namespace Nexendrie;

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
}

/**
 * Poll voting exception
 * 
 * @author Jakub Konečný
 */
class PollVotingException extends \Exception {
  
}
?>