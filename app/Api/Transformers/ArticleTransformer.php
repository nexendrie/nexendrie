<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class ArticleTransformer extends BaseTransformer {
  protected $fields = ["id", "title", "text", "author", "category", "addedAt", "allowedComments", ];
  protected $fieldsRename = ["addedAt" => "added", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Article::class;
  }
}
?>