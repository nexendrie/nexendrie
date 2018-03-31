<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany,
    Nexendrie\Utils\Numbers;

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
  protected function setterIncomeBonus(int $value): int {
    return Numbers::range($value, 0, 99);
  }
  
  protected function setterGuildFee(int $value): int {
    return Numbers::range($value, 0, 999);
  }
}
?>