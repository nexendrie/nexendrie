<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<JobMessage>
 */
final class JobMessagesRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [JobMessage::class];
    }

    /**
     * @return ICollection<JobMessage>
     */
    public function findByJobAndSuccess(Job|int $job, bool $success): ICollection
    {
        return $this->findBy(["job" => $job, "success" => $success]);
    }
}
