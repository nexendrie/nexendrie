<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Message as MessageEntity;

/**
 * Messenger Model
 *
 * @author Jakub Konečný
 */
class Messenger extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  
  /**
   * @param \Nexendrie\Orm\Model $orm
   * @param \Nette\Security\User $user
   * @param \Nexendrie\Model\Profile $profileModel
   */
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user, Profile $profileModel) {
    $this->orm = $orm;
    $this->user = $user;
    $this->profileModel = $profileModel;
  }
  
  /**
   * Get list of received messages
   * 
   * @return MessageEntity[]
   * @throws \Nette\Application\ForbiddenRequestException
   */
  function inbox() {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    return $this->orm->messages->findByTo($this->user->id);
  }
  
  /**
   * Get list of sent messages
   * 
   * @return MessageEntity[]
   * @throws \Nette\Application\ForbiddenRequestException
   */
  function outbox() {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    return $this->orm->messages->findByFrom($this->user->id);
  }
  
  /**
   * Show specified message
   * 
   * @param int $id Message's id
   * @return MessageEntity
   * @throws \Nette\Application\ForbiddenRequestException
   * @throws \Nette\Application\BadRequestException
   */
  function show($id) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException("This action requires authentication.", 401);
    $message = $this->orm->messages->getById($id);
    if(!$message) throw new \Nette\Application\BadRequestException("Message not found.");
    if($message->from->id != $this->user->id AND $message->to->id != $this->user->id) {
      throw new \Nette\Application\ForbiddenRequestException("You can't see this message.", 403);
    }
    return $message;
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
    $message = new MessageEntity;
    $message->subject = $data["subject"];
    $message->text = $data["text"];
    $message->sent = time();
    $message->from = $this->orm->users->getById($this->user->id);
    $message->to = $this->orm->users->getById($data["to"]);
    $this->orm->messages->persistAndFlush($message);
  }
}
?>