<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class PollVotesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [PollVote::class];
  }
  
  /**
   * @param int $id
   * @return PollVote|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param Poll|int $poll
   * @param User|int $user
   * @return PollVote|NULL
   */
  function getByPollAndUser($poll, $user) {
    return $this->getBy(array("poll" => $poll, "user" => $user));
  }
  
  /**
   * @param Poll|int $poll
   * @return ICollection|PollVote[]
   */
  function findByPoll($poll) {
    return $this->findBy(array("poll" => $poll));
  }
  
  /**
   * @param User|int $user
   * @return ICollection|PollVote[]
   */
  function findByUser($user) {
    return $this->findBy(array("user" => $user));
  }
}
?>