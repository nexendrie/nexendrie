<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * UserItem
 *
 * @author Jakub Konečný
 * @property Item $item {m:1 Item}
 * @property User $user {m:1 User::$items}
 * @property int $amount {default 1}
 */
class UserItem extends Entity {
  
}
?>