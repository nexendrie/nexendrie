<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Message as MessageEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Messenger Model
 *
 * @author Jakub Konečný
 */
class Messenger {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var Profile */
  protected $profileModel;
  
  use \Nette\SmartObject;
  
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
   * @return MessageEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  function inbox(): ICollection {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    return $this->orm->messages->findByTo($this->user->id)->orderBy("sent", ICollection::DESC);
  }
  
  /**
   * Get list of sent messages
   * 
   * @return MessageEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  function outbox(): ICollection {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    return $this->orm->messages->findByFrom($this->user->id)->orderBy("sent", ICollection::DESC);
  }
  
  /**
   * Show specified message
   * 
   * @param int $id Message's id
   * @return MessageEntity
   * @throws AuthenticationNeededException
   * @throws MessageNotFoundException
   * @throws AccessDeniedException
   */
  function show(int $id): MessageEntity {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    $message = $this->orm->messages->getById($id);
    if(!$message) throw new MessageNotFoundException("Message not found.");
    if($message->from->id != $this->user->id AND $message->to->id != $this->user->id) {
      throw new AccessDeniedException("You can't see this message.");
    }
    if(!$message->read AND $message->to->id === $this->user->id) {
      $message->read = true;
      $this->orm->messages->persistAndFlush($message);
    }
    return $message;
  }
  
  /**
   * Get list of users
   * 
   * @return array id => publicname
   */
  function usersList(): array {
    return $this->orm->users->findBy(
        ["id!=" => $this->user->id]
    )->fetchPairs("id", "publicname");
  }
  
  /**
   * Sends new message
   * 
   * @param array $data
   * @return void
   */
  function send(array $data) {
    $message = new MessageEntity;
    $this->orm->messages->attach($message);
    $message->subject = $data["subject"];
    $message->text = $data["text"];
    $message->from = $this->orm->users->getById($this->user->id);
    $message->from->lastActive = time();
    $message->to = $data["to"];
    $this->orm->messages->persistAndFlush($message);
  }
}
?>