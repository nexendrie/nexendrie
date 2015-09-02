<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

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
 */
class Poll extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function getterAddedAt() {
    return $this->localeModel->formatDateTime($this->added);
  }
  
  function getterParsedAnswers() {
    return explode("\n", $this->answers);
  }
}
?>