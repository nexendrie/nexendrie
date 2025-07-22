<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
final class BeerProductionMapper extends \Nextras\Orm\Mapper\Dbal\DbalMapper {
  public function getTableName(): string {
    return "beer_production";
  }
}
?>