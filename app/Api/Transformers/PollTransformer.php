<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class PollTransformer extends BaseTransformer
{
    protected array $fields = ["id", "question", "parsedAnswers", "author", "createdAt",];
    protected array $fieldsRename = ["parsedAnswers" => "answers", "createdAt" => "created",];

    public function getEntityClassName(): string
    {
        return \Nexendrie\Orm\Poll::class;
    }
}
