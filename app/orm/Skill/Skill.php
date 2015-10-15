<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Skill
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $price
 * @property-read string $priceT {virtual}
 */
class Skill extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
}
?>