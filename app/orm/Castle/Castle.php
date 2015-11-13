<?php
namespace Nexendrie\Orm;

/**
 * Castle
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $founded
 * @property-read string $foundedAt {virtual}
 * @property User $owner {1:1 User::$castle}
 * @property int $level {default 1}
 * @property int $hp {default 100}
 * @property-read int $taxesBonusIncome {virtual}
 */
class Castle extends \Nextras\Orm\Entity\Entity {
  const MAX_LEVEL = 5;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterLevel($value) {
    if($value < 1) return 1;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
   protected function setterHp($value) {
    if($value < 1) return 1;
    elseif($value > 100) return 100;
    else return $value;
  }
  
  protected function getterFoundedAt() {
    return $this->localeModel->formatDate($this->founded);
  }
  
  protected function getterTaxesBonusIncome() {
    if($this->hp <= 30) return 0;
    else return $this->level * 30;
  }
}
?>