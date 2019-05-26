<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class CommentTransformer extends BaseTransformer {
  protected $fields = ["id", "title", "text", "article", "author", "addedAt",];
  protected $fieldsRename = ["addedAt" => "added",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Comment::class;
  }
}
?>