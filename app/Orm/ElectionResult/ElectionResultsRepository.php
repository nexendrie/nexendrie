<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class ElectionResultsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [ElectionResult::class];
  }
  
  /**
   * @param int $id
   * @return ElectionResult|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Town|int $town
   * @param int $year
   * @param int $month
   * @return ICollection|ElectionResult[]
   */
  function findByTownAndYearAndMonth($town,$year,$month) {
    return $this->findBy(["town" => $town, "year" => $year, "month" => $month]);
  }
}
?>