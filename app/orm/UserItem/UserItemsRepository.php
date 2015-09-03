<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserItem|NULL getById($id)
 * @method UserItem|NULL getByUserAndItem($user,$item)
 * @method ICollection|UserItem[] findByUser($user)
 * @method ICollection|UserItem[] findByItem($item)
 */
class UserItemsRepository extends Repository {
  
}
?>