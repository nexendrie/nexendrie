<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Item>
 */
final class ItemsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Item::class];
    }

    /**
     * @return ICollection<Item>
     */
    public function findWeapons(): ICollection
    {
        return $this->findBy(["type" => Item::TYPE_WEAPON]);
    }

    /**
     * @return ICollection<Item>
     */
    public function findArmors(): ICollection
    {
        return $this->findBy(["type" => Item::TYPE_ARMOR]);
    }

    /**
     * @return ICollection<Item>
     */
    public function findHelmets(): ICollection
    {
        return $this->findBy(["type" => Item::TYPE_HELMET]);
    }

    /**
     * @return ICollection<Item>
     */
    public function findByShop(Shop|int $shop): ICollection
    {
        return $this->findBy(["shop" => $shop]);
    }
}
