<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class GuildTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "description", "level", "createdAt", "town", "skill", "members", ];
  protected $fieldsRename = ["createdAt" => "created", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Guild::class;
  }
}
?>