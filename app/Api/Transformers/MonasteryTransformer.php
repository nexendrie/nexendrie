<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class MonasteryTransformer extends BaseTransformer {
  protected $fields = ["id", "name", "leader", "town", "createdAt", "level", "hp", "members", ];
  protected $fieldsRename = ["createdAt" => "created", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Monastery::class;
  }

  public function getCollectionName(): string {
    return "monasteries";
  }
}
?>