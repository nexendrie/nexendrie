<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Punishment
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$punishments}
 * @property string $crime
 * @property int $created
 * @property int $updated
 * @property int|null $released
 * @property int $numberOfShifts
 * @property int $count {default 0}
 * @property int|null $lastAction
 * @property-read string $createdAt {virtual}
 * @property-read string $releasedAt {virtual}
 * @property-read int $nextShift {virtual}
 */
final class Punishment extends BaseEntity {
  private \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
  
  protected function getterReleasedAt(): string {
    if(is_int($this->released)) {
      return $this->localeModel->formatDateTime($this->released);
    }
    return "";
  }
  
  protected function getterNextShift(): int {
    if($this->lastAction === null) {
      return time();
    }
    return $this->lastAction + (60 * 60);
  }
}
?>