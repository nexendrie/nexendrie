<?php
namespace Nexendrie\Orm;

/**
 * Message
 *
 * @author Jakub Konečný
 * @property string $subject
 * @property string $text
 * @property User $from {m:1 User::$sentMessages}
 * @property User $to {m:1 User::$receivedMessages}
 * @property int $sent
 * @property bool $read {default 0}
 * @property-read string $sentAt {virtual}
 */
class Message extends \Nextras\Orm\Entity\Entity {
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