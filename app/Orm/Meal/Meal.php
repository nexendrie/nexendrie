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
final class Meal extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterEffect(): string {
    $word = ($this->life < 0) ? "ubere" : "přidá";
    return $word . " " . $this->localeModel->hitpoints($this->life);
  }
}
?>