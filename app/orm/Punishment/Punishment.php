<?php
namespace Nexendrie\Orm;

/**
 * Punishment
 *
 * @author Jakub Konečný
 * @property User $user {m:1 User::$punishments}
 * @property string $crime
 * @property int $imprisoned
 * @property int|NULL $released
 * @property int $numberOfShifts
 * @property int $count {default 0}
 * @property int|NULL $lastAction
 * @property-read string $imprisonedAt {virtual}
 * @property-read string $releasedAt {virtual}
 * @property-read int $nextShift {virtual}
 */
class Punishment extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterImprisonedAt() {
    return $this->localeModel->formatDateTime($this->imprisoned);
  }
  
  protected function getterReleasedAt() {
    if(is_int($this->released)) return $this->localeModel->formatDateTime($this->released);
    else return "";
  }
  
  protected function getterNextShift() {
    if($this->lastAction === NULL) return time();
    else return $this->lastAction + (60 * 60);
  }
}
?>