<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * OrderFeesRepository
 *
 * @author Jakub Konečný
 * @method OrderFee|null getById(int $id)
 * @method OrderFee|null getBy(array $conds)
 * @method ICollection|OrderFee[] findBy(array $conds)
 * @method ICollection|OrderFee[] findAll()
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
