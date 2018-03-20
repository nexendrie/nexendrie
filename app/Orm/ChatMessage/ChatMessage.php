<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * ChatMessage
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $message
 * @property int $when
 * @property string $whenS {virtual}
 * @property User $user {m:1 User::$chatMessages}
 * @property Town|NULL $town {m:1 Town::$chatMessages}
 * @property Monastery|NULL $monastery {m:1 Monastery::$chatMessages}
 * @property Guild|NULL $guild {m:1 Guild::$chatMessages}
 * @property Order|NULL $order {m:1 Order::$chatMessages}
 */
class ChatMessage extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterWhenS(): string {
    return $this->localeModel->formatDateTime($this->when);
  }
  
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->when = time();
  }
}
?>