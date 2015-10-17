<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Message
 *
 * @author Jakub Konečný
 * @property string $subject
 * @property string $text
 * @property User $from {m:1 User::$sentMessages}
 * @property User $to {m:1 User::$receivedMessages}
 * @property int $sent
 * @property-read string $sentAt {virtual}
 */
class Message extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterSentAt() {
    return $this->localeModel->formatDateTime($this->sent);
  }
}
?>