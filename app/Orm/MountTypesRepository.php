<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
final class MountTypesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [MountType::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?MountType {
    return $this->getBy(["id" => $id]);
  }
}
?>