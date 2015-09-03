<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * PollVote
 *
 * @author Jakub Konečný
 * @property Poll $poll {m:1 Poll::$votes}
 * @property User $user {m:1 User}
 * @property int $answer
 * @property int $voted
 * @property-read string $votedAt {virtual}
 */
class PollVote extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function getterAddedAt() {
    return $this->localeModel->formatDateTime($this->added);
  }
}
?>