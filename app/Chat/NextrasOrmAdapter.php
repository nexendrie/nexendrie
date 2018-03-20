<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use Nexendrie\Orm\Model as ORM,
    Nexendrie\Orm\ChatMessage as ChatMessageEntity;

/**
 * NextrasOrmAdapter
 *
 * @author Jakub Konečný
 */
class NextrasOrmAdapter implements IDatabaseAdapter {
  /** @var ORM */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  public function __construct(ORM $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  public function getTexts(string $column, $value, int $limit): ChatMessagesCollection {
    $count = $this->orm->chatMessages->findBy([
      $column => $value,
    ])->countStored();
    $paginator = new \Nette\Utils\Paginator;
    $paginator->setItemCount($count);
    $paginator->setItemsPerPage($limit);
    $paginator->setPage($paginator->pageCount);
    $messages = $this->orm->chatMessages->findBy([
      $column => $value,
    ])->limitBy($paginator->length, $paginator->offset);
    $collection = new ChatMessagesCollection();
    foreach($messages as $message) {
      $character = new ChatCharacter($message->user->username, $message->user->publicname);
      $collection[] = new ChatMessage($message->id, $message->message, $message->whenS, $character);
    }
    return $collection;
  }
  
  public function getCharacters(string $column, $value): ChatCharactersCollection {
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->lastActive = time();
    $this->orm->users->persistAndFlush($user);
    $characters = $this->orm->users->findBy([
      $column => $value, "lastActive>=" => time()
    ]);
    $collection = new ChatCharactersCollection();
    foreach($characters as $character) {
      $collection[] = new ChatCharacter($character->username, $character->publicname);
    }
    return $collection;
  }
  
  public function addMessage(string $message, string $filterColumn, int $filterValue): void {
    $chatMessage = new ChatMessageEntity();
    $chatMessage->message = $message;
    $this->orm->chatMessages->attach($chatMessage);
    $chatMessage->user = $this->user->id;
    $chatMessage->user->lastActive = time();
    $chatMessage->{$filterColumn} = $filterValue;
    $this->orm->chatMessages->persistAndFlush($chatMessage);
  }
}
?>