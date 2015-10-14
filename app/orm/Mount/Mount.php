<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Mount
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $gender {enum self::GENDER_*}
 * @property MountType $type {m:1 MountType::$mounts}
 * @property User $owner {m:1 User::$mounts}
 * @property int $price
 * @property bool $onMarket {default 0}
 * @property int $birth
 * @property-read string $genderCZ {virtual}
 * @property-read string $priceT {virtual}
 * @property-read string $birthAt {virtual}
 */
class Mount extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  const GENDER_MALE = "male";
  const GENDER_FEMALE = "female";
  const GENDER_YOUNG = "young";
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function getterGenderCZ() {
    switch($this->gender) {
      case self::GENDER_MALE:
        return "hřebec";
        break;
      case self::GENDER_FEMALE:
        return "klisna";
        break;
      case self::GENDER_YOUNG:
        return "mládě";
        break;
    }
  }
  
  function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
  
  function getterBirthAt() {
    return $this->localeModel->formatDateTime($this->birth);
  }
  
  function dummy() {
    return new MountDummy($this);
  }
  
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>