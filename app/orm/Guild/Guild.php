<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Guild
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $level {default 1}
 * @property int $founded
 * @property Town $town {m:1 Town::$guilds}
 * @property int $money
 * @property OneHasMany|User[] $members {1:m User::$guild}
 */
class Guild extends \Nextras\Orm\Entity\Entity {
  protected function setterLevel($value) {
    if($value < 1) return 1;
    elseif($value > 9) return 9;
    else return $value;
  }
}
?>