<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class HousesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [House::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?House {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $owner
   */
  public function getByOwner($owner): ?House {
    return $this->getBy(["owner" => $owner]);
  }
  
  /**
   * Get houses owned by users
   * 
   * @return ICollection|House[]
   */
  public function findOwnedHouses(): ICollection {
    return $this->findBy(["this->owner->id>" => 0]);
  }
}
?>