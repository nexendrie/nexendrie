<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class GuildTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "description", "level", "foundedAt", "town", "skill", "members",];
  protected $fieldsRename = ["foundedAt" => "founded",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Guild::class;
  }
}
?>