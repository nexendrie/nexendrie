<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Poll>
 */
final class PollsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Poll::class];
    }

    /**
     * @return ICollection<Poll>
     */
    public function findByAuthor(User|int $author): ICollection
    {
        return $this->findBy(["author" => $author]);
    }
}
