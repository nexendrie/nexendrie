<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * DepositsRepository
 *
 * @author Jakub Konečný
 * @method Deposit|null getById(int $id)
 * @method Deposit|null getBy(array $conds)
 * @method ICollection|Deposit[] findBy(array $conds)
 * @method ICollection|Deposit[] findAll()
 */
final class DepositsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Deposit::class];
    }

    /**
     * @return ICollection|Deposit[]
     */
    public function findByUser(User|int $user): ICollection
    {
        return $this->findBy(["user" => $user]);
    }

    /**
     * Get specified user's active loan
     */
    public function getActiveDeposit(int $user): ?Deposit
    {
        return $this->getBy(["user" => $user, "closed" => false]);
    }

    /**
     * Get deposit accounts due this month
     *
     * @return ICollection|Deposit[]
     */
    public function findDueThisMonth(int $user): ICollection
    {
        $month = (int) date("n");
        $year = (int) date("Y");
        $startOfMonthTS = (int) mktime(0, 0, 0, $month, 1, $year);
        $date = new \DateTime();
        $date->setTimestamp($startOfMonthTS);
        $start = $date->getTimestamp();
        $date->modify("+ 1 month");
        $date->modify("- 1 second");
        $end = $date->getTimestamp();
        return $this->findBy(["user" => $user, "term>" => $start, "term<" => $end]);
    }
}
