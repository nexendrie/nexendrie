<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Meal
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $message
 * @property int $price
 * @property-read string $priceT {virtual}
 */
class Meal extends Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
}
?>