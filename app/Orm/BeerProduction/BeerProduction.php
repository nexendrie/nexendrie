<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * BeerProduction
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$beerProduction}
 * @property House $house {m:1 House::$beerProduction}
 * @property int $amount
 * @property int $price
 * @property int $created
 */
final class BeerProduction extends \Nextras\Orm\Entity\Entity {
  public function onBeforeInsert(): void {
    parent::onBeforeInsert();
    $this->created = time();
  }
}
?>