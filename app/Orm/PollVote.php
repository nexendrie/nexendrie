<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * PollVote
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property Poll $poll {m:1 Poll::$votes}
 * @property User $user {m:1 User::$pollVotes}
 * @property int $answer
 * @property int $created
 * @property-read string $createdAt {virtual}
 */
final class PollVote extends BaseEntity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
}
?>