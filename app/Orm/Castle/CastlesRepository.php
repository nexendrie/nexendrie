<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class CastlesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Castle::class];
  }
  
  /**
   * @param int $id
   * @return Castle|NULL
   */
  function getById($id): ?Castle {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $owner
   * @return Castle|NULL
   */
  function getByOwner($owner): ?Castle {
    return $this->getBy(["owner" => $owner]);
  }
  
  /**
   * @param string $name
   * @return Castle|NULL
   */
  function getByName(string $name): ?Castle {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * Get castles owned by users
   * 
   * @return ICollection|Castle[]
   */
  function findOwnedCastles(): ICollection {
    return $this->findBy(["this->owner->id>" => 0]);
  }
}
?>