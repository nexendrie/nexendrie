<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Mount
 *
 * @author Jakub Konečný
 * @property string $name
 * @property int $gender {enum self::GENDER_*}
 * @property MountType $type {m:1 MountType::$mounts}
 * @property User $owner {m:1 User::$mounts}
 * @property int $price
 * @property bool $onMarket {default 0}
 * @property int $birth
 * @property-read string $birthAt {virtual}
 */
class Mount extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  const GENDER_MALE = 1;
  const GENDER_FEMALE = 2;
  const GENDER_YOUNG = 3;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
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