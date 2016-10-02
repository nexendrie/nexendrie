<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * GuildRank
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property int $incomeBonus
 * @property int $guildFee
 * @property OneHasMany|User[] $people {1:m User::$guildRank}
 */
class GuildRank extends \Nextras\Orm\Entity\Entity {
  protected function setterIncomeBonus($value) {
    if($value < 0) return 0;
    elseif($value > 99) return 99;
    else return $value;
  }
  
  protected function setterGuildFee($value) {
    if($value < 0) return 0;
    elseif($value > 999) return 999;
    else return $value;
  }
}
?>