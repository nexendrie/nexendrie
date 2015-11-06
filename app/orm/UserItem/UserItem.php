<?php
namespace Nexendrie\Orm;

/**
 * UserItem
 *
 * @author Jakub Konečný
 * @property Item $item {m:1 Item}
 * @property User $user {m:1 User::$items}
 * @property int $amount {default 1}
 * @property bool $worn {default 0}
 */
class UserItem extends \Nextras\Orm\Entity\Entity {
  
}
?>