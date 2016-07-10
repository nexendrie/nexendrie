<?php
namespace Nexendrie\Orm;

/**
 * PollVote
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property Poll $poll {m:1 Poll::$votes}
 * @property User $user {m:1 User::$pollVotes}
 * @property int $answer
 * @property int $voted
 * @property-read string $votedAt {virtual}
 */
class PollVote extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterVotedAt() {
    return $this->localeModel->formatDateTime($this->voted);
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->voted = time();
  }
}
?>