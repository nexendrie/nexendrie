<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

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
 * @property-read string $imprisonedAt {virtual}
 * @property-read string $releasedAt {virtual}
 */
class Punishment extends Entity {
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
}
?>