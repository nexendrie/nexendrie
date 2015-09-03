<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * UserItem
 *
 * @author Jakub Konečný
 * @property-read Item $item {m:1 Item}
 * @property-read User $user {m:1 User::$items}
 * @property-read int $amount {default 1}
 */
class UserItem extends Entity {
  
}
?>