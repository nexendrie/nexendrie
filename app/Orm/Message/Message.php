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
 * @property int $created
 * @property bool $read {default false}
 * @property-read string $createdAt {virtual}
 */
final class Message extends BaseEntity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
}
?>