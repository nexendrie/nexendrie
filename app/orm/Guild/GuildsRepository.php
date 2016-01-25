<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Guild|NULL getById($id)
 * @method ICollection|Guild[] findByTown($town)
 * @method Guild|NULL getByName($name)
 */
class GuildsRepository extends \Nextras\Orm\Repository\Repository {
  
}
?>