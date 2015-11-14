<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Mount
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $gender {enum self::GENDER_*} {default self::GENDER_YOUNG}
 * @property MountType $type {m:1 MountType::$mounts}
 * @property User $owner {m:1 User::$mounts}
 * @property int $price
 * @property bool $onMarket {default 0}
 * @property int $birth
 * @property int $hp {default 100}
 * @property int $damage {default 0}
 * @property int $armor {default 0}
 * @property OneHasMany|UserAdventure[] $adventures {1:m UserAdventure::$mount}
 * @property-read string $genderCZ {virtual}
 * @property-read string $priceT {virtual}
 * @property-read string $birthAt {virtual}
 * @property-read int $baseDamage {virtual}
 * @property-read int $baseArmor {virtual}
 * @property-read int $maxDamage {virtual}
 * @property-read int $maxArmor {virtual}
 */
class Mount extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  const GENDER_MALE = "male";
  const GENDER_FEMALE = "female";
  const GENDER_YOUNG = "young";
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  /**
   * @return string[]
   */
  static function getGenders() {
    return array(
      self::GENDER_MALE => "hřebec",
      self::GENDER_FEMALE => "klisna",
      self::GENDER_YOUNG => "mládě"
    );
  }
  
  protected function setterHp($value) {
    if($value > 100) return 100;
    elseif($value < 0) return 0;
    else return $value;
  }
  
  protected function setterDamage($value) {
    if($value > $this->maxDamage) return $this->maxDamage;
    elseif($value < $this->baseDamage) return $this->baseDamage;
    else return $value;
  }
  
  protected function setterArmor($value) {
    if($value > $this->maxArmor) return $this->maxArmor;
    elseif($value < $this->baseArmor) return $this->baseArmor;
    else return $value;
  }
  
  protected function getterGenderCZ() {
    return self::getGenders()[$this->gender];
  }
  
  protected function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterBirthAt() {
    return $this->localeModel->formatDateTime($this->birth);
  }
  
  protected function getterBaseDamage() {
    return $this->type->damage;
  }
  
  protected function getterBaseArmor() {
    return $this->type->armor;
  }
  
  protected function getterMaxDamage() {
    return ($this->type->armor * 2) + 1;
  }
  
  protected function getterMaxArmor() {
    return ($this->type->armor * 2) + 1;
  }
  
  function dummy() {
    return new MountDummy($this);
  }
  
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>