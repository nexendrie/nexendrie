<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method PollVote|null getById(int $id)
 * @method PollVote|null getBy(array $conds)
 * @method ICollection|PollVote[] findBy(array $conds)
 * @method ICollection|PollVote[] findAll()
 */
final class PollVotesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [PollVote::class];
  }
  
  /**
   * @param Poll|int $poll
   * @param User|int $user
   */
  public function getByPollAndUser($poll, $user): ?PollVote {
    return $this->getBy(["poll" => $poll, "user" => $user]);
  }
  
  /**
   * @param Poll|int $poll
   * @return ICollection|PollVote[]
   */
  public function findByPoll($poll): ICollection {
    return $this->findBy(["poll" => $poll]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|PollVote[]
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
}
?>