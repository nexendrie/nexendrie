<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method PollVote|NULL getById($id)
 * @method ICollection|PollVote[] findByPoll($poll)
 * @method ICollection|PollVote[] findByUser($user)
 * @method PollVote|NULL getByPollAndUser($poll,$user)
 */
class PollVotesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [PollVote::class];
  }
}
?>