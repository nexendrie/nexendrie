<?php
namespace Nexendrie;

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
  /** @var \Nexendrie\Profile */
  protected $profileModel;
  
  /**
   * @param \Nette\Database\Context $db
   * @param \Nette\Security\User $user
   * @param \Nexendrie\Profile $profileModel
   */
  function __construct(\Nette\Database\Context $db, \Nette\Security\User $user, \Nexendrie\Profile $profileModel) {
    $this->db = $db;
    $this->user = $user;
    $this->profileModel = $profileModel;
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
          $user = $this->profileModel->getNames($value);
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
          $user = $this->profileModel->getNames($value);
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
      throw new \Nette\Application\ForbiddenRequestException("You can't see this message.", 403);
    }
    $return = new \stdClass;
    foreach($message as $key => $value) {
      if($key === "from" OR $key === "to") {
        $return->{$key . "_id"} = $value;
        $user = $this->profileModel->getNames($value);
        $return->$key = $user->publicname;
        $return->{$key . "_username"} = $user->username;
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
    $users = $this->profileModel->getAllNames();
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