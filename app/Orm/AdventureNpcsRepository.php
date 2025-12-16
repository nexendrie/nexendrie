<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method AdventureNpc|null getById(int $id)
 * @method AdventureNpc|null getBy(array $conds)
 * @method ICollection|AdventureNpc[] findBy(array $conds)
 * @method ICollection|AdventureNpc[] findAll()
 */
final class AdventureNpcsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [AdventureNpc::class];
    }

    public function getByAdventureAndOrder(Adventure|int $adventure, Order|int $order): ?AdventureNpc
    {
        return $this->getBy(["adventure" => $adventure, "order" => $order]);
    }

    /**
     * @return ICollection|AdventureNpc[]
     */
    public function findByAdventure(int $adventure): ICollection
    {
        return $this->findBy(["adventure" => $adventure])->orderBy("order");
    }
}
