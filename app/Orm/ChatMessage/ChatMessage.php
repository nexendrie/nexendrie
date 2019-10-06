<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * ChatMessage
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $message
 * @property int $created
 * @property string $createdAt {virtual}
 * @property User $user {m:1 User::$chatMessages}
 * @property Town|null $town {m:1 Town::$chatMessages}
 * @property Monastery|null $monastery {m:1 Monastery::$chatMessages}
 * @property Guild|null $guild {m:1 Guild::$chatMessages}
 * @property Order|null $order {m:1 Order::$chatMessages}
 */
final class ChatMessage extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
  
  public function onBeforeInsert(): void {
    parent::onBeforeInsert();
    $this->created = time();
  }
}
?>