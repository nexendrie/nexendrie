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
 * @property int $created
 * @property int $updated
 * @property-read string $effect {virtual}
 */
final class Meal extends BaseEntity {
  private \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterEffect(): string {
    $word = ($this->life < 0) ? "ubere" : "přidá";
    return $word . " " . $this->localeModel->hitpoints($this->life);
  }
}
?>