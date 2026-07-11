<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<AdventureNpc>
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
     * @return ICollection<AdventureNpc>
     */
    public function findByAdventure(int $adventure): ICollection
    {
        return $this->findBy(["adventure" => $adventure])->orderBy("order");
    }
}
