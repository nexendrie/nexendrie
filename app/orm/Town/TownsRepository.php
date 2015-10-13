<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Town|NULL getById($id)
 * @method ICollection|Town[] findByOwner($owner)
 */
class TownsRepository extends Repository {
  
}
?>