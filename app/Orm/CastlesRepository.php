<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Castle|null getById(int $id)
 * @method Castle|null getBy(array $conds)
 * @method ICollection|Castle[] findBy(array $conds)
 * @method ICollection|Castle[] findAll()
 */
final class CastlesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Castle::class];
  }
  
  /**
   * @param User|int $owner
   */
  public function getByOwner($owner): ?Castle {
    return $this->getBy(["owner" => $owner]);
  }
  
  public function getByName(string $name): ?Castle {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * Get castles owned by users
   * 
   * @return ICollection|Castle[]
   */
  public function findOwnedCastles(): ICollection {
    return $this->findBy(["owner->id>" => 0]);
  }
}
?>