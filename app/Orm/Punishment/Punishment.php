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
 * @property int $imprisoned
 * @property int|null $released
 * @property int $numberOfShifts
 * @property int $count {default 0}
 * @property int|null $lastAction
 * @property-read string $imprisonedAt {virtual}
 * @property-read string $releasedAt {virtual}
 * @property-read int $nextShift {virtual}
 */
final class Punishment extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterImprisonedAt(): string {
    return $this->localeModel->formatDateTime($this->imprisoned);
  }
  
  protected function getterReleasedAt(): string {
    if(is_int($this->released)) {
      return $this->localeModel->formatDateTime($this->released);
    }
    return "";
  }
  
  protected function getterNextShift(): int {
    if(is_null($this->lastAction)) {
      return time();
    }
    return $this->lastAction + (60 * 60);
  }
  
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->imprisoned = time();
  }
}
?>