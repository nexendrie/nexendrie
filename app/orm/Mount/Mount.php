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
 * @property OneHasMany|UserAdventure[] $adventures {1:m UserAdventure::$mount}
 * @property-read string $genderCZ {virtual}
 * @property-read string $priceT {virtual}
 * @property-read string $birthAt {virtual}
 * @property-read int $damage {virtual}
 * @property-read int $armor {virtual}
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
  
  protected function getterGenderCZ() {
    return self::getGenders()[$this->gender];
  }
  
  protected function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterBirthAt() {
    return $this->localeModel->formatDateTime($this->birth);
  }
  
  protected function getterDamage() {
    return $this->type->damage;
  }
  
  protected function getterArmor() {
    return $this->type->armor;
  }
  
  function dummy() {
    return new MountDummy($this);
  }
  
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>