<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * ContentReportsRepository
 *
 * @author Jakub Konečný
 */
final class ContentReportsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [ContentReport::class];
  }

  /**
   * @param int $id
   * @return ContentReport|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
  }
}
?>