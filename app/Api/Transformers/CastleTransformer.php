<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class CastleTransformer extends BaseTransformer {
  protected array $fields = ["id", "name", "description", "createdAt", "owner", "level", "hp",];
  protected array $fieldsRename = ["createdAt" => "created",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Castle::class;
  }
}
?>