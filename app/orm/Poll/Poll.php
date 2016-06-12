<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Poll
 *
 * @author Jakub Konečný
 * @property string $question
 * @property string $answers
 * @property-read array $parsedAnswers {virtual}
 * @property User $author {m:1 User}
 * @property int $added
 * @property-read string $addedAt {virtual}
 * @property bool $locked {default 0}
 * @property OneHasMany|PollVote[] $votes {1:m PollVote::$poll}
 * 
 */
class Poll extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterAddedAt() {
    return $this->localeModel->formatDateTime($this->added);
  }
  
  protected function getterParsedAnswers() {
    return explode("\n", $this->answers);
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->added = time();
  }
}
?>