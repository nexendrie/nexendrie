<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Mount
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $gender {enum self::GENDER_*} {default self::GENDER_YOUNG}
 * @property MountType $type {m:1 MountType::$mounts}
 * @property User $owner {m:1 User::$mounts}
 * @property int $price
 * @property bool $onMarket {default false}
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
 * @property-read int $damageTrainingCost {virtual}
 * @property-read int $armorTrainingCost {virtual}
 * @property-read string $damageTrainingCostT {virtual}
 * @property-read string $armorTrainingCostT {virtual}
 * @property-read string $typeGenderName {virtual}
 */
class Mount extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Model\Events */
  protected $eventsModel;
  
  const GENDER_MALE = "male";
  const GENDER_FEMALE = "female";
  const GENDER_YOUNG = "young";
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function injectEventsModel(\Nexendrie\Model\Events $eventsModel) {
    $this->eventsModel = $eventsModel;
  }
  
  /**
   * @return string[]
   */
  static function getGenders(): array {
    return [
      self::GENDER_MALE => "hřebec",
      self::GENDER_FEMALE => "klisna",
      self::GENDER_YOUNG => "mládě"
    ];
  }
  
  protected function setterHp(int $value): int {
    if($value > 100) {
      return 100;
    } elseif($value < 0) {
      return 0;
    }
    return $value;
  }
  
  protected function setterDamage(int $value): int {
    if($value > $this->maxDamage) {
      return $this->maxDamage;
    } elseif($value < $this->baseDamage) {
      return $this->baseDamage;
    }
    return $value;
  }
  
  protected function setterArmor(int $value): int {
    if($value > $this->maxArmor) {
      return $this->maxArmor;
    } elseif($value < $this->baseArmor) {
      return $this->baseArmor;
    }
    return $value;
  }
  
  protected function getterGenderCZ(): string {
    return self::getGenders()[$this->gender];
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterBirthAt(): string {
    return $this->localeModel->formatDateTime($this->birth);
  }
  
  protected function getterBaseDamage() {
    return $this->type->damage;
  }
  
  protected function getterBaseArmor() {
    return $this->type->armor;
  }
  
  protected function getterMaxDamage() {
    return ($this->type->damage * 2) + 1;
  }
  
  protected function getterMaxArmor() {
    return ($this->type->armor * 2) + 1;
  }
  
  protected function getterDamageTrainingCost() {
    if($this->damage >= $this->maxDamage) {
      return 0;
    }
    $basePrice = ($this->damage - $this->baseDamage + 1) * 30;
    $basePrice -= $this->eventsModel->calculateTrainingDiscount($basePrice);
    return $basePrice;
  }
  
  protected function getterArmorTrainingCost() {
    if($this->armor >= $this->maxArmor) {
      return 0;
    }
    $basePrice = ($this->armor - $this->baseArmor + 1) * 30;
    $basePrice -= $this->eventsModel->calculateTrainingDiscount($basePrice);
    return $basePrice;
  }
  
  protected function getterDamageTrainingCostT() {
    return $this->localeModel->money($this->damageTrainingCost);
  }
  
  protected function getterArmorTrainingCostT() {
    return $this->localeModel->money($this->armorTrainingCost);
  }
  
  protected function getterTypeGenderName() {
    return $this->type->{$this->gender . "Name"};
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    if(!$this->price) {
      $this->price = $this->type->price;
    }
    if(!$this->damage) {
      $this->damage = $this->type->damage;
    }
    if(!$this->armor) {
      $this->armor = $this->type->armor;
    }
    $this->birth = time();
    if($this->owner->id === 0) {
      $this->onMarket = true;
    }
  }
}
?>