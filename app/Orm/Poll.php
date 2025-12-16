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
 * @property int $created
 * @property int $updated
 * @property-read string $createdAt {virtual}
 * @property bool $locked {default false}
 * @property OneHasMany|PollVote[] $votes {1:m PollVote::$poll}
 *
 */
final class Poll extends BaseEntity
{
    private \Nexendrie\Model\Locale $localeModel;

    public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void
    {
        $this->localeModel = $localeModel;
    }

    protected function getterCreatedAt(): string
    {
        return $this->localeModel->formatDateTime($this->created);
    }

    protected function getterParsedAnswers(): array
    {
        return explode("\n", $this->answers);
    }
}
