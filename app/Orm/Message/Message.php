<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Message
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $subject
 * @property string $text
 * @property User $from {m:1 User::$sentMessages}
 * @property User $to {m:1 User::$receivedMessages}
 * @property int $sent
 * @property bool $read {default false}
 * @property-read string $sentAt {virtual}
 */
final class Message extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterSentAt(): string {
    return $this->localeModel->formatDateTime($this->sent);
  }
  
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->sent = time();
  }
}
?>