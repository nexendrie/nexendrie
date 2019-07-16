<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class PollTransformer extends BaseTransformer {
  protected $fields = ["id", "question", "parsedAnswers", "author", "addedAt", ];
  protected $fieldsRename = ["parsedAnswers" => "answers", "addedAt" => "added", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Poll::class;
  }
}
?>