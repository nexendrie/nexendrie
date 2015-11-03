<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * UserAdventure
 *
 * @author Jakub Konečný
 * @property User $user {m:1 User::$adventures}
 * @property Adventure $adventure {m:1 Adventure::$userAdventures}
 * @property Mount $mount {m:1 Mount::$adventures}
 * @property int $started
 * @property int $progress {default 0}
 * @property int $reward {default 0}
 * @property int $loot {default 0}
 */
class UserAdventure extends Entity {
  
}
?>