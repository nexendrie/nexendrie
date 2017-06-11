<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * OrderRank
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property int $adventureBonus
 * @property int $orderFee
 * @property OneHasMany|User[] $people {1:m User::$orderRank}
 */
class OrderRank extends \Nextras\Orm\Entity\Entity {
  protected function setterIncomeBonus(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 99) {
      return 99;
    }
    return $value;
  }
  
  protected function setterGuildFee(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 999) {
      return 999;
    }
    return $value;
  }
}
?>