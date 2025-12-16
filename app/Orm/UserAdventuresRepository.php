<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserAdventure|null getById(int $id)
 * @method UserAdventure|null getBy(array $conds)
 * @method ICollection|UserAdventure[] findBy(array $conds)
 * @method ICollection|UserAdventure[] findAll()
 */
final class UserAdventuresRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [UserAdventure::class];
    }


    /**
     * @return ICollection|UserAdventure[]
     */
    public function findByUser(User|int $user): ICollection
    {
        return $this->findBy(["user" => $user]);
    }

    /**
     * Find specified user's active adventure
     */
    public function getUserActiveAdventure(int $user): ?UserAdventure
    {
        return $this->getBy(["user" => $user, "progress<" => UserAdventure::PROGRESS_COMPLETED]);
    }

    public function getLastAdventure(User|int $user): ?UserAdventure
    {
        return $this->findBy(["user" => $user]) // @phpstan-ignore return.type
        ->orderBy("created", ICollection::DESC)
            ->limitBy(1)
            ->fetch();
    }

    /**
     * Get specified user's adventures from month
     *
     * @return ICollection|UserAdventure[]
     */
    public function findFromMonth(int $user, int $month = null, int $year = null): ICollection
    {
        $startOfMonthTS = (int) mktime(0, 0, 0, $month ?? (int) date("n"), 1, $year ?? (int) date("Y"));
        $date = new \DateTime();
        $date->setTimestamp($startOfMonthTS);
        $start = $date->getTimestamp();
        $date->modify("+ 1 month");
        $date->modify("- 1 second");
        $end = $date->getTimestamp();
        return $this->findBy(["user" => $user, "created>" => $start, "created<" => $end]);
    }

    /**
     * Get open adventures
     *
     * @return ICollection|UserAdventure[]
     */
    public function findOpenAdventures(): ICollection
    {
        $day = date("j");
        $month = date("n");
        $ts = mktime(0, 0, 0, (int) $month, (int) $day);
        return $this->findBy(["created<" => $ts, "progress<" => UserAdventure::PROGRESS_COMPLETED]);
    }
}
