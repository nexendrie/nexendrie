<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method MonasteryDonation|null getById(int $id)
 * @method MonasteryDonation|null getBy(array $conds)
 * @method ICollection|MonasteryDonation[] findBy(array $conds)
 * @method ICollection|MonasteryDonation[] findAll()
 */
final class MonasteryDonationsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [MonasteryDonation::class];
    }

    /**
     * @return ICollection|MonasteryDonation[]
     */
    public function findByUser(User|int $user): ICollection
    {
        return $this->findBy(["user" => $user]);
    }

    /**
     * @return ICollection|MonasteryDonation[]
     */
    public function findByMonastery(Monastery|int $monastery): ICollection
    {
        return $this->findBy(["monastery" => $monastery]);
    }

    /**
     * Get donations made this month by specified user
     *
     * @return ICollection|MonasteryDonation[]
     */
    public function findDonatedThisMonth(int $user): ICollection
    {
        $month = date("n");
        $year = date("Y");
        $startOfMonthTS = (int) mktime(0, 0, 0, (int) $month, 1, (int) $year);
        $date = new \DateTime();
        $date->setTimestamp($startOfMonthTS);
        $start = $date->getTimestamp();
        $date->modify("+ 1 month");
        $date->modify("- 1 second");
        $end = $date->getTimestamp();
        return $this->findBy(["user" => $user, "created>" => $start, "created<" => $end]);
    }
}
