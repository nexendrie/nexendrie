<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class TownsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Town::class];
  }
  
  /**
   * @param int $id
   * @return Town|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param string $name
   * @return Town|NULL
   */
  function getByName(string $name) {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * @param User|int $owner
   * @return ICollection|Town[]
   */
  function findByOwner($owner): ICollection {
    return $this->findBy(["owner" => $owner]);
  }
  
  /**
   * Get towns on sale
   * 
   * @return ICollection|Town[]
   */
  function findOnMarket(): ICollection {
    return $this->findBy(["onMarket" => true]);
  }
}
?>