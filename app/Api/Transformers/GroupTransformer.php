<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class GroupTransformer extends BaseTransformer {
  protected array $fields = ["id", "name", "singleName", "femaleName", "level", "path", "members",];
  protected array $fieldsRename = ["singleName" => "maleTitle", "femaleName" => "femaleTitle",];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Group::class;
  }
}
?>