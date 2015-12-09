<?php
namespace Nexendrie\Orm;

/**
 * House
 *
 * @author Jakub Konečný
 * @property User $owner {1:1 User::$house}
 * @property int $luxuryLevel {default 1}
 * @property int $breweryLevel {default 0}
 * @property int $hp {default 100}
 */
class House extends \Nextras\Orm\Entity\Entity {
  const MAX_LEVEL = 5;
  
  protected function setterLuxuryLevel($value) {
    if($value < 1) return 1;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
  protected function setterBreweryLevel($value) {
    if($value < 1) return 1;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
  protected function setterHp($value) {
    if($value < 1) return 1;
    elseif($value > 100) return 100;
    else return $value;
  }
}
?>