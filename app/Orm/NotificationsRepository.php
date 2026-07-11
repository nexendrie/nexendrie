<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Notification>
 */
final class NotificationsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Notification::class];
    }

    /**
     * @return ICollection<Notification>
     */
    public function findByUser(User|int $user): ICollection
    {
        return $this->findBy(["user" => $user]);
    }
}
