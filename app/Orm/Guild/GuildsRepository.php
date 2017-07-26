<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class GuildsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Guild::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Guild {
    return $this->getBy(["id" => $id]);
  }
  
  public function getByName(int $name): ?Guild {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|Guild[]
   */
  public function findByTown($town): ICollection {
    return $this->findBy(["town" => $town]);
  }
}
?>