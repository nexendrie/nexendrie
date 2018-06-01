<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class ElectionResultsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [ElectionResult::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?ElectionResult {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|ElectionResult[]
   */
  public function findByTownAndYearAndMonth($town, int $year, int $month): ICollection {
    return $this->findBy(["town" => $town, "year" => $year, "month" => $month]);
  }
}
?>