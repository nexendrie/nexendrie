<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class ArticleTransformer extends BaseTransformer {
  protected array $fields = ["id", "title", "text", "author", "category", "createdAt", "allowedComments",];
  protected array $fieldsRename = ["createdAt" => "created",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Article::class;
  }
}
?>