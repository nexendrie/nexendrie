<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class CommentTransformer extends BaseTransformer
{
    protected array $fields = ["id", "title", "text", "article", "author", "createdAt",];
    protected array $fieldsRename = ["createdAt" => "created",];

    public function getEntityClassName(): string
    {
        return \Nexendrie\Orm\Comment::class;
    }
}
