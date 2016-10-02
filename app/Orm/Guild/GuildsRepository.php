<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class GuildsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Guild::class];
  }
  
  /**
   * @param int $id
   * @return Guild|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param int $name
   * @return Guild|NULL
   */
  function getByName(int $name) {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|Guild[]
   */
  function findByTown($town): ICollection {
    return $this->findBy(["town" => $town]);
  }
}
?>