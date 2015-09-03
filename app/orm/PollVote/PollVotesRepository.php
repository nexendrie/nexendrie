<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method PollVote|NULL getById($id)
 * @method ICollection|PollVote[] findByPoll($poll)
 * @method ICollection|PollVote[] findByUser($user)
 * @method PollVote|NULL getByPollAndUser($poll,$user)
 */
class PollVotesRepository extends Repository {
  
}
?>