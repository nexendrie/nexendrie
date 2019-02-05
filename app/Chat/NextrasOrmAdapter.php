<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\ChatMessage as ChatMessageEntity;
use HeroesofAbenez\Chat\IDatabaseAdapter;
use HeroesofAbenez\Chat\ChatMessagesCollection;
use HeroesofAbenez\Chat\ChatMessage;
use HeroesofAbenez\Chat\ChatCharactersCollection;
use HeroesofAbenez\Chat\ChatCharacter;

/**
 * NextrasOrmAdapter
 *
 * @author Jakub Konečný
 */
final class NextrasOrmAdapter implements IDatabaseAdapter {
  /** @var ORM */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  public function __construct(ORM $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }

  /**
   * @param mixed $value
   */
  public function getTexts(string $column, $value, int $limit): ChatMessagesCollection {
    $count = $this->orm->chatMessages->findBy([
      $column => $value,
    ])->countStored();
    $paginator = new \Nette\Utils\Paginator();
    $paginator->setItemCount($count);
    $paginator->setItemsPerPage($limit);
    $paginator->setPage($paginator->pageCount);
    $messages = $this->orm->chatMessages->findBy([
      $column => $value,
    ])->limitBy($paginator->length, $paginator->offset);
    $collection = new ChatMessagesCollection();
    foreach($messages as $message) {
      $character = new ChatCharacter($message->user->publicname, $message->user->publicname);
      $collection[] = new ChatMessage($message->id, $message->message, $message->whenS, $character);
    }
    return $collection;
  }

  /**
   * @param mixed $value
   */
  public function getCharacters(string $column, $value): ChatCharactersCollection {
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->lastActive = time();
    $this->orm->users->persistAndFlush($user);
    $characters = $this->orm->users->findBy([
      $column => $value, "lastActive>=" => time() - 60 * 5
    ]);
    $collection = new ChatCharactersCollection();
    foreach($characters as $character) {
      $collection[] = new ChatCharacter($character->publicname, $character->publicname);
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