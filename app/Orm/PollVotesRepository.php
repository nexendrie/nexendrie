<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<PollVote>
 */
final class PollVotesRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [PollVote::class];
    }

    public function getByPollAndUser(Poll|int $poll, User|int $user): ?PollVote
    {
        return $this->getBy(["poll" => $poll, "user" => $user]);
    }

    /**
     * @return ICollection<PollVote>
     */
    public function findByPoll(Poll|int $poll): ICollection
    {
        return $this->findBy(["poll" => $poll]);
    }

    /**
     * @return ICollection<PollVote>
     */
    public function findByUser(User|int $user): ICollection
    {
        return $this->findBy(["user" => $user]);
    }
}
