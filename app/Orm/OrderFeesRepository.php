<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * OrderFeesRepository
 *
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<OrderFee>
 */
final class OrderFeesRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [OrderFee::class];
    }

    public function getByUserAndOrder(User|int $user, Order|int $order): ?OrderFee
    {
        return $this->getBy([
            "user" => $user, "order" => $order,
        ]);
    }
}
