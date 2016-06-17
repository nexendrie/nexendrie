<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Marriage|NULL getById($id)
 * @method ICollection|Marriage[] findByUser1($user1)
 * @method ICollection|Marriage[] findByUser2($user2)
 * @method Marriage|NULL getActiveMarriage($user)
 */
class MarriagesRepository extends \Nextras\Orm\Repository\Repository {
  
}
?>