<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Mapper\Dbal\DbalMapper;

/**
 * @author Jakub Konečný
 * @extends DbalMapper<BeerProduction>
 */
final class BeerProductionMapper extends DbalMapper
{
    public function getTableName(): string
    {
        return "beer_production";
    }
}
