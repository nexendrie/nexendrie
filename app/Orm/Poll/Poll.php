<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Poll
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $question
 * @property string $answers
 * @property-read array $parsedAnswers {virtual}
 * @property User $author {m:1 User::$polls}
 * @property int $added
 * @property-read string $addedAt {virtual}
 * @property bool $locked {default false}
 * @property OneHasMany|PollVote[] $votes {1:m PollVote::$poll}
 * 
 */
final class Poll extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterAddedAt(): string {
    return $this->localeModel->formatDateTime($this->added);
  }
  
  protected function getterParsedAnswers(): array {
    return explode("\n", $this->answers);
  }
  
  public function onBeforeInsert(): void {
    parent::onBeforeInsert();
    $this->added = time();
  }
}
?>