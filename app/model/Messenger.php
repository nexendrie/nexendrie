<?php
namespace Nexendrie;

use Nette\Utils\Arrays;

/**
 * Messenger Model
 *
 * @author Jakub Konečný
 */
class Messenger extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var array */
  protected $names = array();
  
  function __construct(\Nette\Database\Context $db, \Nette\Security\User $user) {
    $this->db = $db;
    $this->user = $user;
  }
  
  function getNames($id) {
    $user = Arrays::get($this->names, $id, false);
    if(!$user) {
      $user = $this->db->table("users")->get($id);
      $this->names[$id] = (object) array(
        "username" => $user->username, "publicname" => $user->publicname
      );
    }
    return $user;
  }
  
  /**
   * Get list of received messages
   * 
   * @return array
   * @throws \Nette\Application\ForbiddenRequestException
   */
  function inbox() {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    $return = $users = array();
    $messages = $this->db->table("messages")
      ->where("to", $this->user->id);
    foreach($messages as $message) {
      $m = new \stdClass;
      foreach($message as $key => $value) {
        if($key === "from" OR $key === "to") {
          $user = $this->getNames($value);
          $m->$key = $user->publicname;
          $key .= "_username";
          $m->$key = $user->username;
        } else {
          $m->$key = $value;
        }
      }
      $return[] = $m;
    }
    return $return;
  }
  
  /**
   * Get list of sent messages
   * 
   * @return array
   * @throws \Nette\Application\ForbiddenRequestException
   */
  function outbox() {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    $return = $users = array();
    $messages = $this->db->table("messages")
      ->where("from", $this->user->id);
    foreach($messages as $message) {
      $m = new \stdClass;
      foreach($message as $key => $value) {
        if($key === "from" OR $key === "to") {
          $user = $this->getNames($value);
          $m->$key = $user->publicname;
          $key .= "_username";
          $m->$key = $user->username;
        } else {
          $m->$key = $value;
        }
      }
      $return[] = $m;
    }
    return $return;
  }
  
  /**
   * Show specified message
   * 
   * @param int $id Message's id
   * @return \stdClass
   * @throws \Nette\Application\ForbiddenRequestException
   * @throws \Nette\Application\BadRequestException
   */
  function show($id) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException("This action requires authentication.", 401);
    $message = $this->db->table("messages")->get($id);
    if(!$message) throw new \Nette\Application\BadRequestException("Message not found.");
    if($message->from != $this->user->id AND $message->to != $this->user->id) {
      throw new \Nette\Application\ForbiddenRequestException("This action requires authentication.", 403);
    }
    $return = new \stdClass;
    foreach($message as $key => $value) {
      if($key === "from" OR $key === "to") {
        $user = $this->db->table("users")->get($value);
        $return->$key = $user->publicname;
        $key .= "_username";
        $return->$key = $user->username;
      } else {
        $return->$key = $value;
      }
    }
    return $return;
  }
  
  /**
   * Get list of users
   * 
   * @return array id => publicname
   */
  function usersList() {
    $return = array();
    $users = $this->db->table("users");
    foreach($users as $user) {
      if($user->id === $this->user->id) continue;
      $return[$user->id] = $user->publicname;
    }
    return $return;
  }
  
  /**
   * Sends new message
   * 
   * @param \Nette\Utils\ArrayHash $data
   * @return void
   */
  function send(\Nette\Utils\ArrayHash $data) {
    $data["from"] = $this->user->id;
    $this->db->query("INSERT INTO messages", $data);
  }
}
?>