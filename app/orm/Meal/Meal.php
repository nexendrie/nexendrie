<?php
namespace Nexendrie\Orm;

/**
 * Meal
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $message
 * @property int $price
 * @property int $life {default 0}
 * @property-read string $priceT {virtual}
 */
class Meal extends \Nextras\Orm\Entity\Entity {
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