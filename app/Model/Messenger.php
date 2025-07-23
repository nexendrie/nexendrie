<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Application\LinkGenerator;
use Nexendrie\Orm\Message as MessageEntity;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Structs\Notification;
use Nextras\Orm\Collection\ICollection;

/**
 * Messenger Model
 *
 * @author Jakub Konečný
 */
final class Messenger {
  public function __construct(private readonly ORM $orm, private readonly \Nette\Security\User $user, private readonly LinkGenerator $linkGenerator, private readonly GenericNotificator $notificator) {
  }

  use \Nette\SmartObject;
  
  /**
   * Get list of received messages
   * 
   * @return MessageEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  public function inbox(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    return $this->orm->messages->findByTo($this->user->id)->orderBy("created", ICollection::DESC);
  }
  
  /**
   * Get list of sent messages
   * 
   * @return MessageEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  public function outbox(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    return $this->orm->messages->findByFrom($this->user->id)->orderBy("created", ICollection::DESC);
  }
  
  /**
   * Show specified message
   *
   * @throws AuthenticationNeededException
   * @throws MessageNotFoundException
   * @throws AccessDeniedException
   */
  public function show(int $id): MessageEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    $message = $this->orm->messages->getById($id);
    if($message === null) {
      throw new MessageNotFoundException("Message not found.");
    }
    if($message->from->id !== $this->user->id && $message->to->id !== $this->user->id) {
      throw new AccessDeniedException("You can't see this message.");
    }
    if(!$message->read && $message->to->id === $this->user->id) {
      $message->read = true;
      $this->orm->messages->persistAndFlush($message);
    }
    return $message;
  }
  
  /**
   * Get list of users
   * 
   * @return string[] id => publicname
   */
  public function usersList(): array {
    return $this->orm->users->findBy(
        ["id!=" => [$this->user->id, 0]]
    )->orderBy("id")
     ->fetchPairs("id", "publicname");
  }
  
  /**
   * Sends new message
   */
  public function send(array $data): int {
    $message = new MessageEntity();
    $this->orm->messages->attach($message);
    $message->subject = $data["subject"];
    $message->text = $data["text"];
    /** @var \Nexendrie\Orm\User $from */
    $from = $this->orm->users->getById($this->user->id);
    $message->from = $from;
    $message->from->lastActive = time();
    $message->to = $data["to"];
    $this->orm->messages->persistAndFlush($message);
    $notification = new Notification();
    $notification->title = "Nová zpráva na " . $this->notificator->getSiteName();
    $notification->body = "Přišla ti zpráva od {$message->from->publicname} s předmětem {$message->subject}.";
    $notification->tag = "messagePrivate";
    $notification->targetUrl = $this->linkGenerator->link("Front:Messages:view", ["id" => $message->id]);
    $this->notificator->createNotification($notification, $this->user->id);
    return $message->id;
  }
}
?>