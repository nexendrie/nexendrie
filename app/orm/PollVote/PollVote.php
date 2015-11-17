<?php
namespace Nexendrie\Orm;

/**
 * PollVote
 *
 * @author Jakub Konečný
 * @property Poll $poll {m:1 Poll::$votes} {primary}
 * @property User $user {m:1 User} {primary}
 * @property int $answer
 * @property int $voted
 * @property-read string $votedAt {virtual}
 */
class PollVote extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterAddedAt() {
    return $this->localeModel->formatDateTime($this->added);
  }
}
?>