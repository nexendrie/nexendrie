<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method ElectionResult|NULL getById($id)
 * @method ICollection|ElectionResult[] findByTownAndYearAndMonth($town,$year,$month)
 */
class ElectionResultsRepository extends \Nextras\Orm\Repository\Repository {
  
}
?>