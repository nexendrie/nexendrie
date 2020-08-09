<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class GuildsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Guild::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Guild {
    return $this->getBy(["id" => $id]);
  }
  
  public function getByName(string $name): ?Guild {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|Guild[]
   */
  public function findByTown($town): ICollection {
    return $this->findBy(["town" => $town]);
  }
  
  /**
   * @param Skill|int $skill
   * @return ICollection|Guild[]
   */
  public function findBySkill($skill): ICollection {
    return $this->findBy(["skill" => $skill]);
  }
}
?>