<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Order>
 */
final class OrdersRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Order::class];
    }

    public function getByName(string $name): ?Order
    {
        return $this->getBy(["name" => $name]);
    }
}
