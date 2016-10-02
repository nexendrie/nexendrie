<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Meal
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $message
 * @property int $price
 * @property int $life {default 0}
 * @property-read string $priceT {virtual}
 * @property-read string $effect {virtual}
 */
class Meal extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterEffect(): string {
    if($this->life < 0) $word = "ubere";
    else $word = "přidá";
    return $word . " " . $this->localeModel->hitpoints($this->life);
  }
}
?>