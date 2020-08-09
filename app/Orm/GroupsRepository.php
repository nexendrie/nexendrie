<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class GroupsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Group::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Group {
    return $this->getBy(["id" => $id]);
  }
  
  public function getByLevel(int $level): ?Group {
    return $this->getBy(["level" => $level]);
  }

  /**
   * @return int[]
   */
  public function getChurchGroupIds(): array {
    return $this->findBy([
      "path" => Group::PATH_CHURCH,
    ])->orderBy("level", ICollection::DESC)->fetchPairs(null, "id");
  }

  /**
   * @return int[]
   */
  public function getTowerGroupIds(): array {
    return $this->findBy([
      "path" => Group::PATH_TOWER,
    ])->orderBy("level", ICollection::DESC)->fetchPairs(null, "id");
  }

  /**
   * @return int[]
   */
  public function getCityGroupIds(): array {
    return $this->findBy([
      "path" => Group::PATH_CITY,
      "level>" => 0,
    ])->orderBy("level", ICollection::DESC)->fetchPairs(null, "id");
  }
}
?>