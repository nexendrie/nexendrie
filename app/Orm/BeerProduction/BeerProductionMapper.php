<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class BeerProductionMapper extends \Nextras\Orm\Mapper\Mapper {
  function getTableName() {
    return "beer_production";
  }
}
?>